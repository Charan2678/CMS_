<?php

declare(strict_types=1);

namespace App\Modules\Result\services;

use Exception;
use PDO;

class ResultService
{
    // ─── 1. Timetable Scheduling ──────────────────────────────
    public function getTimetableForSection(int $sectionId, int $academicYearId): array
    {
        $stmt = db()->prepare('
            SELECT tt.*, sub.name AS subject_name, sub.code AS subject_code,
                   f.first_name AS faculty_first_name, f.last_name AS faculty_last_name,
                   r.name AS room_name
            FROM timetable tt
            JOIN subjects sub ON sub.id = tt.subject_id
            JOIN faculty f ON f.id = tt.faculty_id
            LEFT JOIN rooms r ON r.id = tt.room_id
            WHERE tt.section_id = :section_id AND tt.academic_year_id = :ay_id
            ORDER BY tt.day_of_week ASC, tt.period_number ASC
        ');
        $stmt->execute([':section_id' => $sectionId, ':ay_id' => $academicYearId]);
        $rows = $stmt->fetchAll() ?: [];

        $grid = [];
        foreach ($rows as $r) {
            $grid[$r['day_of_week']][$r['period_number']] = $r;
        }
        return $grid;
    }

    public function saveTimetableSlot(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO timetable (
                section_id, academic_year_id, day_of_week, period_number,
                subject_id, faculty_id, room_id, start_time, end_time, created_at
            ) VALUES (
                :section_id, :academic_year_id, :day_of_week, :period_number,
                :subject_id, :faculty_id, :room_id, :start_time, :end_time, NOW()
            )
            ON DUPLICATE KEY UPDATE
                subject_id = VALUES(subject_id),
                faculty_id = VALUES(faculty_id),
                room_id    = VALUES(room_id),
                start_time = VALUES(start_time),
                end_time   = VALUES(end_time)
        ');

        return $stmt->execute([
            ':section_id'       => (int) $data['section_id'],
            ':academic_year_id' => (int) $data['academic_year_id'],
            ':day_of_week'      => strtolower($data['day_of_week']),
            ':period_number'    => (int) $data['period_number'],
            ':subject_id'       => (int) $data['subject_id'],
            ':faculty_id'       => (int) $data['faculty_id'],
            ':room_id'          => !empty($data['room_id']) ? (int)$data['room_id'] : null,
            ':start_time'       => $data['start_time'] ?? '09:00:00',
            ':end_time'         => $data['end_time'] ?? '10:00:00',
        ]);
    }

    // ─── 2. Internal Marks ─────────────────────────────────────
    public function saveInternalMarks(int $sectionId, int $subjectId, int $academicYearId, string $examType, array $marksMap, float $maxMarks): bool
    {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare('
                INSERT INTO internal_marks (
                    student_id, subject_id, academic_year_id, exam_type, marks_obtained, max_marks, entered_by, created_at
                ) VALUES (
                    :student_id, :subject_id, :academic_year_id, :exam_type, :marks_obtained, :max_marks, :entered_by, NOW()
                )
                ON DUPLICATE KEY UPDATE
                    marks_obtained = VALUES(marks_obtained),
                    max_marks      = VALUES(max_marks),
                    entered_by     = VALUES(entered_by)
            ');

            $enteredBy = auth_id() ?? 1;

            foreach ($marksMap as $studentId => $marks) {
                $stmt->execute([
                    ':student_id'       => (int) $studentId,
                    ':subject_id'       => $subjectId,
                    ':academic_year_id' => $academicYearId,
                    ':exam_type'        => $examType,
                    ':marks_obtained'   => (float) $marks,
                    ':max_marks'        => $maxMarks,
                    ':entered_by'       => $enteredBy,
                ]);
            }

            db()->commit();
            return true;
        } catch (Exception $e) {
            db()->rollBack();
            return false;
        }
    }

    // ─── 3. External Marks & Consolidated Results Engine ──────
    public function saveExternalMarksAndComputeResult(int $semesterId, int $academicYearId, array $externalMarksMap, float $maxMarks = 100.0): bool
    {
        db()->beginTransaction();
        try {
            $enteredBy = auth_id() ?? 1;

            // 1. Save External Marks per student per subject
            $emStmt = db()->prepare('
                INSERT INTO external_marks (
                    student_id, subject_id, semester_id, academic_year_id, marks_obtained, max_marks, grade, entered_by, created_at
                ) VALUES (
                    :student_id, :subject_id, :semester_id, :academic_year_id, :marks_obtained, :max_marks, :grade, :entered_by, NOW()
                )
                ON DUPLICATE KEY UPDATE
                    marks_obtained = VALUES(marks_obtained),
                    max_marks      = VALUES(max_marks),
                    grade          = VALUES(grade),
                    entered_by     = VALUES(entered_by)
            ');

            foreach ($externalMarksMap as $studentId => $subjectsData) {
                $totalStudentMarks = 0.0;
                $totalMaxMarks     = 0.0;
                $hasFailed         = false;

                foreach ($subjectsData as $subjectId => $obtainedMarks) {
                    $obtained = (float) $obtainedMarks;

                    // Grade calculation
                    $pct = ($obtained / $maxMarks) * 100;
                    $grade = 'F';
                    if ($pct >= 85)     $grade = 'A+';
                    elseif ($pct >= 75) $grade = 'A';
                    elseif ($pct >= 65) $grade = 'B';
                    elseif ($pct >= 50) $grade = 'C';
                    elseif ($pct >= 40) $grade = 'D';

                    if ($pct < 40) {
                        $hasFailed = true;
                    }

                    $emStmt->execute([
                        ':student_id'       => (int) $studentId,
                        ':subject_id'       => (int) $subjectId,
                        ':semester_id'      => $semesterId,
                        ':academic_year_id' => $academicYearId,
                        ':marks_obtained'   => $obtained,
                        ':max_marks'        => $maxMarks,
                        ':grade'            => $grade,
                        ':entered_by'       => $enteredBy,
                    ]);

                    $totalStudentMarks += $obtained;
                    $totalMaxMarks     += $maxMarks;
                }

                // 2. Calculate Consolidated Result
                $overallPct = $totalMaxMarks > 0 ? ($totalStudentMarks / $totalMaxMarks) * 100 : 0.0;
                $overallResult = ($hasFailed || $overallPct < 40.0) ? 'fail' : 'pass';

                $overallGrade = 'F';
                if ($overallPct >= 85)     $overallGrade = 'A+';
                elseif ($overallPct >= 75) $overallGrade = 'A';
                elseif ($overallPct >= 65) $overallGrade = 'B';
                elseif ($overallPct >= 50) $overallGrade = 'C';
                elseif ($overallPct >= 40) $overallGrade = 'D';

                $resStmt = db()->prepare('
                    INSERT INTO results (
                        student_id, semester_id, academic_year_id, total_marks, percentage, grade, result, published, created_at
                    ) VALUES (
                        :student_id, :semester_id, :academic_year_id, :total_marks, :percentage, :grade, :result, 0, NOW()
                    )
                    ON DUPLICATE KEY UPDATE
                        total_marks = VALUES(total_marks),
                        percentage  = VALUES(percentage),
                        grade       = VALUES(grade),
                        result      = VALUES(result),
                        updated_at  = NOW()
                ');

                $resStmt->execute([
                    ':student_id'       => (int) $studentId,
                    ':semester_id'      => $semesterId,
                    ':academic_year_id' => $academicYearId,
                    ':total_marks'      => $totalStudentMarks,
                    ':percentage'       => round($overallPct, 2),
                    ':grade'            => $overallGrade,
                    ':result'           => $overallResult,
                ]);
            }

            db()->commit();
            return true;
        } catch (Exception $e) {
            db()->rollBack();
            return false;
        }
    }

    public function getResults(int $semesterId, int $academicYearId): array
    {
        $stmt = db()->prepare('
            SELECT res.*, s.roll_number, s.first_name, s.last_name
            FROM results res
            JOIN students s ON s.id = res.student_id
            WHERE res.semester_id = :sem_id AND res.academic_year_id = :ay_id
            ORDER BY res.percentage DESC
        ');
        $stmt->execute([':sem_id' => $semesterId, ':ay_id' => $academicYearId]);
        return $stmt->fetchAll() ?: [];
    }

    public function publishResults(int $semesterId, int $academicYearId): bool
    {
        $stmt = db()->prepare('
            UPDATE results
            SET published = 1, published_at = NOW()
            WHERE semester_id = :sem_id AND academic_year_id = :ay_id
        ');
        return $stmt->execute([':sem_id' => $semesterId, ':ay_id' => $academicYearId]);
    }

    /**
     * Fetch complete result history for a student across all semesters (current and previous).
     */
    public function getStudentAllSemesterResults(int $studentId): array
    {
        // 1. Get consolidated semester results for this student
        $stmt = db()->prepare('
            SELECT 
                r.*,
                sem.number as semester_number,
                sem.name as semester_name,
                c.name as course_name,
                ay.name as academic_year_name
            FROM results r
            JOIN semesters sem ON sem.id = r.semester_id
            JOIN courses c ON c.id = sem.course_id
            JOIN academic_years ay ON ay.id = r.academic_year_id
            WHERE r.student_id = :student_id
            ORDER BY sem.number ASC
        ');
        $stmt->execute([':student_id' => $studentId]);
        $semResults = $stmt->fetchAll() ?: [];

        // 2. Fetch detailed subject marks for each semester
        foreach ($semResults as &$sr) {
            $semId = (int) $sr['semester_id'];
            $ayId  = (int) $sr['academic_year_id'];

            $subjStmt = db()->prepare('
                SELECT 
                    sub.code as subject_code,
                    sub.name as subject_name,
                    sub.credits,
                    em.marks_obtained as external_marks,
                    em.max_marks as external_max,
                    em.grade,
                    (
                        SELECT MAX(im.marks_obtained) 
                        FROM internal_marks im 
                        WHERE im.student_id = :student_id_sub 
                          AND im.subject_id = sub.id 
                          AND im.academic_year_id = :ay_id_sub
                    ) as internal_marks
                FROM external_marks em
                JOIN subjects sub ON sub.id = em.subject_id
                WHERE em.student_id = :student_id 
                  AND em.semester_id = :sem_id 
                  AND em.academic_year_id = :ay_id
                ORDER BY sub.code ASC
            ');
            $subjStmt->execute([
                ':student_id_sub' => $studentId,
                ':ay_id_sub'      => $ayId,
                ':student_id'     => $studentId,
                ':sem_id'          => $semId,
                ':ay_id'           => $ayId
            ]);
            $sr['subjects'] = $subjStmt->fetchAll() ?: [];
        }

        return $semResults;
    }

    /**
     * Fetch timetable grid for a student's current active section.
     */
    public function getStudentTimetable(int $studentId): array
    {
        $stmt = db()->prepare('
            SELECT sa.section_id, sa.academic_year_id, sec.name as section_name, ay.name as academic_year_name
            FROM student_academics sa
            JOIN sections sec ON sec.id = sa.section_id
            JOIN academic_years ay ON ay.id = sa.academic_year_id
            WHERE sa.student_id = :student_id AND sa.is_current = 1
            LIMIT 1
        ');
        $stmt->execute([':student_id' => $studentId]);
        $ac = $stmt->fetch();

        if (!$ac) {
            return ['info' => null, 'grid' => []];
        }

        $grid = $this->getTimetableForSection((int)$ac['section_id'], (int)$ac['academic_year_id']);
        return [
            'info' => $ac,
            'grid' => $grid
        ];
    }
}

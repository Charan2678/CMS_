<?php

declare(strict_types=1);

namespace App\Modules\Result\services;

use App\Modules\Settings\services\NotificationService;
use Exception;
use PDO;

class ResultService
{
    private NotificationService $notifSvc;

    public function __construct()
    {
        $this->notifSvc = new NotificationService();
    }

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
            $checkStmt = db()->prepare('
                SELECT id, marks_obtained FROM internal_marks 
                WHERE student_id = :sid AND subject_id = :sub_id AND academic_year_id = :ay_id AND exam_type = :etype LIMIT 1
            ');

            $logStmt = db()->prepare('
                INSERT INTO marks_revision_log (type, record_id, student_id, subject_id, old_marks, new_marks, changed_by, reason, created_at)
                VALUES ("internal", :rec_id, :sid, :sub_id, :old_m, :new_m, :by, "Faculty Internal Marks Update", NOW())
            ');

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
                $newMarks = (float) $marks;

                // Audit check
                $checkStmt->execute([
                    ':sid'   => (int) $studentId,
                    ':sub_id'=> $subjectId,
                    ':ay_id' => $academicYearId,
                    ':etype' => $examType,
                ]);
                $existing = $checkStmt->fetch();
                if ($existing && (float)$existing['marks_obtained'] !== $newMarks) {
                    $logStmt->execute([
                        ':rec_id' => (int) $existing['id'],
                        ':sid'    => (int) $studentId,
                        ':sub_id' => $subjectId,
                        ':old_m'  => (float) $existing['marks_obtained'],
                        ':new_m'  => $newMarks,
                        ':by'     => $enteredBy,
                    ]);
                }

                $stmt->execute([
                    ':student_id'       => (int) $studentId,
                    ':subject_id'       => $subjectId,
                    ':academic_year_id' => $academicYearId,
                    ':exam_type'        => $examType,
                    ':marks_obtained'   => $newMarks,
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

    // ─── 3. External Marks & SGPA/CGPA Calculation ─────────────
    public function saveExternalMarksAndComputeResult(
        int $semesterId,
        int $academicYearId,
        array $studentMarksGrid,
        float $maxMarks = 100.0
    ): bool {
        db()->beginTransaction();

        try {
            $extStmt = db()->prepare('
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

            // Fetch subject credits map
            $cStmt = db()->query('SELECT id, credits FROM subjects');
            $creditMap = [];
            foreach ($cStmt->fetchAll() ?: [] as $cRow) {
                $creditMap[(int)$cRow['id']] = (float) ($cRow['credits'] ?? 3.0);
            }

            $enteredBy = auth_id() ?? 1;

            foreach ($studentMarksGrid as $studentId => $subjects) {
                $totalStudentMarks = 0.0;
                $totalMaxMarks     = 0.0;
                $hasFailed         = false;
                $totalCredits      = 0.0;
                $weightedPoints    = 0.0;

                foreach ($subjects as $subjectId => $obtained) {
                    $obtained = (float) $obtained;
                    $pct = ($obtained / $maxMarks) * 100;
                    $credits = $creditMap[(int)$subjectId] ?? 3.0;

                    // Compute Grade & Grade Point
                    if ($pct >= 85) {
                        $grade = 'A+';
                        $point = 10.0;
                    } elseif ($pct >= 75) {
                        $grade = 'A';
                        $point = 9.0;
                    } elseif ($pct >= 65) {
                        $grade = 'B';
                        $point = 8.0;
                    } elseif ($pct >= 50) {
                        $grade = 'C';
                        $point = 7.0;
                    } elseif ($pct >= 40) {
                        $grade = 'D';
                        $point = 6.0;
                    } else {
                        $grade = 'F';
                        $point = 0.0;
                        $hasFailed = true;
                    }

                    $extStmt->execute([
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
                    $totalCredits      += $credits;
                    $weightedPoints    += ($point * $credits);
                }

                // Calculate Consolidated Result & SGPA
                $overallPct = $totalMaxMarks > 0 ? ($totalStudentMarks / $totalMaxMarks) * 100 : 0.0;
                $overallResult = ($hasFailed || $overallPct < 40.0) ? 'fail' : 'pass';
                $sgpa = $totalCredits > 0 ? round($weightedPoints / $totalCredits, 2) : 0.0;

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

    /**
     * Publish semester results and dispatch notifications to affected students & parents.
     */
    public function publishResults(int $semesterId, int $academicYearId): bool
    {
        $stmt = db()->prepare('
            UPDATE results
            SET published = 1, published_at = NOW()
            WHERE semester_id = :sem_id AND academic_year_id = :ay_id
        ');
        $ok = $stmt->execute([':sem_id' => $semesterId, ':ay_id' => $academicYearId]);

        if ($ok) {
            // Fetch semester and affected students
            $semStmt = db()->prepare('SELECT number, name FROM semesters WHERE id = :id LIMIT 1');
            $semStmt->execute([':id' => $semesterId]);
            $semRow = $semStmt->fetch();
            $semName = $semRow ? "Semester {$semRow['number']}" : "Semester Results";

            $stStmt = db()->prepare('
                SELECT s.id, s.roll_number, s.first_name, s.last_name, s.email,
                       u.id AS student_user_id,
                       pu.id AS parent_user_id
                FROM results r
                JOIN students s ON s.id = r.student_id
                JOIN users u ON u.linked_type = "student" AND u.linked_id = s.id
                LEFT JOIN guardians g ON g.student_id = s.id
                LEFT JOIN users pu ON pu.linked_type = "parent" AND pu.linked_id = g.id
                WHERE r.semester_id = :sem_id AND r.academic_year_id = :ay_id
            ');
            $stStmt->execute([':sem_id' => $semesterId, ':ay_id' => $academicYearId]);
            $recipients = $stStmt->fetchAll() ?: [];

            foreach ($recipients as $rec) {
                $title = "{$semName} Official Results Published!";
                $msg   = "Your official examination marks and SGPA for {$semName} have been published. View your marksheet now.";

                if (!empty($rec['student_user_id'])) {
                    $this->notifSvc->notify((int) $rec['student_user_id'], $title, $msg, '/results', 'success', 'high');
                }
                if (!empty($rec['parent_user_id'])) {
                    $this->notifSvc->notify((int) $rec['parent_user_id'], "[Ward] {$title}", "Results for {$rec['first_name']} ({$rec['roll_number']}) have been published.", '/results', 'success', 'high');
                }
            }
        }

        return $ok;
    }

    /**
     * Fetch complete result history for a student across all semesters with SGPA and cumulative CGPA.
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

        $cumulativePoints = 0.0;
        $cumulativeCredits = 0.0;

        // 2. Fetch detailed subject marks for each semester and calculate SGPA
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
            $subjects = $subjStmt->fetchAll() ?: [];
            $sr['subjects'] = $subjects;

            // Credit-weighted SGPA calculation
            $semCredits = 0.0;
            $semPoints = 0.0;
            $earnedCredits = 0.0;

            foreach ($subjects as $s) {
                $cr = (float) ($s['credits'] ?? 3.0);
                $gr = strtoupper($s['grade'] ?? 'F');
                $gp = match($gr) {
                    'A+' => 10.0,
                    'A'  => 9.0,
                    'B'  => 8.0,
                    'C'  => 7.0,
                    'D'  => 6.0,
                    default => 0.0,
                };
                $semCredits += $cr;
                $semPoints += ($gp * $cr);
                if ($gr !== 'F') {
                    $earnedCredits += $cr;
                }
            }

            $sgpa = $semCredits > 0 ? round($semPoints / $semCredits, 2) : round(((float)$sr['percentage']) / 10, 2);
            $sr['sgpa'] = $sgpa;
            $sr['sem_credits'] = $semCredits;
            $sr['earned_credits'] = $earnedCredits;

            $cumulativePoints += $semPoints;
            $cumulativeCredits += $semCredits;
            $sr['cgpa'] = $cumulativeCredits > 0 ? round($cumulativePoints / $cumulativeCredits, 2) : $sgpa;
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

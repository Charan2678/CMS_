<?php

declare(strict_types=1);

namespace App\Modules\Attendance\services;

use Exception;
use PDO;

class AttendanceService
{
    /**
     * Get students enrolled in a section.
     */
    public function getStudentsForSection(int $sectionId): array
    {
        $stmt = db()->prepare('
            SELECT s.id, s.roll_number, s.first_name, s.last_name
            FROM students s
            JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            WHERE sa.section_id = :section_id AND s.status = "active"
            ORDER BY s.roll_number ASC
        ');
        $stmt->execute([':section_id' => $sectionId]);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get existing attendance records for section, subject, date.
     */
    public function getExistingAttendance(int $sectionId, int $subjectId, string $date): array
    {
        $stmt = db()->prepare('
            SELECT student_id, status
            FROM attendance
            WHERE section_id = :section_id AND subject_id = :subject_id AND date = :date
        ');
        $stmt->execute([
            ':section_id' => $sectionId,
            ':subject_id' => $subjectId,
            ':date'       => $date,
        ]);
        $rows = $stmt->fetchAll() ?: [];

        $map = [];
        foreach ($rows as $r) {
            $map[$r['student_id']] = $r['status'];
        }
        return $map;
    }

    /**
     * Bulk save student attendance.
     */
    public function saveBulkAttendance(int $sectionId, int $subjectId, int $academicYearId, string $date, array $statusMap): bool
    {
        db()->beginTransaction();

        try {
            $stmt = db()->prepare('
                INSERT INTO attendance (
                    student_id, subject_id, section_id, academic_year_id, date, status, marked_by, created_at
                ) VALUES (
                    :student_id, :subject_id, :section_id, :academic_year_id, :date, :status, :marked_by, NOW()
                )
                ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by)
            ');

            $markedBy = auth_id() ?? 1;

            foreach ($statusMap as $studentId => $status) {
                $stmt->execute([
                    ':student_id'       => (int) $studentId,
                    ':subject_id'       => $subjectId,
                    ':section_id'       => $sectionId,
                    ':academic_year_id' => $academicYearId,
                    ':date'             => $date,
                    ':status'           => in_array($status, ['present', 'absent', 'late', 'holiday']) ? $status : 'present',
                    ':marked_by'        => $markedBy,
                ]);
            }

            db()->commit();
            return true;
        } catch (Exception $e) {
            db()->rollBack();
            return false;
        }
    }

    /**
     * Resolve student ID for a logged-in user ID.
     */
    public function getStudentIdFromUser(int $userId): ?int
    {
        // First check session linked_id
        if (!empty($_SESSION['linked_id']) && ($_SESSION['linked_type'] ?? '') === 'student') {
            return (int) $_SESSION['linked_id'];
        }

        $stmt = db()->prepare('SELECT linked_id FROM users WHERE id = :id AND linked_type = "student" LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $val = $stmt->fetchColumn();

        if ($val) {
            return (int) $val;
        }

        // Fallback: search student by matching email or username
        $stmt = db()->prepare('SELECT id FROM students WHERE email = (SELECT email FROM users WHERE id = :id) OR roll_number = (SELECT username FROM users WHERE id = :id) LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $val = $stmt->fetchColumn();

        return $val ? (int) $val : null;
    }

    /**
     * Get overall attendance summary for a student.
     */
    public function getStudentSummary(int $studentId): array
    {
        $stmt = db()->prepare('
            SELECT 
                COUNT(*) as total_conducted,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as total_present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as total_absent,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as total_late,
                SUM(CASE WHEN status = "holiday" THEN 1 ELSE 0 END) as total_holiday
            FROM attendance
            WHERE student_id = :student_id
        ');
        $stmt->execute([':student_id' => $studentId]);
        $row = $stmt->fetch() ?: [
            'total_conducted' => 0,
            'total_present' => 0,
            'total_absent' => 0,
            'total_late' => 0,
            'total_holiday' => 0
        ];

        $conducted = (int) $row['total_conducted'];
        $effectiveAttended = (int) $row['total_present'] + ((int)$row['total_late'] * 0.5);
        $percentage = $conducted > 0 ? round(($effectiveAttended / $conducted) * 100, 1) : 100.0;

        return [
            'total_conducted' => $conducted,
            'total_present'   => (int) $row['total_present'],
            'total_absent'    => (int) $row['total_absent'],
            'total_late'      => (int) $row['total_late'],
            'total_holiday'   => (int) $row['total_holiday'],
            'percentage'      => $percentage
        ];
    }

    /**
     * Get subject-wise attendance breakdown for a student.
     */
    public function getStudentSubjectWiseAttendance(int $studentId): array
    {
        $stmt = db()->prepare('
            SELECT 
                sub.id as subject_id,
                sub.name as subject_name,
                sub.code as subject_code,
                sub.type as subject_type,
                COUNT(a.id) as conducted,
                SUM(CASE WHEN a.status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN a.status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN a.status = "late" THEN 1 ELSE 0 END) as late
            FROM attendance a
            JOIN subjects sub ON sub.id = a.subject_id
            WHERE a.student_id = :student_id
            GROUP BY sub.id, sub.name, sub.code, sub.type
            ORDER BY sub.code ASC
        ');
        $stmt->execute([':student_id' => $studentId]);
        $rows = $stmt->fetchAll() ?: [];

        foreach ($rows as &$r) {
            $conducted = (int) $r['conducted'];
            $effective = (int) $r['present'] + ((int) $r['late'] * 0.5);
            $r['percentage'] = $conducted > 0 ? round(($effective / $conducted) * 100, 1) : 100.0;
        }

        return $rows;
    }

    /**
     * Get daily/period-wise attendance log for a student with optional filters.
     */
    public function getStudentDailyLog(int $studentId, ?string $month = null, ?int $subjectId = null): array
    {
        $sql = '
            SELECT 
                a.id,
                a.date,
                a.status,
                sub.name as subject_name,
                sub.code as subject_code,
                sec.name as section_name,
                ay.name as academic_year
            FROM attendance a
            JOIN subjects sub ON sub.id = a.subject_id
            LEFT JOIN sections sec ON sec.id = a.section_id
            LEFT JOIN academic_years ay ON ay.id = a.academic_year_id
            WHERE a.student_id = :student_id
        ';
        $params = [':student_id' => $studentId];

        if (!empty($month)) {
            $sql .= ' AND DATE_FORMAT(a.date, "%Y-%m") = :month';
            $params[':month'] = $month;
        }

        if (!empty($subjectId) && $subjectId > 0) {
            $sql .= ' AND a.subject_id = :subject_id';
            $params[':subject_id'] = $subjectId;
        }

        $sql .= ' ORDER BY a.date DESC, sub.code ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }
}

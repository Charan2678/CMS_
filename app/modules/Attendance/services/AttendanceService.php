<?php

declare(strict_types=1);

namespace App\Modules\Attendance\services;

use App\Modules\Leave\services\LeaveService;
use App\Modules\Settings\services\NotificationService;
use Exception;
use PDO;

class AttendanceService
{
    private NotificationService $notifSvc;
    private LeaveService $leaveSvc;

    public function __construct()
    {
        $this->notifSvc = new NotificationService();
        $this->leaveSvc = new LeaveService();
    }

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
     * Also merges auto-detected approved leaves for the date.
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
            $map[(int)$r['student_id']] = $r['status'];
        }

        // Auto-detect approved leave requests for this date
        $leaveStudentIds = $this->leaveSvc->getActiveStudentLeaveIdsForDate($date);
        foreach ($leaveStudentIds as $sid) {
            if (!isset($map[(int)$sid])) {
                $map[(int)$sid] = 'on_leave';
            }
        }

        return $map;
    }

    /**
     * Bulk save student attendance with automated shortage alerts & audit trail.
     */
    public function saveBulkAttendance(int $sectionId, int $subjectId, int $academicYearId, string $date, array $statusMap): bool
    {
        db()->beginTransaction();

        try {
            $stmt = db()->prepare('
                INSERT INTO attendance (
                    student_id, subject_id, section_id, academic_year_id, date, status, marked_by, updated_by, created_at
                ) VALUES (
                    :student_id, :subject_id, :section_id, :academic_year_id, :date, :status, :marked_by, :updated_by, NOW()
                )
                ON DUPLICATE KEY UPDATE 
                    status = VALUES(status), 
                    updated_by = VALUES(updated_by),
                    updated_at = NOW()
            ');

            $currentUserId = auth_id() ?? 1;

            foreach ($statusMap as $studentId => $status) {
                $stmt->execute([
                    ':student_id'       => (int) $studentId,
                    ':subject_id'       => $subjectId,
                    ':section_id'       => $sectionId,
                    ':academic_year_id' => $academicYearId,
                    ':date'             => $date,
                    ':status'           => in_array($status, ['present', 'absent', 'late', 'holiday', 'on_leave'], true) ? $status : 'present',
                    ':marked_by'        => $currentUserId,
                    ':updated_by'       => $currentUserId,
                ]);
            }

            db()->commit();

            // Post-save: Calculate running attendance % and trigger shortage alerts
            $this->checkAndAlertAttendanceShortage(array_keys($statusMap));

            return true;
        } catch (Exception $e) {
            db()->rollBack();
            return false;
        }
    }

    /**
     * Check running attendance % for students and dispatch alerts if < 75%.
     */
    private function checkAndAlertAttendanceShortage(array $studentIds): void
    {
        $minPercent = 75.0;

        foreach ($studentIds as $sid) {
            $studentId = (int) $sid;
            $stats = $this->getStudentSummary($studentId);
            $pct = (float) $stats['percentage'];

            // If attendance is below minimum threshold and at least 3 classes conducted
            if ($pct < $minPercent && $stats['total_conducted'] >= 3) {
                $student = $this->getStudentDetails($studentId);
                if ($student) {
                    $roll = $student['roll_number'];
                    $name = $student['first_name'] . ' ' . $student['last_name'];
                    $msg  = "Attendance Shortage Alert: Overall attendance is currently at {$pct}% (Minimum required is 75%). Please ensure regular class attendance.";

                    // 1. Notify Student User
                    $studentUserId = $this->getStudentUserId($studentId);
                    if ($studentUserId) {
                        $this->notifSvc->notify(
                            $studentUserId,
                            "⚠️ Attendance Shortage Alert ({$pct}%)",
                            $msg,
                            '/attendance',
                            'alert',
                            'high'
                        );
                    }

                    // 2. Notify Parent User
                    $parentUserId = $this->getParentUserId($studentId);
                    if ($parentUserId) {
                        $this->notifSvc->notify(
                            $parentUserId,
                            "🚨 [Ward Alert] Attendance Shortage ({$name} - {$pct}%)",
                            "Your ward {$name}'s overall attendance has dropped to {$pct}%. Safe standing is 75% or higher.",
                            '/attendance',
                            'alert',
                            'urgent'
                        );
                    }
                }
            }
        }
    }

    /**
     * Resolve student ID for a logged-in user ID.
     */
    public function getStudentIdFromUser(int $userId): ?int
    {
        if (!empty($_SESSION['linked_id']) && ($_SESSION['linked_type'] ?? '') === 'student') {
            return (int) $_SESSION['linked_id'];
        }
        if (!empty($_SESSION['parent_ward_id'])) {
            return (int) $_SESSION['parent_ward_id'];
        }

        $stmt = db()->prepare('SELECT linked_type, linked_id FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $userRow = $stmt->fetch();

        if ($userRow) {
            if ($userRow['linked_type'] === 'student') {
                return (int) $userRow['linked_id'];
            }
            if ($userRow['linked_type'] === 'parent') {
                $gStmt = db()->prepare('SELECT student_id FROM guardians WHERE id = :gid LIMIT 1');
                $gStmt->execute([':gid' => (int) $userRow['linked_id']]);
                $sid = $gStmt->fetchColumn();
                if ($sid) {
                    return (int) $sid;
                }
            }
        }

        $stmt = db()->prepare('SELECT id FROM students WHERE email = (SELECT email FROM users WHERE id = :id1) OR roll_number = (SELECT username FROM users WHERE id = :id2) LIMIT 1');
        $stmt->execute([':id1' => $userId, ':id2' => $userId]);
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
                SUM(CASE WHEN status = "on_leave" THEN 1 ELSE 0 END) as total_on_leave,
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
            'total_on_leave' => 0,
            'total_holiday' => 0
        ];

        // Total active academic classes (excluding holidays)
        $conducted = (int) $row['total_conducted'] - (int) $row['total_holiday'];
        if ($conducted < 0) $conducted = 0;

        // Effective attended (present + 0.5 late + on_leave excused)
        $effectiveAttended = (int) $row['total_present'] + ((int)$row['total_late'] * 0.5) + ((int)$row['total_on_leave'] * 1.0);
        $percentage = $conducted > 0 ? round(($effectiveAttended / $conducted) * 100, 1) : 100.0;
        if ($percentage > 100.0) $percentage = 100.0;

        return [
            'total_conducted' => $conducted,
            'total_present'   => (int) $row['total_present'],
            'total_absent'    => (int) $row['total_absent'],
            'total_late'      => (int) $row['total_late'],
            'total_on_leave'  => (int) $row['total_on_leave'],
            'total_holiday'   => (int) $row['total_holiday'],
            'percentage'      => $percentage,
            'overall_pct'     => $percentage,
        ];
    }

    public function getStudentOverallSummary(int $studentId): array
    {
        return $this->getStudentSummary($studentId);
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
                SUM(CASE WHEN a.status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN a.status = "on_leave" THEN 1 ELSE 0 END) as on_leave
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
            $effective = (int) $r['present'] + ((int) $r['late'] * 0.5) + ((int) $r['on_leave'] * 1.0);
            $r['percentage'] = $conducted > 0 ? round(($effective / $conducted) * 100, 1) : 100.0;
            if ($r['percentage'] > 100.0) $r['percentage'] = 100.0;
        }

        return $rows;
    }

    /**
     * Get daily attendance log for a student.
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

    private function getStudentDetails(int $studentId): ?array
    {
        $stmt = db()->prepare('SELECT id, roll_number, first_name, last_name FROM students WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $studentId]);
        return $stmt->fetch() ?: null;
    }

    private function getStudentUserId(int $studentId): ?int
    {
        $stmt = db()->prepare('SELECT id FROM users WHERE linked_type = "student" AND linked_id = :id LIMIT 1');
        $stmt->execute([':id' => $studentId]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    private function getParentUserId(int $studentId): ?int
    {
        $stmt = db()->prepare('
            SELECT u.id FROM users u
            JOIN guardians g ON g.id = u.linked_id AND u.linked_type = "parent"
            WHERE g.student_id = :sid LIMIT 1
        ');
        $stmt->execute([':sid' => $studentId]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }
}

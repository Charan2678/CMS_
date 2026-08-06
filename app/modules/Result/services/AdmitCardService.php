<?php

declare(strict_types=1);

namespace App\Modules\Result\services;

use App\Modules\Attendance\services\AttendanceService;

class AdmitCardService
{
    private AttendanceService $attendanceService;

    public function __construct()
    {
        $this->attendanceService = new AttendanceService();
    }

    /**
     * Check student exam eligibility based on Attendance (>=75%) & Fee Dues (== 0).
     */
    public function checkEligibility(int $studentId, int $academicYearId, int $semesterId): array
    {
        // 1. Calculate Attendance Percentage
        $summary = $this->attendanceService->getStudentSummary($studentId);
        $attendancePct = (float) ($summary['percentage'] ?? 100.0);

        // If no attendance records exist yet, treat as 100% for fresh semesters
        if (($summary['total_conducted'] ?? 0) === 0) {
            $attendancePct = 100.0;
        }

        // 2. Calculate Fee Dues
        $feeStmt = db()->prepare('
            SELECT 
                COALESCE(SUM(sf.net_amount), 0) AS total_fee,
                COALESCE(SUM(p.amount_paid), 0) AS total_paid
            FROM student_fees sf
            LEFT JOIN payments p ON p.student_fee_id = sf.id AND p.status = "success"
            WHERE sf.student_id = :student_id
        ');
        $feeStmt->execute([':student_id' => $studentId]);
        $feeRow = $feeStmt->fetch();

        $totalFee = (float) ($feeRow['total_fee'] ?? 0.0);
        $totalPaid = (float) ($feeRow['total_paid'] ?? 0.0);
        $pendingDues = max(0.0, $totalFee - $totalPaid);

        // 3. Determine Eligibility Status
        $status = 'eligible';
        $reasons = [];

        if ($attendancePct < 75.0) {
            $status = 'blocked_attendance';
            $reasons[] = "Attendance Shortage: Current attendance is " . number_format($attendancePct, 1) . "% (Minimum required: 75.0%).";
        }

        if ($pendingDues > 0.0) {
            // If already blocked for attendance, note both
            if ($status === 'blocked_attendance') {
                $reasons[] = "Outstanding Fee Dues: Pending balance of ₹" . number_format($pendingDues, 2) . ".";
            } else {
                $status = 'blocked_dues';
                $reasons[] = "Outstanding Fee Dues: Pending balance of ₹" . number_format($pendingDues, 2) . ".";
            }
        }

        // Check if condoned by admin
        $htStmt = db()->prepare('
            SELECT * FROM hall_tickets 
            WHERE student_id = :student_id AND academic_year_id = :acyr AND semester_id = :sem
            LIMIT 1
        ');
        $htStmt->execute([
            ':student_id' => $studentId,
            ':acyr'       => $academicYearId,
            ':sem'        => $semesterId,
        ]);
        $existingHt = $htStmt->fetch();

        if ($existingHt && $existingHt['status'] === 'condoned') {
            $status = 'condoned';
            $reasons = ["Attendance shortage was formally condoned by academic authority."];
        }

        return [
            'status'         => $status,
            'is_eligible'    => in_array($status, ['eligible', 'condoned'], true),
            'attendance_pct' => $attendancePct,
            'pending_dues'   => $pendingDues,
            'reasons'        => $reasons,
            'hall_ticket'    => $existingHt ?: null,
        ];
    }

    /**
     * Get or Generate Hall Ticket record.
     */
    public function getOrGenerateHallTicket(int $studentId, int $academicYearId, int $semesterId): array
    {
        $eligibility = $this->checkEligibility($studentId, $academicYearId, $semesterId);

        // Fetch Student & Placement details
        $stmt = db()->prepare('
            SELECT 
                s.*,
                sa.department_id, sa.course_id, sa.semester_id, sa.section_id,
                d.name AS dept_name, d.code AS dept_code,
                c.name AS course_name, c.code AS course_code,
                sem.number AS sem_number, sem.name AS sem_name,
                sec.name AS section_name,
                ay.name AS acyr_name
            FROM students s
            JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            JOIN departments d ON d.id = sa.department_id
            JOIN courses c ON c.id = sa.course_id
            JOIN semesters sem ON sem.id = sa.semester_id
            JOIN sections sec ON sec.id = sa.section_id
            JOIN academic_years ay ON ay.id = sa.academic_year_id
            WHERE s.id = :student_id
            LIMIT 1
        ');
        $stmt->execute([':student_id' => $studentId]);
        $student = $stmt->fetch();

        if (!$student) {
            return ['success' => false, 'message' => 'Student placement details not found.'];
        }

        // Fetch Exam Timetable / Subjects
        $subStmt = db()->prepare('
            SELECT sub.*, tt.day_of_week, tt.start_time, tt.end_time
            FROM subjects sub
            LEFT JOIN timetable tt ON tt.subject_id = sub.id AND tt.section_id = :section_id
            WHERE sub.semester_id = :semester_id AND sub.status = 1
            ORDER BY sub.code ASC
        ');
        $subStmt->execute([
            ':section_id'  => $student['section_id'],
            ':semester_id' => $semesterId,
        ]);
        $timetable = $subStmt->fetchAll();

        // Create or Update Hall Ticket record
        $htNumber = 'HT-' . date('Y') . '-' . sprintf('%02d', $student['sem_number']) . '-' . strtoupper($student['roll_number']);

        if (!$eligibility['hall_ticket']) {
            $insert = db()->prepare('
                INSERT INTO hall_tickets (
                    student_id, academic_year_id, semester_id, hall_ticket_number, status, attendance_pct, pending_dues, generated_at
                ) VALUES (
                    :student_id, :acyr, :sem, :ht_num, :status, :att_pct, :dues, NOW()
                ) ON DUPLICATE KEY UPDATE 
                    status = VALUES(status), attendance_pct = VALUES(attendance_pct), pending_dues = VALUES(pending_dues)
            ');
            $insert->execute([
                ':student_id' => $studentId,
                ':acyr'       => $academicYearId,
                ':sem'        => $semesterId,
                ':ht_num'     => $htNumber,
                ':status'     => $eligibility['status'],
                ':att_pct'    => $eligibility['attendance_pct'],
                ':dues'       => $eligibility['pending_dues'],
            ]);
        }

        return [
            'success'     => true,
            'eligibility' => $eligibility,
            'student'     => $student,
            'timetable'   => $timetable,
            'ht_number'   => $htNumber,
        ];
    }

    /**
     * Condone Attendance Shortage for a Student Hall Ticket.
     */
    public function condoneShortage(int $studentId, int $academicYearId, int $semesterId, int $condonedBy, string $reason): bool
    {
        $htNumber = 'HT-' . date('Y') . '-CONDONED-' . $studentId;

        $stmt = db()->prepare('
            INSERT INTO hall_tickets (
                student_id, academic_year_id, semester_id, hall_ticket_number, status, condoned_by, condonation_reason, generated_at
            ) VALUES (
                :student_id, :acyr, :sem, :ht_num, "condoned", :by, :reason, NOW()
            ) ON DUPLICATE KEY UPDATE 
                status = "condoned", condoned_by = VALUES(condoned_by), condonation_reason = VALUES(condonation_reason)
        ');

        return $stmt->execute([
            ':student_id' => $studentId,
            ':acyr'       => $academicYearId,
            ':sem'        => $semesterId,
            ':ht_num'     => $htNumber,
            ':by'         => $condonedBy,
            ':reason'     => $reason,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Leave\services;

use App\Modules\Settings\services\NotificationService;
use PDO;

class LeaveService
{
    private NotificationService $notifSvc;

    public function __construct()
    {
        $this->notifSvc = new NotificationService();
    }

    /**
     * Submit a leave application or hostel outpass request.
     */
    public function applyLeave(array $data, int $userId): array
    {
        $appType = $data['applicant_type'] ?? 'student';
        $appId   = (int) ($data['applicant_id'] ?? $userId);
        $leaveType = $data['leave_type'] ?? 'casual';
        $fromDate  = $data['from_date'] ?? date('Y-m-d');
        $toDate    = $data['to_date'] ?? $fromDate;
        $reason    = trim((string) ($data['reason'] ?? ''));
        $expReturn = !empty($data['expected_return_time']) ? $data['expected_return_time'] : null;

        if (empty($reason)) {
            return ['success' => false, 'message' => 'Reason for leave/outpass is required.'];
        }

        if (strtotime($toDate) < strtotime($fromDate)) {
            return ['success' => false, 'message' => 'To Date cannot be earlier than From Date.'];
        }

        try {
            $stmt = db()->prepare('
                INSERT INTO leave_requests (
                    college_id, applicant_type, applicant_id, leave_type, from_date, to_date,
                    reason, expected_return_time, status, created_at
                ) VALUES (
                    1, :applicant_type, :applicant_id, :leave_type, :from_date, :to_date,
                    :reason, :expected_return_time, "pending", NOW()
                )
            ');

            $stmt->execute([
                ':applicant_type'       => in_array($appType, ['student', 'staff', 'faculty'], true) ? $appType : 'student',
                ':applicant_id'         => $appId,
                ':leave_type'           => in_array($leaveType, ['sick', 'casual', 'hostel_outpass', 'duty', 'other'], true) ? $leaveType : 'casual',
                ':from_date'            => $fromDate,
                ':to_date'              => $toDate,
                ':reason'               => $reason,
                ':expected_return_time' => $expReturn,
            ]);

            // Notify Admin / HOD of new pending request
            $applicantName = $this->getApplicantName($appType, $appId);
            $typeLabel = $leaveType === 'hostel_outpass' ? 'Hostel Outpass' : 'Leave Request';
            
            // Notify student user if parent applied
            if (auth_role() === 'parent') {
                $studentUserId = $this->getStudentUserId($appId);
                if ($studentUserId) {
                    $this->notifSvc->notify($studentUserId, "Leave Submitted by Parent", "Your parent has submitted a {$typeLabel} for {$fromDate} to {$toDate}.", '/leave/apply');
                }
            }

            return ['success' => true, 'message' => "{$typeLabel} submitted successfully and is pending institutional review."];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to submit leave request: ' . $e->getMessage()];
        }
    }

    /**
     * Review (Approve or Reject) a leave request.
     */
    public function reviewLeave(int $leaveId, string $status, ?string $remarks, int $reviewerUserId): array
    {
        if (!in_array($status, ['approved', 'rejected'], true)) {
            return ['success' => false, 'message' => 'Invalid review status.'];
        }

        try {
            $stmt = db()->prepare('
                UPDATE leave_requests
                SET status = :status, remarks = :remarks, reviewed_by = :reviewed_by, reviewed_at = NOW()
                WHERE id = :id
            ');
            $stmt->execute([
                ':status'      => $status,
                ':remarks'     => $remarks,
                ':reviewed_by' => $reviewerUserId,
                ':id'          => $leaveId,
            ]);

            // Fetch request details to notify applicant
            $lStmt = db()->prepare('SELECT * FROM leave_requests WHERE id = :id LIMIT 1');
            $lStmt->execute([':id' => $leaveId]);
            $req = $lStmt->fetch();

            if ($req) {
                $appType = $req['applicant_type'];
                $appId   = (int) $req['applicant_id'];
                $userId  = $appType === 'student' ? $this->getStudentUserId($appId) : $appId;

                $typeLabel = $req['leave_type'] === 'hostel_outpass' ? 'Hostel Outpass' : 'Leave Application';
                $statusUpper = strtoupper($status);
                $title = "{$typeLabel} {$statusUpper}";
                $msg   = "Your {$typeLabel} from {$req['from_date']} to {$req['to_date']} has been {$status}." . ($remarks ? " Remarks: {$remarks}" : "");

                if ($userId) {
                    $this->notifSvc->notify((int) $userId, $title, $msg, '/leave/apply', $status === 'approved' ? 'success' : 'alert', 'high');
                }

                // If student, also notify linked parent
                if ($appType === 'student') {
                    $pUserId = $this->getParentUserId($appId);
                    if ($pUserId) {
                        $this->notifSvc->notify((int) $pUserId, "[Ward] {$title}", $msg, '/leave/apply', $status === 'approved' ? 'success' : 'alert');
                    }
                }
            }

            return ['success' => true, 'message' => "Leave request {$status} successfully."];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Review action failed: ' . $e->getMessage()];
        }
    }

    /**
     * Check-in an active hostel outpass upon student return.
     */
    public function checkInOutpass(int $leaveId): bool
    {
        try {
            $stmt = db()->prepare('
                UPDATE leave_requests
                SET status = "completed", actual_return_time = NOW()
                WHERE id = :id AND leave_type = "hostel_outpass"
            ');
            return $stmt->execute([':id' => $leaveId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get active approved student leaves for a specific date (used by Attendance system).
     * Returns array of student IDs on approved leave.
     */
    public function getActiveStudentLeaveIdsForDate(string $date): array
    {
        try {
            $stmt = db()->prepare('
                SELECT applicant_id FROM leave_requests
                WHERE applicant_type = "student"
                  AND status = "approved"
                  AND :date BETWEEN from_date AND to_date
            ');
            $stmt->execute([':date' => $date]);
            return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get leave requests submitted by a specific student/staff.
     */
    public function getMyLeaves(string $applicantType, ?int $applicantId = null): array
    {
        if (empty($applicantId)) {
            return [];
        }
        try {
            $stmt = db()->prepare('
                SELECT lr.*, u.username AS reviewer_name
                FROM leave_requests lr
                LEFT JOIN users u ON u.id = lr.reviewed_by
                WHERE lr.applicant_type = :app_type AND lr.applicant_id = :app_id
                ORDER BY lr.id DESC
            ');
            $stmt->execute([
                ':app_type' => $applicantType,
                ':app_id'   => $applicantId,
            ]);
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get all pending leave requests for faculty/HOD/admin review.
     */
    public function getPendingLeaves(?int $departmentId = null): array
    {
        try {
            $sql = '
                SELECT lr.*, 
                       CASE 
                           WHEN lr.applicant_type = "student" THEN CONCAT(s.first_name, " ", s.last_name)
                           WHEN lr.applicant_type = "faculty" THEN CONCAT(f.first_name, " ", f.last_name)
                           ELSE CONCAT(st.first_name, " ", st.last_name)
                       END AS applicant_name,
                       s.roll_number,
                       d.name AS department_name
                FROM leave_requests lr
                LEFT JOIN students s ON lr.applicant_type = "student" AND s.id = lr.applicant_id
                LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
                LEFT JOIN departments d ON d.id = sa.department_id
                LEFT JOIN faculty f ON lr.applicant_type = "faculty" AND f.id = lr.applicant_id
                LEFT JOIN staff st ON lr.applicant_type = "staff" AND st.id = lr.applicant_id
                ORDER BY FIELD(lr.status, "pending", "approved", "rejected", "completed"), lr.created_at DESC
            ';

            $stmt = db()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get all hostel outpasses for warden monitoring.
     */
    public function getHostelOutpasses(): array
    {
        try {
            $stmt = db()->prepare('
                SELECT lr.*, 
                       CONCAT(s.first_name, " ", s.last_name) AS student_name,
                       s.roll_number, s.mobile AS student_phone,
                       hb.name AS block_name, hr.room_number
                FROM leave_requests lr
                JOIN students s ON s.id = lr.applicant_id
                LEFT JOIN hostel_allocations ha ON ha.student_id = s.id AND ha.status = "active"
                LEFT JOIN hostel_rooms hr ON hr.id = ha.hostel_room_id
                LEFT JOIN hostel_blocks hb ON hb.id = hr.hostel_block_id
                WHERE lr.leave_type = "hostel_outpass"
                ORDER BY lr.id DESC
            ');
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getApplicantName(string $type, int $id): string
    {
        if ($type === 'student') {
            $s = db()->query("SELECT CONCAT(first_name, ' ', last_name) FROM students WHERE id = {$id}")->fetchColumn();
            return (string) ($s ?: 'Student');
        }
        return 'Staff/Faculty';
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

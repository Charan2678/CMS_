<?php

declare(strict_types=1);

namespace App\Modules\Dashboard\controllers;

use App\Core\Controller;
use App\Modules\Attendance\services\AttendanceService;
use App\Modules\Fee\services\FeeService;
use App\Modules\Result\services\ResultService;
use App\Modules\Settings\services\NotificationService;
use App\Modules\Canteen\services\CanteenService;

class DashboardController extends Controller
{
    /**
     * Dashboard main page.
     */
    public function index(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        if ($_SESSION['must_change_password'] ?? false) {
            $this->redirect('/change-password');
        }

        $role   = auth_role();
        $userId = auth_id();

        // System-wide metric counts with error-resilient fallbacks
        try {
            $studentCount = (int) db()->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn();
        } catch (\Throwable $e) {
            $studentCount = 0;
        }

        try {
            $facultyCount = (int) db()->query("SELECT COUNT(*) FROM faculty WHERE status = 'active'")->fetchColumn();
        } catch (\Throwable $e) {
            $facultyCount = 0;
        }

        try {
            $staffCount = (int) db()->query("SELECT COUNT(*) FROM staff WHERE status = 'active'")->fetchColumn();
        } catch (\Throwable $e) {
            $staffCount = 0;
        }

        try {
            $deptCount = (int) db()->query("SELECT COUNT(*) FROM departments WHERE status = 1")->fetchColumn();
        } catch (\Throwable $e) {
            $deptCount = 0;
        }

        try {
            $courseCount = (int) db()->query("SELECT COUNT(*) FROM courses WHERE status = 1")->fetchColumn();
        } catch (\Throwable $e) {
            $courseCount = 0;
        }

        // Total Financial Collections (queries `payments` table)
        try {
            $totalFeeCollected = (float) (db()->query("SELECT COALESCE(SUM(amount_paid), 0) FROM payments")->fetchColumn() ?: 0);
        } catch (\Throwable $e) {
            $totalFeeCollected = 0.00;
        }

        // Announcements Feed
        $announcements = [];
        try {
            $notificationService = new NotificationService();
            $announcements       = $notificationService->getAnnouncements(1);
        } catch (\Throwable $e) {
            $announcements = [];
        }

        // Audit Logs (for admin/super_admin)
        $auditLogs = [];
        if (in_array($role, ['super_admin', 'admin'])) {
            try {
                $stmt = db()->query("
                    SELECT al.*, u.username 
                    FROM audit_logs al 
                    LEFT JOIN users u ON u.id = al.user_id 
                    ORDER BY al.id DESC 
                    LIMIT 5
                ");
                $auditLogs = $stmt ? ($stmt->fetchAll() ?: []) : [];
            } catch (\Throwable $e) {
                $auditLogs = [];
            }
        }

        // Department-wise distribution
        $deptStats = [];
        try {
            $stmt = db()->query("
                SELECT d.name, d.code, COUNT(s.id) AS student_count
                FROM departments d
                LEFT JOIN student_academics sa ON sa.department_id = d.id AND sa.is_current = 1
                LEFT JOIN students s ON s.id = sa.student_id AND s.status = 'active'
                WHERE d.status = 1
                GROUP BY d.id
            ");
            $deptStats = $stmt ? ($stmt->fetchAll() ?: []) : [];
        } catch (\Throwable $e) {
            $deptStats = [];
        }

        // Real-time Student & Parent Ward Metrics
        $studentData = [];
        if (in_array($role, ['student', 'parent'], true) && $userId) {
            try {
                $attendanceService = new AttendanceService();
                $feeService        = new FeeService();
                $resultService     = new ResultService();
                $canteenService    = new CanteenService();

                $studentId = $attendanceService->getStudentIdFromUser($userId);
                $wardInfo  = null;

                if ($studentId) {
                    $attSummary = $attendanceService->getStudentSummary($studentId);
                    $feeSummary = $feeService->getFeesForStudent($studentId);
                    $timetable  = $resultService->getStudentTimetable($studentId);
                    $allResults = $resultService->getStudentAllSemesterResults($studentId);

                    // Fetch ward academic details
                    $sStmt = db()->prepare('
                        SELECT s.first_name, s.last_name, s.roll_number, s.email, s.mobile,
                               d.name AS department_name, c.name AS course_name, sem.number AS semester_number, sec.name AS section_name
                        FROM students s
                        LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
                        LEFT JOIN departments d ON d.id = sa.department_id
                        LEFT JOIN courses c ON c.id = sa.course_id
                        LEFT JOIN semesters sem ON sem.id = sa.semester_id
                        LEFT JOIN sections sec ON sec.id = sa.section_id
                        WHERE s.id = :sid LIMIT 1
                    ');
                    $sStmt->execute([':sid' => $studentId]);
                    $wardInfo = $sStmt->fetch() ?: null;
                } else {
                    $attSummary = ['percentage' => 0, 'total_conducted' => 0, 'total_present' => 0, 'total_absent' => 0];
                    $feeSummary = ['total_payable' => 0, 'total_paid' => 0, 'balance_due' => 0];
                    $timetable  = [];
                    $allResults = [];
                }

                $canteenOrders = $canteenService->getUserOrders($userId);

                $studentData = [
                    'student_id'    => $studentId,
                    'ward_info'     => $wardInfo,
                    'attendance'    => $attSummary,
                    'fee'           => $feeSummary,
                    'timetable'     => $timetable,
                    'all_results'   => $allResults,
                    'announcements' => $announcements,
                    'canteen'       => $canteenOrders,
                ];
            } catch (\Throwable $e) {
                $studentData = [];
            }
        }

        $this->render('Dashboard/views/index', [
            'title'             => 'Overview Dashboard',
            'studentCount'      => $studentCount,
            'facultyCount'      => $facultyCount,
            'staffCount'        => $staffCount,
            'deptCount'         => $deptCount,
            'courseCount'       => $courseCount,
            'totalFeeCollected' => $totalFeeCollected,
            'announcements'     => $announcements,
            'auditLogs'         => $auditLogs,
            'deptStats'         => $deptStats,
            'studentData'       => $studentData,
        ], 'layout');
    }
}

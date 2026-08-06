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

        // System-wide metric counts
        $studentCount = (int) db()->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn();
        $facultyCount = (int) db()->query("SELECT COUNT(*) FROM faculty WHERE status = 'active'")->fetchColumn();
        $deptCount    = (int) db()->query("SELECT COUNT(*) FROM departments WHERE status = 1")->fetchColumn();
        $courseCount  = (int) db()->query("SELECT COUNT(*) FROM courses WHERE status = 1")->fetchColumn();

        // Real-time Student Metrics
        $studentData = [];
        if ($role === 'student' && $userId) {
            $attendanceService   = new AttendanceService();
            $feeService          = new FeeService();
            $resultService       = new ResultService();
            $notificationService = new NotificationService();
            $canteenService      = new CanteenService();

            $studentId = $attendanceService->getStudentIdFromUser($userId);

            if ($studentId) {
                $attSummary = $attendanceService->getStudentSummary($studentId);
                $feeSummary = $feeService->getFeesForStudent($studentId);
                $timetable  = $resultService->getStudentTimetable($studentId);
                $allResults = $resultService->getStudentAllSemesterResults($studentId);
            } else {
                $attSummary = ['percentage' => 0, 'total_conducted' => 0, 'total_present' => 0, 'total_absent' => 0];
                $feeSummary = ['total_payable' => 0, 'total_paid' => 0, 'balance_due' => 0];
                $timetable  = [];
                $allResults = [];
            }

            $announcements = $notificationService->getAnnouncements(1);
            $canteenOrders = $canteenService->getUserOrders($userId);

            $studentData = [
                'student_id'    => $studentId,
                'attendance'    => $attSummary,
                'fee'           => $feeSummary,
                'timetable'     => $timetable,
                'all_results'   => $allResults,
                'announcements' => $announcements,
                'canteen'       => $canteenOrders,
            ];
        }

        $this->render('Dashboard/views/index', [
            'title'        => 'Overview Dashboard',
            'studentCount' => $studentCount,
            'facultyCount' => $facultyCount,
            'deptCount'    => $deptCount,
            'courseCount'  => $courseCount,
            'studentData'  => $studentData,
        ], 'layout');
    }
}

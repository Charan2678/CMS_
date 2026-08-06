<?php

declare(strict_types=1);

namespace App\Modules\Attendance\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Attendance\services\AttendanceService;
use App\Modules\Master\services\MasterService;

class AttendanceController extends Controller
{
    private AttendanceService $attendanceService;
    private MasterService $masterService;

    public function __construct()
    {
        $this->attendanceService = new AttendanceService();
        $this->masterService     = new MasterService();
    }

    /**
     * Entry point for /attendance route. Role-aware dispatcher.
     */
    public function index(): void
    {
        if (auth_role() === 'student') {
            $this->studentView();
            return;
        }

        $this->mark();
    }

    /**
     * View Attendance Dashboard for Logged-In Student.
     */
    public function studentView(): void
    {
        Permission::enforce('attendance.view');

        $userId = auth_id();
        $studentId = $userId ? $this->attendanceService->getStudentIdFromUser($userId) : null;

        if (!$studentId) {
            $this->render('Attendance/views/student_view', [
                'title'           => 'My Attendance',
                'summary'         => ['total_conducted' => 0, 'total_present' => 0, 'total_absent' => 0, 'total_late' => 0, 'percentage' => 100],
                'subjects'        => [],
                'dailyLog'        => [],
                'selectedMonth'   => '',
                'selectedSubject' => 0,
            ], 'layout');
            return;
        }

        $selectedMonth   = query('month', '');
        $selectedSubject = (int) query('subject_id', 0);

        $summary  = $this->attendanceService->getStudentSummary($studentId);
        $subjects = $this->attendanceService->getStudentSubjectWiseAttendance($studentId);
        $dailyLog = $this->attendanceService->getStudentDailyLog($studentId, $selectedMonth ?: null, $selectedSubject > 0 ? $selectedSubject : null);

        $this->render('Attendance/views/student_view', [
            'title'           => 'My Attendance Dashboard',
            'summary'         => $summary,
            'subjects'        => $subjects,
            'dailyLog'        => $dailyLog,
            'selectedMonth'   => $selectedMonth,
            'selectedSubject' => $selectedSubject,
        ], 'layout');
    }

    /**
     * Mark & View Attendance (Faculty / Admin).
     */
    public function mark(): void
    {
        Permission::enforce('attendance.mark');

        $error   = null;
        $success = null;

        $sectionId      = (int) query('section_id', $this->input('section_id', '0'));
        $subjectId      = (int) query('subject_id', $this->input('subject_id', '0'));
        $academicYearId = (int) query('academic_year_id', $this->input('academic_year_id', '0'));
        $date           = query('date', $this->input('date', date('Y-m-d')));

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $statusMap = $_POST['attendance'] ?? [];
                if (empty($sectionId) || empty($subjectId) || empty($academicYearId) || empty($statusMap)) {
                    $error = 'Section, Subject, Academic Year, and Student attendance statuses are required.';
                } else {
                    if ($this->attendanceService->saveBulkAttendance($sectionId, $subjectId, $academicYearId, $date, $statusMap)) {
                        $success = 'Attendance marked successfully for ' . count($statusMap) . ' students on ' . $date . '.';
                    } else {
                        $error = 'Failed to record attendance.';
                    }
                }
            }
        }

        $sections      = $this->masterService->getSections();
        $subjects      = $this->masterService->getSubjects();
        $academicYears = $this->masterService->getAcademicYears(1);

        $students = [];
        $existing = [];

        if ($sectionId > 0 && $subjectId > 0) {
            $students = $this->attendanceService->getStudentsForSection($sectionId);
            $existing = $this->attendanceService->getExistingAttendance($sectionId, $subjectId, $date);
        }

        $this->render('Attendance/views/mark', [
            'title'          => 'Mark Daily Attendance',
            'sections'       => $sections,
            'subjects'       => $subjects,
            'academicYears'  => $academicYears,
            'sectionId'      => $sectionId,
            'subjectId'      => $subjectId,
            'academicYearId' => $academicYearId,
            'date'           => $date,
            'students'       => $students,
            'existing'       => $existing,
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }
}

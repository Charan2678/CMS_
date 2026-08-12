<?php

declare(strict_types=1);

namespace App\Modules\Leave\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Attendance\services\AttendanceService;
use App\Modules\Leave\services\LeaveService;

class LeaveController extends Controller
{
    private LeaveService $leaveService;

    public function __construct()
    {
        $this->leaveService = new LeaveService();
    }

    /**
     * Apply for Student/Staff Leave or Hostel Outpass.
     */
    public function apply(): void
    {
        Permission::enforce('leave.apply');

        $userId = (int) auth_id();
        $role   = auth_role();
        $error   = null;
        $success = null;

        // Resolve applicant ID & type
        $attSvc = new AttendanceService();
        $applicantType = in_array($role, ['student', 'parent']) ? 'student' : ($role === 'faculty' ? 'faculty' : 'staff');
        $studentIdVal  = $attSvc->getStudentIdFromUser($userId);
        $applicantId   = $applicantType === 'student' ? ($studentIdVal ? (int)$studentIdVal : null) : (int)$userId;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'applicant_type'       => $applicantType,
                    'applicant_id'         => $applicantId,
                    'leave_type'           => $this->input('leave_type'),
                    'from_date'            => $this->input('from_date'),
                    'to_date'              => $this->input('to_date'),
                    'expected_return_time' => $this->input('expected_return_time'),
                    'reason'               => $this->input('reason'),
                ];

                $res = $this->leaveService->applyLeave($data, $userId);
                if ($res['success']) {
                    $success = $res['message'];
                } else {
                    $error = $res['message'];
                }
            }
        }

        $myLeaves = $this->leaveService->getMyLeaves($applicantType, $applicantId);

        $this->render('Leave/views/apply', [
            'title'     => 'Apply Leave & Hostel Outpass',
            'myLeaves'  => $myLeaves,
            'isParent'  => $role === 'parent',
            'error'     => $error,
            'success'   => $success,
        ], 'layout');
    }

    /**
     * Institutional Review Screen for Faculty / HOD / Admin.
     */
    public function review(): void
    {
        Permission::enforce('leave.approve');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $leaveId  = (int) $this->input('leave_id');
                $status   = $this->input('status'); // approved / rejected
                $remarks  = $this->input('remarks');

                $res = $this->leaveService->reviewLeave($leaveId, $status, $remarks, (int) auth_id());
                if ($res['success']) {
                    $success = $res['message'];
                } else {
                    $error = $res['message'];
                }
            }
        }

        $leaves = $this->leaveService->getPendingLeaves();

        $this->render('Leave/views/review', [
            'title'   => 'Review Leave Applications & Outpasses',
            'leaves'  => $leaves,
            'error'   => $error,
            'success' => $success,
        ], 'layout');
    }

    /**
     * Warden Hostel Outpass Tracker & Security Log.
     */
    public function outpasses(): void
    {
        Permission::enforce('hostel.allocate');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $leaveId = (int) $this->input('leave_id');
                if ($this->leaveService->checkInOutpass($leaveId)) {
                    $success = 'Student checked back into hostel premises successfully!';
                } else {
                    $error = 'Failed to check-in student outpass.';
                }
            }
        }

        $outpasses = $this->leaveService->getHostelOutpasses();

        $this->render('Leave/views/outpass', [
            'title'     => 'Hostel Outpass & Gate Security Register',
            'outpasses' => $outpasses,
            'error'     => $error,
            'success'   => $success,
        ], 'layout');
    }
}

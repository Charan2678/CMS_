<?php

declare(strict_types=1);

namespace App\Modules\Staff\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Faculty\services\FacultyService;
use App\Modules\Staff\services\StaffService;

class StaffController extends Controller
{
    private StaffService $staffService;
    private FacultyService $facultyService;

    public function __construct()
    {
        $this->staffService   = new StaffService();
        $this->facultyService = new FacultyService();
    }

    /**
     * Staff Directory.
     */
    public function index(): void
    {
        Permission::enforce('staff.view');

        $filters = [
            'department_type' => query('department_type', ''),
            'status'          => query('status', ''),
            'search'          => query('search', ''),
        ];

        $staffList = $this->staffService->getAllStaff($filters);

        $this->render('Staff/views/index', [
            'title'     => 'Non-Faculty Staff Directory',
            'staffList' => $staffList,
            'filters'   => $filters,
            'success'   => flash_get('success'),
        ], 'layout');
    }

    /**
     * Onboard Staff Member.
     */
    public function create(): void
    {
        Permission::enforce('staff.create');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Security session expired. Please refresh and try again.';
            } else {
                $data = $_POST;
                $res  = $this->staffService->createStaff($data);

                if ($res['success']) {
                    flash('success', $res['message']);
                    $this->redirect('/staff/' . $res['staff_id']);
                } else {
                    $error = $res['message'];
                }
            }
        }

        $designations = $this->facultyService->getDesignations(1);

        $this->render('Staff/views/create', [
            'title'        => 'Staff Onboarding',
            'designations' => $designations,
            'error'        => $error,
            'success'      => $success,
        ], 'layout');
    }

    /**
     * Show Staff Profile.
     */
    public function show(string $id): void
    {
        Permission::enforce('staff.view');

        $staffId = (int) $id;
        $staff   = $this->staffService->getStaffProfile($staffId);

        if (!$staff) {
            http_response_code(404);
            $this->render('Master/views/404', [], null);
            return;
        }

        $this->render('Staff/views/show', [
            'title' => "Staff Profile: {$staff['first_name']} {$staff['last_name']}",
            'staff' => $staff,
        ], 'layout');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Transport\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Student\services\StudentService;
use App\Modules\Transport\services\TransportService;

class TransportController extends Controller
{
    private TransportService $transportService;
    private StudentService $studentService;

    public function __construct()
    {
        $this->transportService = new TransportService();
        $this->studentService   = new StudentService();
    }

    public function index(): void
    {
        $canManage = Permission::has('transport.manage');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('transport.manage');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'create_route');

                if ($action === 'create_route') {
                    $data = [
                        'college_id'  => 1,
                        'route_name'  => $this->input('route_name'),
                        'route_code'  => $this->input('route_code'),
                        'start_point' => $this->input('start_point'),
                        'end_point'   => $this->input('end_point'),
                        'fare'        => (float) $this->input('fare', '0.00'),
                    ];

                    if (empty($data['route_name'])) {
                        $error = 'Route name is required.';
                    } else {
                        if ($this->transportService->createTransportRoute($data)) {
                            $success = 'Transport route added successfully.';
                        } else {
                            $error = 'Failed to add transport route.';
                        }
                    }
                } elseif ($action === 'allocate_student') {
                    $studentId = (int) $this->input('student_id');
                    $routeId   = (int) $this->input('transport_route_id');

                    if (!$studentId || !$routeId) {
                        $error = 'Please select both a student and a route.';
                    } else {
                        $res = $this->transportService->allocateStudent($studentId, $routeId);
                        if ($res['success']) {
                            $success = $res['message'];
                        } else {
                            $error = $res['message'];
                        }
                    }
                } elseif ($action === 'cancel_allocation') {
                    $allocId = (int) $this->input('allocation_id');
                    if ($this->transportService->vacateAllocation($allocId)) {
                        $success = 'Transport subscription cancelled successfully.';
                    } else {
                        $error = 'Failed to cancel subscription.';
                    }
                }
            }
        }

        $routes      = $this->transportService->getTransportRoutes(1);
        $allocations = $this->transportService->getAllocations();
        $students    = $this->studentService->getStudents(1, 1, 100)['data'] ?? [];

        $this->render('Transport/views/index', [
            'title'       => 'Transport & Bus Routes Management',
            'routes'      => $routes,
            'allocations' => $allocations,
            'students'    => $students,
            'canManage'   => $canManage,
            'error'       => $error,
            'success'     => $success,
        ], 'layout');
    }
}

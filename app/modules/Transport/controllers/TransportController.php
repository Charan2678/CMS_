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

    /**
     * Transport Management Dashboard.
     */
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
                        'fare'        => (float) $this->input('fare', '18000.00'),
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
        $passStats   = $this->transportService->getBusPassStats();

        $myStudentId = null;
        $isStudentOrParent = false;
        if (is_authenticated()) {
            $user = auth_user();
            if ($user && $user['linked_type'] === 'student') {
                $myStudentId = (int) $user['linked_id'];
                $isStudentOrParent = true;
            } elseif ($user && $user['linked_type'] === 'parent') {
                $myStudentId = (int) (session('active_ward_id') ?? 1);
                $isStudentOrParent = true;
            }
        }

        $mySubscription = null;
        $myBusPass = null;
        if ($myStudentId) {
            $mySubscription = $this->transportService->getStudentActiveSubscription($myStudentId);
            $myBusPass      = $this->transportService->getOrCreateBusPass($myStudentId);
        }

        // Student-Facing Dedicated Experience
        if ($isStudentOrParent && !$canManage) {
            $this->render('Transport/views/student_index', [
                'title'          => 'College Bus Transport & Digital Pass',
                'routes'         => $routes,
                'mySubscription' => $mySubscription,
                'myBusPass'      => $myBusPass,
                'studentId'      => $myStudentId,
                'error'          => $error,
                'success'        => $success,
            ], 'layout');
            return;
        }

        // Admin Management View
        $this->render('Transport/views/index', [
            'title'             => 'Transport & Bus Routes Management',
            'routes'            => $routes,
            'allocations'       => $allocations,
            'students'          => $students,
            'passStats'         => $passStats,
            'mySubscription'    => $mySubscription,
            'myBusPass'         => $myBusPass,
            'isStudentOrParent' => $isStudentOrParent,
            'canManage'         => $canManage,
            'error'             => $error,
            'success'           => $success,
        ], 'layout');
    }

    /**
     * Student Self-Service Route & Pickup Stop Selection (POST /transport/subscribe).
     */
    public function subscribeRoute(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
            return;
        }

        if (!csrf_verify($this->input('_csrf_token'))) {
            flash('error', 'Invalid security token.');
            $this->redirect('/transport');
            return;
        }

        $user = auth_user();
        $studentId = 0;
        if ($user && $user['linked_type'] === 'student') {
            $studentId = (int) $user['linked_id'];
        } elseif ($user && $user['linked_type'] === 'parent') {
            $studentId = (int) (session('active_ward_id') ?? 1);
        } else {
            $studentId = (int) $this->input('student_id', '1');
        }

        $routeId     = (int) $this->input('route_id');
        $pickupPoint = trim((string) $this->input('pickup_point', 'Campus Main Gate'));

        if (!$routeId) {
            flash('error', 'Please select a valid bus route.');
            $this->redirect('/transport');
            return;
        }

        $existingPass = $this->transportService->getOrCreateBusPass($studentId);
        $hasPaidPass  = ($existingPass && $existingPass['status'] === 'active');

        $res = $this->transportService->saveStudentRouteSelection($studentId, $routeId, $pickupPoint);
        if ($res['success']) {
            if ($hasPaidPass) {
                flash('success', 'Route & stop change registered. Please complete the nominal modification fee of ₹99.00 to reissue your digital bus pass.');
                $this->redirect('/fee/pay/transport_change');
            } else {
                flash('success', 'Route and pickup stop selected. Please complete your annual transport fee payment to activate your bus pass.');
                $this->redirect('/fee/pay/transport');
            }
            return;
        }

        flash('error', $res['message'] ?? 'Failed to select route.');
        $this->redirect('/transport');
    }

    /**
     * Student Self-Service Digital Bus Pass View (/transport/pass).
     */
    public function myPass(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
            return;
        }

        $user = auth_user();
        $studentId = 0;

        if ($user && $user['linked_type'] === 'student') {
            $studentId = (int) $user['linked_id'];
        } elseif ($user && $user['linked_type'] === 'parent') {
            $studentId = (int) (session('active_ward_id') ?? 1);
        } elseif (Permission::has('transport.manage')) {
            // For testing by admin, default to first student with subscription
            $alloc = db()->query('SELECT student_id FROM transport_allocations WHERE status = "active" LIMIT 1')->fetch();
            $studentId = $alloc ? (int)$alloc['student_id'] : 1;
        }

        $subscription = $this->transportService->getStudentActiveSubscription($studentId);
        $pass         = $this->transportService->getOrCreateBusPass($studentId);

        $this->render('Transport/views/my_pass', [
            'title'        => 'My Digital Bus Pass',
            'subscription' => $subscription,
            'pass'         => $pass,
            'studentId'    => $studentId,
        ], 'layout');
    }

    /**
     * View Bus Pass Physical ID Card View (Print / Download PNG) (/transport/pass/{id}).
     */
    public function viewPass(string|int $id): void
    {
        $passId = (int) $id;
        $pass = $this->transportService->getBusPassById($passId);

        if (!$pass) {
            http_response_code(404);
            $this->render('Master/views/404', [], null);
            return;
        }

        // Authorization check: Student can only view their own pass, admin/transport manager can view all
        if (is_authenticated()) {
            $user = auth_user();
            if ($user && $user['linked_type'] === 'student' && (int)$user['linked_id'] !== (int)$pass['student_id']) {
                Permission::enforce('transport.manage');
            }
        }

        $this->render('Transport/views/pass_card', [
            'title' => 'Digital Bus Pass - ' . ($pass['pass_number'] ?? 'KEC'),
            'pass'  => $pass,
        ], null);
    }

    /**
     * Admin Pass Management Register (/transport/passes).
     */
    public function passes(): void
    {
        Permission::enforce('transport.manage');

        $status = query('status', '');
        $search = query('search', '');

        $passes = $this->transportService->getAllBusPasses([
            'status' => $status,
            'search' => $search,
        ]);
        $stats  = $this->transportService->getBusPassStats();

        $this->render('Transport/views/passes', [
            'title'   => 'Transport Digital Bus Passes Register',
            'passes'  => $passes,
            'stats'   => $stats,
            'status'  => $status,
            'search'  => $search,
            'success' => flash_get('success'),
            'error'   => flash_get('error'),
        ], 'layout');
    }

    /**
     * Suspend Bus Pass (POST /transport/pass/{id}/suspend).
     */
    public function suspendPass(string|int $id): void
    {
        Permission::enforce('transport.manage');

        if (!csrf_verify($this->input('_csrf_token'))) {
            flash('error', 'Invalid security token.');
            $this->redirect('/transport/passes');
            return;
        }

        $passId = (int) $id;
        $reason = $this->input('reason', 'Administrative suspension');
        $adminId = auth_id() ?? 1;

        if ($this->transportService->suspendBusPass($passId, $reason, $adminId)) {
            flash('success', 'Bus pass suspended successfully.');
        } else {
            flash('error', 'Failed to suspend bus pass.');
        }

        $this->redirect('/transport/passes');
    }

    /**
     * Reactivate Bus Pass (POST /transport/pass/{id}/reactivate).
     */
    public function reactivatePass(string|int $id): void
    {
        Permission::enforce('transport.manage');

        if (!csrf_verify($this->input('_csrf_token'))) {
            flash('error', 'Invalid security token.');
            $this->redirect('/transport/passes');
            return;
        }

        $passId = (int) $id;
        $adminId = auth_id() ?? 1;

        if ($this->transportService->reactivateBusPass($passId, $adminId)) {
            flash('success', 'Bus pass reactivated successfully.');
        } else {
            flash('error', 'Failed to reactivate bus pass.');
        }

        $this->redirect('/transport/passes');
    }
}

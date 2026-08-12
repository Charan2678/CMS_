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
     * Transport Manager Overview Dashboard ONLY (Preserved Design).
     */
    public function index(): void
    {
        $canManage = Permission::has('transport.manage');
        $role      = auth_role();

        if (in_array($role, ['student', 'parent'], true)) {
            $this->redirect('/transport/routes');
            return;
        }

        $stats         = $this->transportService->getStatistics(1);
        $paymentSum    = $this->transportService->getPaymentSummary();
        $studentStatus = $this->transportService->getStudentPaymentStatus();
        $routeSummary  = $this->transportService->getRouteWiseSummary();

        $this->render('Transport/views/index', [
            'title'         => 'Transport Dashboard — Kuppam Engineering College',
            'stats'         => $stats,
            'paymentSum'    => $paymentSum,
            'studentStatus' => $studentStatus,
            'routeSummary'  => $routeSummary,
            'canManage'     => $canManage,
        ], 'layout');
    }

    /**
     * Transport & Bus Routes Page (Serves both Transport Manager and Student Portal).
     */
    public function routes(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $canManage = Permission::has('transport.manage');
        $role      = auth_role();
        $studentId = $this->getStudentId();


        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'create_route');

                if ($action === 'select_route') {
                    $routeId     = (int) $this->input('route_id');
                    $pickupPoint = $this->input('pickup_point');
                    $res         = $this->transportService->selectRoute($studentId, $routeId, $pickupPoint);

                    if ($res['success']) {
                        flash('success', $res['message']);
                        $this->redirect('/transport/pay');
                        return;
                    } else {
                        $error = $res['message'];
                    }
                } elseif ($canManage && $action === 'create_route') {
                    $data = [
                        'college_id'     => 1,
                        'route_name'     => $this->input('route_name'),
                        'route_code'     => $this->input('route_code'),
                        'bus_number'     => $this->input('bus_number'),
                        'bus_reg_number' => $this->input('bus_reg_number'),
                        'capacity'       => (int) $this->input('capacity', '50'),
                        'driver_name'    => $this->input('driver_name'),
                        'driver_contact' => $this->input('driver_contact'),
                        'start_point'    => $this->input('start_point'),
                        'pickup_time'    => $this->input('pickup_time'),
                        'end_point'      => $this->input('end_point'),
                        'drop_time'      => $this->input('drop_time'),
                        'fare'           => (float) $this->input('fare', '18000.00'),
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
                } elseif ($action === 'request_change') {
                    $newRouteId = (int) $this->input('new_route_id');
                    $res        = $this->transportService->requestRouteChange($studentId, $newRouteId);

                    if ($res['success']) {
                        flash('success', $res['message']);
                        $this->redirect('/transport/routes');
                        return;
                    } else {
                        $error = $res['message'];
                    }
                } elseif ($canManage && $action === 'allocate_student') {
                    $stId    = (int) $this->input('student_id');
                    $routeId = (int) $this->input('transport_route_id');

                    if (!$stId || !$routeId) {
                        $error = 'Please select both a student and a route.';
                    } else {
                        $res = $this->transportService->allocateStudent($stId, $routeId);
                        if ($res['success']) {
                            $success = $res['message'];
                        } else {
                            $error = $res['message'];
                        }
                    }
                }
            }
        }

        $routes             = $this->transportService->getTransportRoutes(1);
        $allocations        = $this->transportService->getAllocations();
        $students           = $canManage ? ($this->studentService->getStudents(1, 1, 100)['data'] ?? []) : [];
        $activeSubscription = $this->transportService->getStudentSubscription($studentId);
        $changeRequests     = $this->transportService->getStudentChangeRequests($studentId);

        $this->render('Transport/views/routes', [
            'title'              => 'Transport & Bus Routes Management',
            'routes'             => $routes,
            'allocations'        => $allocations,
            'students'           => $students,
            'activeSubscription' => $activeSubscription,
            'changeRequests'     => $changeRequests,
            'canManage'          => $canManage,
            'error'              => $error,
            'success'            => $success,
        ], 'layout');
    }


    /**
     * Student — Transport Fee Payment & QR Code Page.
     */
    public function pay(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId    = $this->getStudentId();
        $subscription = $this->transportService->getStudentSubscription($studentId);

        if (!$subscription) {
            flash('error', 'Please select a transport bus route first before proceeding to payment.');
            $this->redirect('/transport/routes');
            return;
        }

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $txnId  = trim((string) $this->input('transaction_id'));
                $pDate  = $this->input('payment_date', date('Y-m-d'));
                $amount = (float) $this->input('amount', $subscription['annual_fee']);

                if (empty($txnId)) {
                    $error = 'Transaction ID / UTR Number is required for verification.';
                } else {
                    $res = $this->transportService->submitPayment($studentId, (int)$subscription['id'], $txnId, $pDate, $amount);
                    if ($res['success']) {
                        flash('success', $res['message']);
                        $this->redirect('/transport/routes');
                        return;
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $studentProfile = $this->studentService->getStudentProfile($studentId);

        $this->render('Transport/views/pay', [
            'title'          => 'Transport Fee Payment (QR Code)',
            'subscription'   => $subscription,
            'studentProfile' => $studentProfile,
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }

    /**
     * Student — Payment History.
     */
    public function history(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId      = $this->getStudentId();
        $payments       = $this->transportService->getStudentPayments($studentId);
        $routeHistory   = $this->transportService->getStudentRouteHistory($studentId);
        $changeRequests = $this->transportService->getStudentChangeRequests($studentId);

        $this->render('Transport/views/history', [
            'title'          => 'My Transport History & Payments',
            'payments'       => $payments,
            'routeHistory'   => $routeHistory,
            'changeRequests' => $changeRequests,
        ], 'layout');
    }

    /**
     * Student — View Official Transport Fee Receipt.
     */
    public function receipt(string $id): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $paymentId = (int) $id;
        $studentId = $this->getStudentId();

        $receipt = $this->transportService->getPaymentReceipt($paymentId, $studentId);
        if (!$receipt) {
            flash('error', 'Transport receipt not found.');
            $this->redirect('/transport/routes');
            return;
        }

        $this->render('Transport/views/receipt', [
            'title'   => 'KEC Transport Fee Payment Receipt',
            'receipt' => $receipt,
        ], 'layout');
    }

    /**
     * Resolve Student ID (handles Parent login linked ward ID).
     */
    private function getStudentId(): int
    {
        if (auth_role() === 'parent' && !empty($_SESSION['parent_ward_id'])) {
            return (int) $_SESSION['parent_ward_id'];
        }
        return (int) ($_SESSION['linked_id'] ?? 1);
    }


    /**
     * Transport Manager — Accounts & Transport Fees Ledger.
     */
    public function accounts(): void
    {
        Permission::enforce('transport.manage');
        $verifierId = auth_id() ?: 1;

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action    = $this->input('_action');
                $paymentId = (int) $this->input('payment_id');
                $remarks   = $this->input('remarks', 'Verified by Transport Desk');

                if ($action === 'verify_payment') {
                    if ($this->transportService->verifyPayment($paymentId, $verifierId, $remarks)) {
                        $success = 'Payment verified successfully! Student transport status is now ACTIVE.';
                    } else {
                        $error = 'Failed to verify payment.';
                    }
                } elseif ($action === 'reject_payment') {
                    if ($this->transportService->rejectPayment($paymentId, $verifierId, $remarks)) {
                        $success = 'Payment rejected.';
                    } else {
                        $error = 'Failed to reject payment.';
                    }
                }
            }
        }

        $paymentSum    = $this->transportService->getPaymentSummary();
        $studentStatus = $this->transportService->getStudentPaymentStatus();

        $this->render('Transport/views/accounts', [
            'title'         => 'Accounts & Transport Fees Ledger',
            'paymentSum'    => $paymentSum,
            'studentStatus' => $studentStatus,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * Dedicated Transport Change Requests Management (Transport Manager).
     */
    public function changeRequests(): void
    {
        Permission::enforce('transport.manage');
        $managerId = auth_id() ?: 1;

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action    = $this->input('_action');
                $requestId = (int) $this->input('request_id');

                if ($action === 'approve_request') {
                    if ($this->transportService->approveChangeRequest($requestId, $managerId)) {
                        $success = 'Transport route change request approved successfully! Old route seat freed and new bus seat allocated.';
                    } else {
                        $error = 'Failed to approve change request.';
                    }
                } elseif ($action === 'reject_request') {
                    $reason = trim((string) $this->input('rejection_reason', 'Seat unavailable / Request rejected'));
                    if (empty($reason)) {
                        $error = 'Rejection reason is required.';
                    } else {
                        if ($this->transportService->rejectChangeRequest($requestId, $managerId, $reason)) {
                            $success = 'Route change request rejected.';
                        } else {
                            $error = 'Failed to reject change request.';
                        }
                    }
                }
            }
        }

        $changeRequests = $this->transportService->getPendingChangeRequests();

        $this->render('Transport/views/change_requests', [
            'title'          => 'Transport Route & Bus Change Requests',
            'changeRequests' => $changeRequests,
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }
}


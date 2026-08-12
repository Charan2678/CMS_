<?php

declare(strict_types=1);

namespace App\Modules\Hostel\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Hostel\services\HostelService;
use App\Modules\Student\services\StudentService;

class HostelController extends Controller
{
    private HostelService $hostelService;
    private StudentService $studentService;

    public function __construct()
    {
        $this->hostelService  = new HostelService();
        $this->studentService = new StudentService();
    }

    /**
     * Warden Overview Dashboard ONLY.
     */
    public function index(): void
    {
        $canManage = Permission::has('hostel.manage') || Permission::has('hostel.allocate');

        $stats        = $this->hostelService->getStatistics(1);
        $blockSummary = $this->hostelService->getBlockSummary();

        $this->render('Hostel/views/index', [
            'title'        => 'Hostel Dashboard — Kuppam Engineering College',
            'stats'        => $stats,
            'blockSummary' => $blockSummary,
            'canManage'    => $canManage,
        ], 'layout');
    }

    /**
     * Dedicated Hostel Management Page (Rooms, Blocks & Student Allocation).
     */
    public function management(): void
    {
        $canManage   = Permission::has('hostel.manage') || Permission::has('hostel.allocate');
        $canAllocate = Permission::has('hostel.allocate') || Permission::has('hostel.manage');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('hostel.manage', 'hostel.allocate');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'create_block');

                if ($action === 'create_block') {
                    $data = [
                        'college_id'   => 1,
                        'name'         => $this->input('name'),
                        'gender_type'  => $this->input('gender_type', 'boys'),
                        'warden_name'  => $this->input('warden_name'),
                        'warden_phone' => $this->input('warden_phone'),
                    ];

                    if (empty($data['name'])) {
                        $error = 'Hostel block name is required.';
                    } else {
                        if ($this->hostelService->createHostelBlock($data)) {
                            $success = 'Hostel block added successfully.';
                        } else {
                            $error = 'Failed to add hostel block.';
                        }
                    }
                } elseif ($action === 'create_room') {
                    $data = [
                        'hostel_block_id'  => $this->input('hostel_block_id'),
                        'room_number'      => $this->input('room_number'),
                        'capacity'         => $this->input('capacity', '2'),
                        'fee_per_semester' => $this->input('fee_per_semester', '0.00'),
                    ];

                    if (empty($data['hostel_block_id']) || empty($data['room_number'])) {
                        $error = 'Hostel block and room number are required.';
                    } else {
                        if ($this->hostelService->createRoom($data)) {
                            $success = 'Hostel room added successfully.';
                        } else {
                            $error = 'Failed to create room.';
                        }
                    }
                } elseif ($action === 'allocate_student') {
                    $studentId = (int) $this->input('student_id');
                    $roomId    = (int) $this->input('hostel_room_id');

                    if (!$studentId || !$roomId) {
                        $error = 'Please select both a student and a room.';
                    } else {
                        $res = $this->hostelService->allocateStudent($studentId, $roomId);
                        if ($res['success']) {
                            $success = $res['message'];
                        } else {
                            $error = $res['message'];
                        }
                    }
                } elseif ($action === 'vacate_student') {
                    $allocId = (int) $this->input('allocation_id');
                    if ($this->hostelService->vacateAllocation($allocId)) {
                        $success = 'Student allocation vacated successfully.';
                    } else {
                        $error = 'Failed to vacate allocation.';
                    }
                } elseif ($action === 'verify_booking') {
                    $bookingId = (int) $this->input('booking_id');
                    $subAction = $this->input('sub_action'); // confirm or reject
                    $reason    = $this->input('rejection_reason');
                    $res = $this->hostelService->processWardenBookingAction($bookingId, $subAction, $reason, auth_id());
                    if ($res['success']) {
                        $success = $res['message'];
                    } else {
                        $error = $res['message'];
                    }
                } elseif ($action === 'update_qr_settings') {
                    $qrData = [
                        'qr_image'     => $this->input('qr_image', '/assets/images/hostel_qr.png'),
                        'upi_id'       => $this->input('upi_id', 'kec.hostel@upi'),
                        'payee_name'   => $this->input('payee_name', 'Kuppam Engineering College Hostel Account'),
                        'instructions' => $this->input('instructions', 'Scan with GPay/PhonePe to pay fee.'),
                    ];
                    if ($this->hostelService->updatePaymentSettings(1, $qrData)) {
                        $success = 'Hostel Payment QR & UPI settings updated successfully!';
                    } else {
                        $error = 'Failed to update payment settings.';
                    }
                }
            }
        }

        $blocks          = $this->hostelService->getHostelBlocks(1);
        $rooms           = $this->hostelService->getRooms(1);
        $allocations     = $this->hostelService->getHostelAllocations();
        $bookingRequests = $this->hostelService->getWardenBookingRequests(1);
        $paymentSettings = $this->hostelService->getPaymentSettings(1);
        $students        = $this->studentService->getStudents(1, 1, 100)['data'] ?? [];

        $this->render('Hostel/views/management', [
            'title'           => 'Hostel Management & Resident Allocations',
            'blocks'          => $blocks,
            'rooms'           => $rooms,
            'allocations'     => $allocations,
            'bookingRequests' => $bookingRequests,
            'paymentSettings' => $paymentSettings,
            'students'        => $students,
            'canManage'       => $canManage,
            'canAllocate'     => $canAllocate,
            'error'           => $error,
            'success'         => $success,
        ], 'layout');
    }

    /**
     * Student — Hostel & Room Booking Page.
     */
    public function booking(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId = $this->getStudentId();
        $error     = null;
        $success   = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', 'select_bed');

                if ($action === 'change_hostel') {
                    $bookingId = (int) $this->input('booking_id');
                    $res = $this->hostelService->cancelOrChangeBooking($studentId, $bookingId);
                    if ($res['success']) {
                        flash('success', $res['message']);
                        $this->redirect('/hostel/booking');
                        return;
                    } else {
                        $error = $res['message'];
                    }
                } else {
                    $blockId   = (int) $this->input('hostel_block_id');
                    $roomId    = (int) $this->input('hostel_room_id');
                    $bedNumber = (int) $this->input('bed_number');
                    $fee       = (float) $this->input('hostel_fee', '25000.00');

                    if (!$blockId || !$roomId || !$bedNumber) {
                        $error = 'Please select a hostel block, room, and an available bed.';
                    } else {
                        $res = $this->hostelService->createStudentBooking($studentId, $blockId, $roomId, $bedNumber, $fee);
                        if ($res['success']) {
                            flash('success', $res['message']);
                            $this->redirect('/hostel/pay');
                            return;
                        } else {
                            $error = $res['message'];
                        }
                    }
                }
            }
        }


        $activeBooking = $this->hostelService->getStudentActiveBooking($studentId);
        $hostels       = $this->hostelService->getAvailableHostels(1);
        
        $selectedBlockId = (int) ($this->input('block_id') ?: ($hostels[0]['id'] ?? 1));
        $rooms           = $this->hostelService->getRoomsForBlock($selectedBlockId);
        $studentProfile  = $this->studentService->getStudentProfile($studentId);

        $this->render('Hostel/views/booking', [
            'title'           => 'Hostel & Room Booking',
            'activeBooking'   => $activeBooking,
            'hostels'         => $hostels,
            'selectedBlockId' => $selectedBlockId,
            'rooms'           => $rooms,
            'studentProfile'  => $studentProfile,
            'error'           => $error,
            'success'         => $success,
        ], 'layout');
    }

    /**
     * Student — Hostel Fee Payment Page (QR Code).
     */
    public function pay(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId     = $this->getStudentId();
        $activeBooking = $this->hostelService->getStudentActiveBooking($studentId);

        if (!$activeBooking) {
            flash('error', 'Please select a hostel room and bed first before proceeding to payment.');
            $this->redirect('/hostel/booking');
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
                $amount = (float) $this->input('amount', (string) ($activeBooking['hostel_fee'] ?? '25000.00'));


                if (empty($txnId)) {
                    $error = 'Transaction ID / UTR Number is required for verification.';
                } else {
                    $res = $this->hostelService->submitBookingPayment($studentId, (int)$activeBooking['id'], $txnId, $pDate, $amount);
                    if ($res['success']) {
                        flash('success', $res['message']);
                        $this->redirect('/hostel/booking');
                        return;
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $paymentSettings = $this->hostelService->getPaymentSettings(1);
        $studentProfile  = $this->studentService->getStudentProfile($studentId);

        $this->render('Hostel/views/pay', [
            'title'           => 'Hostel Fee Payment (QR Code)',
            'booking'         => $activeBooking,
            'paymentSettings' => $paymentSettings,
            'studentProfile'  => $studentProfile,
            'error'           => $error,
            'success'         => $success,
        ], 'layout');
    }

    /**
     * Student — My Hostel Payment History.
     */
    public function history(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId = $this->getStudentId();
        $history   = $this->hostelService->getStudentBookingHistory($studentId);

        $this->render('Hostel/views/history', [
            'title'   => 'My Hostel Payments & Booking History',
            'history' => $history,
        ], 'layout');
    }

    /**
     * Parent Portal — Ward Hostel Details & Payment Status.
     */
    public function parentView(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $studentId     = $this->getStudentId();
        $hostelDetails = $this->hostelService->getWardHostelDetails($studentId);
        $history       = $this->hostelService->getStudentBookingHistory($studentId);
        $studentProfile= $this->studentService->getStudentProfile($studentId);

        $this->render('Hostel/views/parent', [
            'title'          => 'Ward Hostel Details & Fees',
            'hostelDetails'  => $hostelDetails,
            'history'        => $history,
            'studentProfile' => $studentProfile,
        ], 'layout');
    }

    /**
     * Helper to resolve active student ID (handles Parent login ward ID).
     */
    private function getStudentId(): int
    {
        if (auth_role() === 'parent' && !empty($_SESSION['parent_ward_id'])) {
            return (int) $_SESSION['parent_ward_id'];
        }
        return (int) ($_SESSION['linked_id'] ?? 1);
    }
}


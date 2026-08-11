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

    public function index(): void
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
                }
            }
        }

        $blocks      = $this->hostelService->getHostelBlocks(1);
        $rooms       = $this->hostelService->getRooms(1);
        $allocations = $this->hostelService->getHostelAllocations();
        $students    = $this->studentService->getStudents(1, 1, 100)['data'] ?? [];

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

        $myAllocation = null;
        if ($myStudentId) {
            foreach ($allocations as $a) {
                if ((int)$a['student_id'] === $myStudentId && $a['status'] === 'active') {
                    $myAllocation = $a;
                    break;
                }
            }
        }

        $this->render('Hostel/views/index', [
            'title'             => 'Hostel Management & Resident Allocations',
            'blocks'            => $blocks,
            'rooms'             => $rooms,
            'allocations'       => $allocations,
            'students'          => $students,
            'myAllocation'      => $myAllocation,
            'isStudentOrParent' => $isStudentOrParent,
            'canManage'         => $canManage,
            'canAllocate'       => $canAllocate,
            'error'             => $error,
            'success'           => $success,
        ], 'layout');
    }
}

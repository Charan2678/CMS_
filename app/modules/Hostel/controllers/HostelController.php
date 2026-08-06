<?php

declare(strict_types=1);

namespace App\Modules\Hostel\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Hostel\services\HostelService;

class HostelController extends Controller
{
    private HostelService $hostelService;

    public function __construct()
    {
        $this->hostelService = new HostelService();
    }

    public function index(): void
    {
        $canManage = Permission::has('hostel.manage');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('hostel.manage');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
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
            }
        }

        $blocks      = $this->hostelService->getHostelBlocks(1);
        $allocations = $this->hostelService->getHostelAllocations();

        $this->render('Hostel/views/index', [
            'title'       => 'Hostel Details & Management',
            'blocks'      => $blocks,
            'allocations' => $allocations,
            'canManage'   => $canManage,
            'error'       => $error,
            'success'     => $success,
        ], 'layout');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Transport\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Transport\services\TransportService;

class TransportController extends Controller
{
    private TransportService $transportService;

    public function __construct()
    {
        $this->transportService = new TransportService();
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
            }
        }

        $routes = $this->transportService->getTransportRoutes(1);

        $this->render('Transport/views/index', [
            'title'     => 'Transport & Bus Routes',
            'routes'    => $routes,
            'canManage' => $canManage,
            'error'     => $error,
            'success'   => $success,
        ], 'layout');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Settings\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Settings\services\NotificationService;

class NotificationController extends Controller
{
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    /**
     * Campus Announcements Board.
     */
    public function announcements(): void
    {
        Permission::enforce('notification.announcement');

        $error   = null;
        $success = null;

        $canBroadcast = in_array(auth_role(), ['admin', 'super_admin', 'faculty', 'hod', 'accounts_staff', 'librarian', 'hostel_warden', 'transport_manager', 'canteen_manager'], true);

        if ($this->isPost()) {
            if (!$canBroadcast) {
                $error = 'Access Denied: Students are not permitted to broadcast announcements.';
            } elseif (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'college_id'  => 1,
                    'title'       => $this->input('title'),
                    'content'     => $this->input('content'),
                    'target_role' => $this->input('target_role', 'all'),
                    'start_date'  => $this->input('start_date', date('Y-m-d')),
                    'end_date'    => $this->input('end_date', date('Y-m-d', strtotime('+30 days'))),
                ];

                if (empty($data['title']) || empty($data['content'])) {
                    $error = 'Title and content are required.';
                } else {
                    if ($this->notificationService->createAnnouncement($data)) {
                        $success = 'Announcement broadcasted successfully.';
                    } else {
                        $error = 'Failed to create announcement.';
                    }
                }
            }
        }

        $announcements = $this->notificationService->getAnnouncements(1);

        $this->render('Settings/views/announcements', [
            'title'         => 'Campus Announcements',
            'announcements' => $announcements,
            'canBroadcast'  => $canBroadcast,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * Immutable System Audit Logs Viewer.
     */
    public function auditLogs(): void
    {
        Permission::enforce('audit.view');

        $logs = $this->notificationService->getAuditLogs(100, 0);

        $this->render('Settings/views/audit_logs', [
            'title' => 'System Audit Logs',
            'logs'  => $logs,
        ], 'layout');
    }
}

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
     * Campus Announcements Board with Hierarchical Updates.
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
                    'college_id'            => 1,
                    'title'                 => $this->input('title'),
                    'content'               => $this->input('content'),
                    'target_role'           => $this->input('target_role'),
                    'target_department_id'  => $this->input('target_department_id'),
                    'target_semester_id'    => $this->input('target_semester_id'),
                    'hierarchy_level'       => $this->input('hierarchy_level', auth_role() === 'super_admin' ? 'chairman' : (auth_role() === 'admin' ? 'principal' : (auth_role() === 'hod' ? 'hod' : 'faculty'))),
                ];

                if (empty($data['title']) || empty($data['content'])) {
                    $error = 'Title and content are required.';
                } else {
                    if ($this->notificationService->createAnnouncement($data, (int) auth_id())) {
                        $success = 'Announcement broadcasted successfully across campus!';
                    } else {
                        $error = 'Failed to create announcement.';
                    }
                }
            }
        }

        $userRoleId = null;
        if (in_array(auth_role(), ['student', 'parent'])) {
            $userRoleId = auth_role() === 'student' ? 10 : 11;
        }

        $announcements = $this->notificationService->getAnnouncements(1, $userRoleId);

        $this->render('Settings/views/announcements', [
            'title'         => 'Campus Announcements & Circulars',
            'announcements' => $announcements,
            'canBroadcast'  => $canBroadcast,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * AJAX endpoint: Get unread notifications for currently logged in user.
     */
    public function getUnread(): void
    {
        if (!is_authenticated()) {
            $this->json(['count' => 0, 'items' => []], 401);
            return;
        }

        $userId = (int) auth_id();
        $res = $this->notificationService->getUnreadNotifications($userId);
        $this->json($res);
    }

    /**
     * AJAX endpoint: Mark single notification as read.
     */
    public function markRead(int $id): void
    {
        if (!is_authenticated()) {
            $this->json(['success' => false, 'error' => 'Unauthenticated'], 401);
            return;
        }

        $userId = (int) auth_id();
        $ok = $this->notificationService->markAsRead($id, $userId);
        $this->json(['success' => $ok]);
    }

    /**
     * AJAX endpoint: Mark all notifications as read.
     */
    public function markAllRead(): void
    {
        if (!is_authenticated()) {
            $this->json(['success' => false, 'error' => 'Unauthenticated'], 401);
            return;
        }

        $userId = (int) auth_id();
        $ok = $this->notificationService->markAllAsRead($userId);
        $this->json(['success' => $ok]);
    }
}

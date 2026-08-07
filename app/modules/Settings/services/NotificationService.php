<?php

declare(strict_types=1);

namespace App\Modules\Settings\services;

use PDO;

class NotificationService
{
    /**
     * Create an in-app notification for a target user.
     */
    public function notify(int $userId, string $title, string $message, ?string $link = null, string $type = 'info'): bool
    {
        try {
            $stmt = db()->prepare('
                INSERT INTO notifications (
                    college_id, user_id, title, message, type, is_read, created_at
                ) VALUES (
                    1, :user_id, :title, :message, :type, 0, NOW()
                )
            ');

            return $stmt->execute([
                ':user_id' => $userId,
                ':title'   => substr($title, 0, 150),
                ':message' => $message,
                ':type'    => $type,
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get unread notification list & count for user.
     */
    public function getUnreadNotifications(int $userId): array
    {
        try {
            $stmt = db()->prepare('
                SELECT * FROM notifications 
                WHERE user_id = :user_id AND is_read = 0 
                ORDER BY created_at DESC 
                LIMIT 10
            ');
            $stmt->execute([':user_id' => $userId]);
            $items = $stmt->fetchAll() ?: [];

            $cntStmt = db()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0');
            $cntStmt->execute([':user_id' => $userId]);
            $count = (int) $cntStmt->fetchColumn();

            return [
                'count' => $count,
                'items' => $items,
            ];
        } catch (\Exception $e) {
            return [
                'count' => 0,
                'items' => [],
            ];
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        try {
            $stmt = db()->prepare('
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE id = :id AND user_id = :user_id
            ');

            return $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get active announcements for a college.
     */
    public function getAnnouncements(int $collegeId = 1): array
    {
        try {
            $stmt = db()->prepare('
                SELECT a.*, 
                       u.username AS publisher_name,
                       COALESCE(r.name, "Everyone") AS target_role
                FROM announcements a
                LEFT JOIN users u ON u.id = a.published_by
                LEFT JOIN roles r ON r.id = a.target_role
                WHERE a.college_id = :college_id
                ORDER BY a.created_at DESC
            ');
            $stmt->execute([':college_id' => $collegeId]);
            return $stmt->fetchAll() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Create a new campus announcement.
     */
    public function createAnnouncement(array $data): bool
    {
        try {
            $publishedBy = auth_id() ?? 1;
            $collegeId   = (int) ($data['college_id'] ?? 1);
            $title       = trim((string) ($data['title'] ?? ''));
            $content     = trim((string) ($data['content'] ?? ''));
            $targetRoleStr = strtolower(trim((string) ($data['target_role'] ?? 'all')));
            $startDate   = !empty($data['start_date']) ? $data['start_date'] . ' 00:00:00' : date('Y-m-d H:i:s');
            $endDate     = !empty($data['end_date']) ? $data['end_date'] . ' 23:59:59' : date('Y-m-d H:i:s', strtotime('+30 days'));

            $targetRoleId = null;
            if ($targetRoleStr !== 'all' && $targetRoleStr !== '') {
                if (is_numeric($targetRoleStr)) {
                    $targetRoleId = (int) $targetRoleStr;
                } else {
                    $roleStmt = db()->prepare('SELECT id FROM roles WHERE LOWER(code) = :code OR LOWER(name) LIKE :name LIMIT 1');
                    $roleStmt->execute([':code' => $targetRoleStr, ':name' => '%' . $targetRoleStr . '%']);
                    $roleRow = $roleStmt->fetch();
                    if ($roleRow) {
                        $targetRoleId = (int) $roleRow['id'];
                    }
                }
            }

            $stmt = db()->prepare('
                INSERT INTO announcements (
                    college_id, title, content, target_role, published_by, publish_at, expire_at, status, created_at
                ) VALUES (
                    :college_id, :title, :content, :target_role, :published_by, :publish_at, :expire_at, "published", NOW()
                )
            ');

            return $stmt->execute([
                ':college_id'   => $collegeId,
                ':title'        => $title,
                ':content'      => $content,
                ':target_role'  => $targetRoleId,
                ':published_by' => $publishedBy,
                ':publish_at'   => $startDate,
                ':expire_at'    => $endDate,
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get system audit logs.
     */
    public function getAuditLogs(int $limit = 100, int $offset = 0): array
    {
        try {
            $stmt = db()->prepare('
                SELECT al.*, u.username
                FROM audit_logs al
                LEFT JOIN users u ON u.id = al.user_id
                ORDER BY al.created_at DESC
                LIMIT :limit OFFSET :offset
            ');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }
}

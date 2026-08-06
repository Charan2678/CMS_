<?php

declare(strict_types=1);

namespace App\Modules\Settings\services;

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
                    user_id, title, message, link, type, is_read, created_at
                ) VALUES (
                    :user_id, :title, :message, :link, :type, 0, NOW()
                )
            ');

            return $stmt->execute([
                ':user_id' => $userId,
                ':title'   => substr($title, 0, 150),
                ':message' => $message,
                ':link'    => $link,
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
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $stmt = db()->prepare('
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = :id AND user_id = :user_id
        ');

        return $stmt->execute([':id' => $notificationId, ':user_id' => $userId]);
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Settings\services;

use PDO;

class NotificationService
{
    /**
     * Create an in-app notification for a target user.
     */
    public function notify(
        int $userId,
        string $title,
        string $message,
        ?string $link = null,
        string $type = 'info',
        string $priority = 'normal',
        string $sourceHierarchy = 'system'
    ): bool {
        try {
            $stmt = db()->prepare('
                INSERT INTO notifications (
                    college_id, user_id, title, message, link, type, priority, source_hierarchy, is_read, created_at
                ) VALUES (
                    1, :user_id, :title, :message, :link, :type, :priority, :source_hierarchy, 0, NOW()
                )
            ');

            return $stmt->execute([
                ':user_id'          => $userId,
                ':title'            => substr($title, 0, 150),
                ':message'          => $message,
                ':link'             => $link,
                ':type'             => in_array($type, ['info', 'warning', 'success', 'alert']) ? $type : 'info',
                ':priority'         => in_array($priority, ['low', 'normal', 'high', 'urgent']) ? $priority : 'normal',
                ':source_hierarchy' => in_array($sourceHierarchy, ['chairman', 'principal', 'hod', 'admin', 'system']) ? $sourceHierarchy : 'system',
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Send targeted notification to all students (and their linked parents) in a department/semester.
     */
    public function notifyDepartment(
        int $departmentId,
        string $title,
        string $message,
        ?string $link = null,
        ?int $semesterId = null,
        string $type = 'info',
        string $sourceHierarchy = 'hod'
    ): int {
        try {
            $sql = '
                SELECT u.id AS student_user_id, pu.id AS parent_user_id
                FROM students s
                JOIN users u ON u.linked_type = "student" AND u.linked_id = s.id
                LEFT JOIN guardians g ON g.student_id = s.id
                LEFT JOIN users pu ON pu.linked_type = "parent" AND pu.linked_id = g.id
                WHERE s.department_id = :dept_id
            ';
            $params = [':dept_id' => $departmentId];

            if ($semesterId !== null) {
                $sql .= ' AND s.current_semester_id = :sem_id';
                $params[':sem_id'] = $semesterId;
            }

            $stmt = db()->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll() ?: [];

            $sent = 0;
            foreach ($rows as $r) {
                if (!empty($r['student_user_id'])) {
                    $this->notify((int) $r['student_user_id'], $title, $message, $link, $type, 'normal', $sourceHierarchy);
                    $sent++;
                }
                if (!empty($r['parent_user_id'])) {
                    $this->notify((int) $r['parent_user_id'], "[Ward Alert] " . $title, $message, $link, $type, 'normal', $sourceHierarchy);
                    $sent++;
                }
            }

            return $sent;
        } catch (\Throwable $e) {
            return 0;
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
                LIMIT 15
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
        } catch (\Throwable $e) {
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
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Mark all unread notifications as read for a user.
     */
    public function markAllAsRead(int $userId): bool
    {
        try {
            $stmt = db()->prepare('
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE user_id = :user_id AND is_read = 0
            ');

            return $stmt->execute([':user_id' => $userId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Get active announcements for a college.
     */
    public function getAnnouncements(int $collegeId = 1, ?int $userRoleId = null, ?int $departmentId = null): array
    {
        try {
            $sql = '
                SELECT a.*, 
                       u.username AS publisher_name,
                       COALESCE(r.name, "Everyone") AS target_role,
                       d.name AS target_department_name
                FROM announcements a
                LEFT JOIN users u ON u.id = a.published_by
                LEFT JOIN roles r ON r.id = a.target_role
                LEFT JOIN departments d ON d.id = a.target_department_id
                WHERE a.college_id = :college_id AND a.is_active = 1
            ';
            $params = [':college_id' => $collegeId];

            if ($userRoleId !== null) {
                $sql .= ' AND (a.target_role IS NULL OR a.target_role = :role_id)';
                $params[':role_id'] = $userRoleId;
            }

            if ($departmentId !== null) {
                $sql .= ' AND (a.target_department_id IS NULL OR a.target_department_id = :dept_id)';
                $params[':dept_id'] = $departmentId;
            }

            $sql .= ' ORDER BY FIELD(a.hierarchy_level, "chairman", "principal", "admin", "hod", "faculty"), a.created_at DESC';

            $stmt = db()->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Create an announcement with hierarchical targeting.
     */
    public function createAnnouncement(array $data, int $userId): bool
    {
        try {
            $stmt = db()->prepare('
                INSERT INTO announcements (
                    college_id, title, content, target_role, target_department_id, target_semester_id,
                    hierarchy_level, published_by, publish_at, is_active
                ) VALUES (
                    :college_id, :title, :content, :target_role, :target_department_id, :target_semester_id,
                    :hierarchy_level, :published_by, NOW(), 1
                )
            ');

            return $stmt->execute([
                ':college_id'            => $data['college_id'] ?? 1,
                ':title'                 => $data['title'],
                ':content'               => $data['content'],
                ':target_role'           => !empty($data['target_role']) ? (int) $data['target_role'] : null,
                ':target_department_id'  => !empty($data['target_department_id']) ? (int) $data['target_department_id'] : null,
                ':target_semester_id'    => !empty($data['target_semester_id']) ? (int) $data['target_semester_id'] : null,
                ':hierarchy_level'       => $data['hierarchy_level'] ?? 'admin',
                ':published_by'          => $userId,
            ]);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

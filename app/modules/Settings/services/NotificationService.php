<?php

declare(strict_types=1);

namespace App\Modules\Settings\services;

use PDO;

class NotificationService
{
    // ─── Announcements ────────────────────────────────────────
    public function getAnnouncements(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT a.*, u.username AS publisher_name
            FROM announcements a
            LEFT JOIN users u ON u.id = a.published_by
            WHERE a.college_id = :college_id
            ORDER BY a.id DESC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createAnnouncement(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO announcements (
                college_id, title, content, target_role, start_date, end_date, is_active, published_by, created_at
            ) VALUES (
                :college_id, :title, :content, :target_role, :start_date, :end_date, 1, :published_by, NOW()
            )
        ');

        $publishedBy = auth_id() ?? 1;

        $res = $stmt->execute([
            ':college_id'   => $data['college_id'] ?? 1,
            ':title'        => $data['title'],
            ':content'      => $data['content'],
            ':target_role'  => $data['target_role'] ?? 'all',
            ':start_date'   => $data['start_date'] ?? date('Y-m-d'),
            ':end_date'     => $data['end_date'] ?? date('Y-m-d', strtotime('+30 days')),
            ':published_by' => $publishedBy,
        ]);

        if ($res) {
            audit_log('Created Announcement: ' . $data['title'], 'announcements', null, $data);
        }

        return $res;
    }

    // ─── Audit Logs ───────────────────────────────────────────
    public function getAuditLogs(int $limit = 50, int $offset = 0): array
    {
        $stmt = db()->prepare('
            SELECT al.*, u.username
            FROM audit_logs al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.id DESC
            LIMIT :lim OFFSET :off
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}

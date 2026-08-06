<?php

declare(strict_types=1);

namespace App\Modules\Hostel\services;

use PDO;

class HostelService
{
    public function getHostelBlocks(int $collegeId = 1): array
    {
        $stmt = db()->prepare('SELECT * FROM hostel_blocks WHERE college_id = :college_id ORDER BY id DESC');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createHostelBlock(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO hostel_blocks (college_id, name, gender_type, warden_name, warden_phone, status)
            VALUES (:college_id, :name, :gender_type, :warden_name, :warden_phone, 1)
        ');
        return $stmt->execute([
            ':college_id'   => $data['college_id'] ?? 1,
            ':name'         => $data['name'],
            ':gender_type'  => $data['gender_type'] ?? 'boys',
            ':warden_name'  => $data['warden_name'] ?? null,
            ':warden_phone' => $data['warden_phone'] ?? null,
        ]);
    }

    public static function getHostelAllocations(): array
    {
        $stmt = db()->prepare('
            SELECT ha.*, hb.name AS block_name, hr.room_number, s.roll_number, s.first_name, s.last_name
            FROM hostel_allocations ha
            JOIN hostel_rooms hr ON hr.id = ha.hostel_room_id
            JOIN hostel_blocks hb ON hb.id = hr.hostel_block_id
            JOIN students s ON s.id = ha.student_id
            ORDER BY ha.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}

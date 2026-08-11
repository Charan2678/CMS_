<?php

declare(strict_types=1);

namespace App\Modules\Hostel\services;

use PDO;

class HostelService
{
    public function getHostelBlocks(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT hb.*, hb.type AS gender_type, s.first_name AS warden_first, s.last_name AS warden_last, s.mobile AS warden_phone
            FROM hostel_blocks hb 
            LEFT JOIN staff s ON s.id = hb.warden_id
            WHERE hb.college_id = :college_id 
            ORDER BY hb.id DESC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createHostelBlock(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO hostel_blocks (college_id, name, type, total_rooms, status)
            VALUES (:college_id, :name, :type, 0, 1)
        ');
        return $stmt->execute([
            ':college_id'   => $data['college_id'] ?? 1,
            ':name'         => $data['name'],
            ':type'         => in_array($data['gender_type'] ?? $data['type'] ?? 'boys', ['boys', 'girls']) ? ($data['gender_type'] ?? $data['type'] ?? 'boys') : 'boys',
        ]);
    }

    public function getRooms(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT hr.*, hb.name AS block_name, hb.type AS gender_type,
                   (SELECT COUNT(*) FROM hostel_allocations ha WHERE ha.hostel_room_id = hr.id AND ha.status = "active") AS occupied_beds
            FROM hostel_rooms hr
            JOIN hostel_blocks hb ON hb.id = hr.hostel_block_id
            WHERE hb.college_id = :college_id
            ORDER BY hb.name ASC, hr.room_number ASC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createRoom(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO hostel_rooms (hostel_block_id, room_number, floor, capacity, type, monthly_rent, status)
            VALUES (:block_id, :room_number, :floor, :capacity, :type, :monthly_rent, "available")
        ');

        $cap = max(1, (int) ($data['capacity'] ?? 2));
        $type = match($cap) {
            1 => 'single',
            2 => 'double',
            3 => 'triple',
            default => 'dormitory',
        };

        return $stmt->execute([
            ':block_id'     => (int) $data['hostel_block_id'],
            ':room_number'  => $data['room_number'],
            ':floor'        => (int) ($data['floor'] ?? 1),
            ':capacity'     => $cap,
            ':type'         => $type,
            ':monthly_rent' => (float) ($data['fee_per_semester'] ?? $data['monthly_rent'] ?? 3000.00),
        ]);
    }

    public function allocateStudent(int $studentId, int $roomId): array
    {
        // 1. Check if student already has an active allocation
        $chkStmt = db()->prepare('SELECT id FROM hostel_allocations WHERE student_id = :sid AND status = "active" LIMIT 1');
        $chkStmt->execute([':sid' => $studentId]);
        if ($chkStmt->fetch()) {
            return ['success' => false, 'message' => 'This student is already actively allocated to a hostel room. Vacate the previous allocation first.'];
        }

        // 2. Check room capacity
        $rStmt = db()->prepare('
            SELECT hr.capacity, 
                   (SELECT COUNT(*) FROM hostel_allocations ha WHERE ha.hostel_room_id = hr.id AND ha.status = "active") AS occupied
            FROM hostel_rooms hr WHERE hr.id = :rid LIMIT 1
        ');
        $rStmt->execute([':rid' => $roomId]);
        $room = $rStmt->fetch();

        if (!$room) {
            return ['success' => false, 'message' => 'Hostel room not found.'];
        }

        if ((int)$room['occupied'] >= (int)$room['capacity']) {
            return ['success' => false, 'message' => 'Cannot allocate: This room is already at full capacity (' . $room['occupied'] . '/' . $room['capacity'] . ' beds occupied).'];
        }

        $nextBed = ((int)$room['occupied']) + 1;

        // 3. Allocate
        $insStmt = db()->prepare('
            INSERT INTO hostel_allocations (
                student_id, hostel_room_id, academic_year_id, bed_number, allotted_date, status, allotted_by
            ) VALUES (
                :student_id, :room_id, 1, :bed_number, CURDATE(), "active", :allotted_by
            )
        ');
        $ok = $insStmt->execute([
            ':student_id'  => $studentId,
            ':room_id'     => $roomId,
            ':bed_number'  => $nextBed,
            ':allotted_by' => auth_id() ?? 1,
        ]);

        return [
            'success' => $ok,
            'message' => $ok ? 'Student successfully allocated to hostel room!' : 'Failed to allocate student.',
        ];
    }

    public function vacateAllocation(int $allocationId): bool
    {
        $stmt = db()->prepare('
            UPDATE hostel_allocations
            SET status = "vacated", vacated_date = CURDATE()
            WHERE id = :id
        ');
        return $stmt->execute([':id' => $allocationId]);
    }

    public static function getHostelAllocations(): array
    {
        $stmt = db()->prepare('
            SELECT ha.*, hb.name AS block_name, hr.room_number, hr.capacity, s.roll_number, s.first_name, s.last_name, s.mobile
            FROM hostel_allocations ha
            JOIN hostel_rooms hr ON hr.id = ha.hostel_room_id
            JOIN hostel_blocks hb ON hb.id = hr.hostel_block_id
            JOIN students s ON s.id = ha.student_id
            ORDER BY ha.status ASC, ha.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}

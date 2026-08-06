<?php

declare(strict_types=1);

namespace App\Modules\Transport\services;

use PDO;

class TransportService
{
    public function getTransportRoutes(int $collegeId = 1): array
    {
        $stmt = db()->prepare('SELECT * FROM transport_routes WHERE college_id = :college_id ORDER BY id DESC');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createTransportRoute(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO transport_routes (college_id, route_name, route_code, start_point, end_point, fare, status)
            VALUES (:college_id, :route_name, :route_code, :start_point, :end_point, :fare, 1)
        ');
        return $stmt->execute([
            ':college_id'  => $data['college_id'] ?? 1,
            ':route_name'  => $data['route_name'],
            ':route_code'  => $data['route_code'] ?? strtolower(str_replace(' ', '_', $data['route_name'])),
            ':start_point' => $data['start_point'] ?? 'Campus',
            ':end_point'   => $data['end_point'] ?? 'City Center',
            ':fare'        => (float) ($data['fare'] ?? 0.00),
        ]);
    }
}

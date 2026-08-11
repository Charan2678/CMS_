<?php

declare(strict_types=1);

namespace App\Modules\Transport\services;

use PDO;

class TransportService
{
    public function getTransportRoutes(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT tr.*, tr.monthly_fee AS fare, v.registration_number AS vehicle_number, v.capacity,
                   (SELECT COUNT(*) FROM transport_allocations ta WHERE ta.route_id = tr.id AND ta.status = "active") AS active_riders
            FROM transport_routes tr 
            LEFT JOIN vehicles v ON v.id = tr.vehicle_id
            WHERE tr.college_id = :college_id 
            ORDER BY tr.id DESC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createTransportRoute(array $data): bool
    {
        // Ensure vehicle exists
        $vehicleId = !empty($data['vehicle_id']) ? (int) $data['vehicle_id'] : (int) db()->query('SELECT id FROM vehicles LIMIT 1')->fetchColumn();
        if (!$vehicleId) {
            $vStmt = db()->prepare('
                INSERT INTO vehicles (college_id, registration_number, type, capacity, driver_name, status)
                VALUES (1, :reg, "bus", 45, "Main Campus Driver", "active")
            ');
            $vStmt->execute([':reg' => 'AP-04-TX-' . rand(1000, 9999)]);
            $vehicleId = (int) db()->lastInsertId();
        }

        $stmt = db()->prepare('
            INSERT INTO transport_routes (college_id, vehicle_id, route_name, start_point, end_point, distance_km, monthly_fee, status)
            VALUES (:college_id, :vehicle_id, :route_name, :start_point, :end_point, :distance_km, :monthly_fee, 1)
        ');
        return $stmt->execute([
            ':college_id'   => $data['college_id'] ?? 1,
            ':vehicle_id'   => $vehicleId,
            ':route_name'   => $data['route_name'],
            ':start_point'  => $data['start_point'] ?? 'Kuppam Town',
            ':end_point'    => $data['end_point'] ?? 'Campus Main Gate',
            ':distance_km'  => (float) ($data['distance_km'] ?? 15.0),
            ':monthly_fee'  => (float) ($data['fare'] ?? $data['monthly_fee'] ?? 1500.00),
        ]);
    }

    public function getAllocations(): array
    {
        $stmt = db()->prepare('
            SELECT ta.*, tr.route_name, tr.monthly_fee AS fare, s.roll_number, s.first_name, s.last_name, s.mobile
            FROM transport_allocations ta
            JOIN transport_routes tr ON tr.id = ta.route_id
            JOIN students s ON s.id = ta.student_id
            ORDER BY ta.status ASC, ta.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    public function allocateStudent(int $studentId, int $routeId, ?string $pickupPoint = null): array
    {
        $chk = db()->prepare('SELECT id FROM transport_allocations WHERE student_id = :sid AND status = "active" LIMIT 1');
        $chk->execute([':sid' => $studentId]);
        if ($chk->fetch()) {
            return ['success' => false, 'message' => 'Student is already actively subscribed to a bus route.'];
        }

        $stmt = db()->prepare('
            INSERT INTO transport_allocations (
                route_id, student_id, academic_year_id, pickup_point, allotted_date, status, created_at
            ) VALUES (
                :route_id, :student_id, 1, :pickup_point, CURDATE(), "active", NOW()
            )
        ');
        $ok = $stmt->execute([
            ':route_id'     => $routeId,
            ':student_id'   => $studentId,
            ':pickup_point' => $pickupPoint ?? 'Campus Gate',
        ]);

        return ['success' => $ok, 'message' => $ok ? 'Student subscribed to bus transport route successfully!' : 'Allocation failed.'];
    }

    public function vacateAllocation(int $allocId): bool
    {
        $stmt = db()->prepare('UPDATE transport_allocations SET status = "cancelled" WHERE id = :id');
        return $stmt->execute([':id' => $allocId]);
    }
}

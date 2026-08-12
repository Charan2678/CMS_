<?php

declare(strict_types=1);

namespace App\Modules\Transport\services;

use PDO;

class TransportService
{
    /**
     * Get all active transport routes with vehicle details, stops, timings, and active rider counts.
     */
    public function getTransportRoutes(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT tr.*, tr.monthly_fee AS fare, v.id AS vehicle_id, v.registration_number AS vehicle_number, 
                   v.type AS vehicle_type, v.capacity, v.driver_name, v.driver_mobile,
                   (SELECT COUNT(*) FROM transport_allocations ta WHERE ta.route_id = tr.id AND ta.status = "active") AS active_riders
            FROM transport_routes tr 
            LEFT JOIN vehicles v ON v.id = tr.vehicle_id
            WHERE tr.college_id = :college_id 
            ORDER BY tr.id ASC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        $routes = $stmt->fetchAll() ?: [];

        // Enrich routes with standard institutional stops and schedule timings
        foreach ($routes as &$r) {
            $r['stops'] = $this->getRouteStops((int) $r['id'], $r['start_point'], $r['end_point']);
            $r['timings'] = [
                'morning_departure' => '07:45 AM',
                'campus_arrival'    => '08:35 AM',
                'evening_departure' => '04:30 PM',
            ];
            $r['available_seats'] = max(0, ((int)($r['capacity'] ?: 45)) - (int)$r['active_riders']);
        }
        unset($r);

        return $routes;
    }

    /**
     * Helper to retrieve defined stops for a route.
     */
    public function getRouteStops(int $routeId, ?string $startPoint = null, ?string $endPoint = null): array
    {
        $defaultStops = [
            1 => ['Main Road', 'RTC Bus Stand', 'Old Town Circle', 'Railway Gate', 'College Campus Gate 1'],
            2 => ['Palamaner Circle', 'Bypass Junction', 'Gudupalli Cross', 'College Campus Gate 2'],
            3 => ['V.Kota Market', 'Checkpost', 'Sub-Station Stop', 'College Campus Gate 1'],
        ];

        if (isset($defaultStops[$routeId])) {
            return $defaultStops[$routeId];
        }

        $stops = [];
        if (!empty($startPoint)) $stops[] = $startPoint;
        $stops[] = 'Main Road Junction';
        $stops[] = 'RTC Bus Stand';
        if (!empty($endPoint)) $stops[] = $endPoint;
        return array_unique($stops);
    }

    /**
     * Save student route and pickup stop selection before payment.
     */
    public function saveStudentRouteSelection(int $studentId, int $routeId, string $pickupPoint): array
    {
        $chk = db()->prepare('SELECT id, status FROM transport_allocations WHERE student_id = :sid ORDER BY id DESC LIMIT 1');
        $chk->execute([':sid' => $studentId]);
        $existing = $chk->fetch();

        if ($existing) {
            $stmt = db()->prepare('
                UPDATE transport_allocations 
                SET route_id = :rid, pickup_point = :pp, status = "active" 
                WHERE id = :id
            ');
            $ok = $stmt->execute([
                ':rid' => $routeId,
                ':pp'  => $pickupPoint,
                ':id'  => $existing['id'],
            ]);
        } else {
            $stmt = db()->prepare('
                INSERT INTO transport_allocations (
                    route_id, student_id, academic_year_id, pickup_point, allotted_date, status, created_at
                ) VALUES (
                    :rid, :sid, 1, :pp, CURDATE(), "active", NOW()
                )
            ');
            $ok = $stmt->execute([
                ':rid' => $routeId,
                ':sid' => $studentId,
                ':pp'  => $pickupPoint,
            ]);
        }

        if ($ok) {
            $this->getOrCreateBusPass($studentId);
        }

        return ['success' => $ok, 'message' => $ok ? 'Route and pickup stop selected successfully!' : 'Failed to save route selection.'];
    }

    /**
     * Create a new transport route.
     */
    public function createTransportRoute(array $data): bool
    {
        $vehicleId = !empty($data['vehicle_id']) ? (int) $data['vehicle_id'] : (int) db()->query('SELECT id FROM vehicles LIMIT 1')->fetchColumn();
        if (!$vehicleId) {
            $vStmt = db()->prepare('
                INSERT INTO vehicles (college_id, registration_number, type, capacity, driver_name, status)
                VALUES (1, :reg, "bus", 50, "Campus Transport Driver", "active")
            ');
            $vStmt->execute([
                ':reg' => 'BUS-' . rand(10, 99) . ' (AP-04-TX-' . rand(1000, 9999) . ')',
            ]);
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
            ':monthly_fee'  => (float) ($data['fare'] ?? $data['monthly_fee'] ?? 18000.00),
        ]);
    }

    /**
     * Get all student route allocations.
     */
    public function getAllocations(): array
    {
        $stmt = db()->prepare('
            SELECT ta.*, tr.route_name, tr.monthly_fee AS fare, v.registration_number AS bus_number,
                   s.roll_number, s.first_name, s.last_name, s.mobile,
                   dept.name AS department_name, d.name AS degree_name,
                   tbp.id AS pass_id, tbp.pass_number, tbp.status AS pass_status, tbp.valid_until
            FROM transport_allocations ta
            JOIN transport_routes tr ON tr.id = ta.route_id
            LEFT JOIN vehicles v ON v.id = tr.vehicle_id
            JOIN students s ON s.id = ta.student_id
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            LEFT JOIN departments dept ON dept.id = sa.department_id
            LEFT JOIN courses d ON d.id = sa.course_id
            LEFT JOIN transport_bus_passes tbp ON tbp.allocation_id = ta.id
            ORDER BY ta.status ASC, ta.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Allocate a student to a route and initialize pass evaluation.
     */
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

        if ($ok) {
            // Evaluate pass right after allocation
            $this->getOrCreateBusPass($studentId);
        }

        return ['success' => $ok, 'message' => $ok ? 'Student subscribed to bus transport route successfully!' : 'Allocation failed.'];
    }

    /**
     * Vacate/Cancel a student allocation and cancel pass.
     */
    public function vacateAllocation(int $allocId): bool
    {
        $stmt = db()->prepare('UPDATE transport_allocations SET status = "cancelled" WHERE id = :id');
        $ok = $stmt->execute([':id' => $allocId]);
        if ($ok) {
            db()->prepare('UPDATE transport_bus_passes SET status = "cancelled", updated_at = NOW() WHERE allocation_id = :id')->execute([':id' => $allocId]);
        }
        return $ok;
    }

    /**
     * Get a student's active transport subscription with bus and route details.
     */
    public function getStudentActiveSubscription(int $studentId): ?array
    {
        $stmt = db()->prepare('
            SELECT ta.*, tr.id AS route_id, tr.route_name, tr.start_point, tr.end_point, tr.monthly_fee AS fare,
                   v.id AS vehicle_id, v.registration_number AS bus_number, v.driver_name, v.driver_mobile
            FROM transport_allocations ta
            JOIN transport_routes tr ON tr.id = ta.route_id
            LEFT JOIN vehicles v ON v.id = tr.vehicle_id
            WHERE ta.student_id = :sid AND ta.status = "active"
            ORDER BY ta.id DESC
            LIMIT 1
        ');
        $stmt->execute([':sid' => $studentId]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }

        // If no active allocation exists in transport_allocations, but student has paid for transport fee,
        // automatically auto-enroll student into active default route
        $payment = $this->checkTransportFeePayment($studentId, 1);
        if ($payment && !empty($payment['is_paid'])) {
            $defaultRouteId = (int) (db()->query('SELECT id FROM transport_routes WHERE status = 1 ORDER BY id ASC LIMIT 1')->fetchColumn() ?: 1);
            $this->saveStudentRouteSelection($studentId, $defaultRouteId, 'Main Road');
            $stmt->execute([':sid' => $studentId]);
            $autoRow = $stmt->fetch();
            if ($autoRow) {
                return $autoRow;
            }
        }

        return null;
    }

    /**
     * Check if the student has a confirmed transport fee payment in the existing Fee/Payment system.
     */
    public function checkTransportFeePayment(int $studentId, int $routeId): ?array
    {
        // 1. Check in payments table
        $stmt = db()->prepare('
            SELECT p.*, p.amount_paid AS paid_amount, p.payment_date AS confirmed_date, p.transaction_id AS txn_ref
            FROM payments p
            WHERE p.student_id = :sid 
              AND (p.fee_category_type = "transport" OR p.remarks LIKE "%transport%" OR p.remarks LIKE "%bus%" OR p.remarks LIKE "%route%")
            ORDER BY p.id DESC
            LIMIT 1
        ');
        $stmt->execute([':sid' => $studentId]);
        $p = $stmt->fetch();
        if ($p) {
            return [
                'is_paid'        => true,
                'payment_id'     => (int) $p['id'],
                'amount_paid'    => (float) $p['amount_paid'],
                'payment_date'   => $p['payment_date'],
                'transaction_id' => $p['transaction_id'] ?? $p['utr_reference'] ?? ('TXN-PAY-' . $p['id']),
                'method'         => $p['payment_method'] ?? 'Online / QR',
            ];
        }

        // 2. Check in payment_gateway_transactions
        $userStmt = db()->prepare('SELECT id FROM users WHERE linked_type = "student" AND linked_id = :sid LIMIT 1');
        $userStmt->execute([':sid' => $studentId]);
        $userId = $userStmt->fetchColumn();

        if ($userId) {
            $gtStmt = db()->prepare('
                SELECT * FROM payment_gateway_transactions
                WHERE user_id = :uid 
                  AND (fee_type = "transport" OR fee_type = "transport_change" OR fee_type LIKE "%transport%")
                  AND status IN ("captured", "authorized")
                ORDER BY id DESC
                LIMIT 1
            ');
            $gtStmt->execute([':uid' => $userId]);
            $gt = $gtStmt->fetch();
            if ($gt) {
                return [
                    'is_paid'        => true,
                    'payment_id'     => null,
                    'amount_paid'    => (float) $gt['amount'],
                    'payment_date'   => date('Y-m-d', strtotime($gt['created_at'])),
                    'transaction_id' => $gt['utr_reference'] ?? $gt['gateway_payment_id'] ?? ('TXN-GT-' . $gt['id']),
                    'method'         => $gt['gateway'] ?? 'Online Payment',
                ];
            }
        }

        // 3. Check in student_fees for transport category
        $sfStmt = db()->prepare('
            SELECT sf.*, fc.code AS category_code
            FROM student_fees sf
            JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            WHERE sf.student_id = :sid 
              AND (fc.code = "transport" OR fc.code = "transport_fee" OR fc.code = "transport_change_fee" OR fc.name LIKE "%transport%" OR fc.name LIKE "%bus%")
              AND sf.status = "paid"
            ORDER BY sf.id DESC
            LIMIT 1
        ');
        $sfStmt->execute([':sid' => $studentId]);
        $sf = $sfStmt->fetch();
        if ($sf) {
            return [
                'is_paid'        => true,
                'payment_id'     => null,
                'amount_paid'    => (float) $sf['final_amount'],
                'payment_date'   => date('Y-m-d', strtotime($sf['updated_at'] ?? $sf['created_at'])),
                'transaction_id' => 'SF-PAID-' . $sf['id'],
                'method'         => 'Campus Fee Portal',
            ];
        }

        return null;
    }

    /**
     * Get or evaluate/generate the Digital Bus Pass for a student.
     * Core Rule: PAID TRANSPORT FEE + VALID TRANSPORT ASSIGNMENT = ACTIVE BUS PASS.
     */
    public function getOrCreateBusPass(int $studentId): ?array
    {
        $sub = $this->getStudentActiveSubscription($studentId);
        if (!$sub) {
            return null;
        }

        $allocationId = (int) $sub['id'];
        $routeId      = (int) $sub['route_id'];
        $vehicleId    = (int) ($sub['vehicle_id'] ?: 1);
        $annualFare   = (float) ($sub['fare'] ?? 18000.00);

        // Check payment status
        $payment = $this->checkTransportFeePayment($studentId, $routeId);
        $isPaid  = !empty($payment['is_paid']);

        // Check existing pass record
        $stmt = db()->prepare('SELECT * FROM transport_bus_passes WHERE allocation_id = :aid OR student_id = :sid ORDER BY id DESC LIMIT 1');
        $stmt->execute([':aid' => $allocationId, ':sid' => $studentId]);
        $pass = $stmt->fetch();

        $currentYear = date('Y');
        $academicYearEnd = (date('n') <= 3) ? date('Y-03-31') : (date('Y', strtotime('+1 year')) . '-03-31');
        $today = date('Y-m-d');

        if (!$pass) {
            // Generate unique pass number: KEC-BP-2026-0001
            $maxId = (int) (db()->query('SELECT COALESCE(MAX(id), 0) FROM transport_bus_passes')->fetchColumn()) + 1;
            $passNumber = 'KEC-BP-' . $currentYear . '-' . str_pad((string)$maxId, 4, '0', STR_PAD_LEFT);
            // In case of any collision, append random suffix
            $dupChk = db()->prepare('SELECT id FROM transport_bus_passes WHERE pass_number = :pnum LIMIT 1');
            $dupChk->execute([':pnum' => $passNumber]);
            if ($dupChk->fetch()) {
                $passNumber .= '-' . strtoupper(bin2hex(random_bytes(2)));
            }

            $status = $isPaid ? 'active' : 'payment_pending';
            $paidAmt = $isPaid ? ($payment['amount_paid'] ?? $annualFare) : 0.00;
            $issueDate = $isPaid ? ($payment['payment_date'] ?? $today) : $today;
            $validFrom = $issueDate;
            $validUntil = $academicYearEnd;
            $paymentId = $payment['payment_id'] ?? null;

            $ins = db()->prepare('
                INSERT INTO transport_bus_passes (
                    allocation_id, student_id, route_id, vehicle_id, payment_id, pass_number,
                    amount_paid, issue_date, valid_from, valid_until, status, created_at
                ) VALUES (
                    :aid, :sid, :rid, :vid, :pid, :pnum, :amt, :idate, :vfrom, :vuntil, :status, NOW()
                )
            ');
            $ins->execute([
                ':aid'    => $allocationId,
                ':sid'    => $studentId,
                ':rid'    => $routeId,
                ':vid'    => $vehicleId,
                ':pid'    => $paymentId,
                ':pnum'   => $passNumber,
                ':amt'    => $paidAmt,
                ':idate'  => $issueDate,
                ':vfrom'  => $validFrom,
                ':vuntil' => $validUntil,
                ':status' => $status,
            ]);

            $passId = (int) db()->lastInsertId();
            return $this->getBusPassById($passId);
        }

        // If existing pass exists: keep allocation, route, vehicle and payment status in sync
        $needsUpdate = false;
        $updates = [];
        $params = [':id' => (int)$pass['id']];

        if ($pass['status'] === 'payment_pending' && $isPaid) {
            $updates[] = 'status = "active"';
            $updates[] = 'amount_paid = :amt';
            $updates[] = 'payment_id = :pid';
            $updates[] = 'valid_from = :vfrom';
            $updates[] = 'valid_until = :vuntil';
            $params[':amt']    = $payment['amount_paid'] ?? $annualFare;
            $params[':pid']    = $payment['payment_id'] ?? null;
            $params[':vfrom']  = $payment['payment_date'] ?? $today;
            $params[':vuntil'] = $academicYearEnd;
            $needsUpdate = true;
        }

        if ((int)$pass['route_id'] !== $routeId) {
            $updates[] = 'route_id = :rid';
            $params[':rid'] = $routeId;
            $needsUpdate = true;
        }

        if ((int)$pass['vehicle_id'] !== $vehicleId) {
            $updates[] = 'vehicle_id = :vid';
            $params[':vid'] = $vehicleId;
            $needsUpdate = true;
        }

        if ((int)$pass['allocation_id'] !== $allocationId) {
            $updates[] = 'allocation_id = :aid';
            $params[':aid'] = $allocationId;
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            $updates[] = 'updated_at = NOW()';
            $upSql = 'UPDATE transport_bus_passes SET ' . implode(', ', $updates) . ' WHERE id = :id';
            $upStmt = db()->prepare($upSql);
            $upStmt->execute($params);
        }

        return $this->getBusPassById((int) $pass['id']);
    }

    /**
     * Get complete 360-degree Bus Pass details by Pass ID.
     */
    public function getBusPassById(int $passId): ?array
    {
        $stmt = db()->prepare('
            SELECT tbp.*, 
                   s.first_name, s.last_name, s.roll_number, s.admission_number, s.photo_path, s.gender, s.mobile, s.email,
                   dept.name AS department_name, dept.code AS department_code,
                   c.name AS course_name, c.code AS course_code,
                   sem.number AS semester_number,
                   sec.name AS section_name,
                   ta.pickup_point, ta.allotted_date,
                   tr.route_name, tr.start_point, tr.end_point, tr.monthly_fee AS route_fare,
                   v.registration_number AS bus_number, v.driver_name, v.driver_mobile,
                   p.transaction_id, p.payment_method, p.payment_date AS fee_paid_date,
                   u.username AS suspended_by_username
            FROM transport_bus_passes tbp
            JOIN transport_allocations ta ON ta.id = tbp.allocation_id
            JOIN students s ON s.id = tbp.student_id
            JOIN transport_routes tr ON tr.id = tbp.route_id
            JOIN vehicles v ON v.id = tbp.vehicle_id
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            LEFT JOIN departments dept ON dept.id = sa.department_id
            LEFT JOIN courses c ON c.id = sa.course_id
            LEFT JOIN semesters sem ON sem.id = sa.semester_id
            LEFT JOIN sections sec ON sec.id = sa.section_id
            LEFT JOIN payments p ON p.id = tbp.payment_id
            LEFT JOIN users u ON u.id = tbp.suspended_by
            WHERE tbp.id = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $passId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Get Bus Passes list for Admin management with filters.
     */
    public function getBusPasses(array $filters = [], int $limit = 100): array
    {
        $sql = '
            SELECT tbp.*, 
                   s.first_name, s.last_name, s.roll_number, s.photo_path,
                   dept.name AS department_name, dept.code AS department_code,
                   c.code AS course_code, sem.number AS semester_number,
                   ta.pickup_point,
                   tr.route_name,
                   v.registration_number AS bus_number,
                   p.transaction_id
            FROM transport_bus_passes tbp
            JOIN transport_allocations ta ON ta.id = tbp.allocation_id
            JOIN students s ON s.id = tbp.student_id
            JOIN transport_routes tr ON tr.id = tbp.route_id
            JOIN vehicles v ON v.id = tbp.vehicle_id
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            LEFT JOIN departments dept ON dept.id = sa.department_id
            LEFT JOIN courses c ON c.id = sa.course_id
            LEFT JOIN semesters sem ON sem.id = sa.semester_id
            LEFT JOIN payments p ON p.id = tbp.payment_id
            WHERE 1=1
        ';

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND tbp.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['bus_id'])) {
            $sql .= ' AND tbp.vehicle_id = :bus_id';
            $params[':bus_id'] = (int) $filters['bus_id'];
        }

        if (!empty($filters['route_id'])) {
            $sql .= ' AND tbp.route_id = :route_id';
            $params[':route_id'] = (int) $filters['route_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (s.first_name LIKE :q OR s.last_name LIKE :q OR s.roll_number LIKE :q OR tbp.pass_number LIKE :q)';
            $params[':q'] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY tbp.id DESC LIMIT ' . $limit;

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get Bus Pass KPI Counters for Admin dashboard.
     */
    public function getBusPassStats(): array
    {
        $totalPasses = (int) db()->query('SELECT COUNT(*) FROM transport_bus_passes')->fetchColumn();
        $activePasses = (int) db()->query('SELECT COUNT(*) FROM transport_bus_passes WHERE status = "active"')->fetchColumn();
        $paymentPending = (int) db()->query('SELECT COUNT(*) FROM transport_bus_passes WHERE status = "payment_pending"')->fetchColumn();
        $expired = (int) db()->query('SELECT COUNT(*) FROM transport_bus_passes WHERE status = "expired" OR (status = "active" AND valid_until < CURDATE())')->fetchColumn();
        $suspended = (int) db()->query('SELECT COUNT(*) FROM transport_bus_passes WHERE status = "suspended"')->fetchColumn();
        $totalBuses = (int) db()->query('SELECT COUNT(*) FROM vehicles WHERE status = "active"')->fetchColumn();

        return [
            'total_passes'    => $totalPasses,
            'active_passes'   => $activePasses,
            'payment_pending' => $paymentPending,
            'expired'         => $expired,
            'suspended'       => $suspended,
            'total_buses'     => $totalBuses,
        ];
    }

    /**
     * Suspend a bus pass.
     */
    public function suspendBusPass(int $passId, string $reason, int $adminId): bool
    {
        $stmt = db()->prepare('
            UPDATE transport_bus_passes 
            SET status = "suspended", suspended_reason = :reason, suspended_by = :aid, suspended_at = NOW(), updated_at = NOW()
            WHERE id = :id
        ');
        return $stmt->execute([
            ':reason' => $reason,
            ':aid'    => $adminId,
            ':id'     => $passId,
        ]);
    }

    /**
     * Reactivate a suspended bus pass.
     */
    public function reactivateBusPass(int $passId, int $adminId): bool
    {
        $stmt = db()->prepare('
            UPDATE transport_bus_passes 
            SET status = "active", suspended_reason = NULL, suspended_by = NULL, suspended_at = NULL, updated_at = NOW()
            WHERE id = :id
        ');
        return $stmt->execute([':id' => $passId]);
    }
}

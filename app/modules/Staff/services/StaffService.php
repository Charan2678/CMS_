<?php

declare(strict_types=1);

namespace App\Modules\Staff\services;

use App\Core\Security;
use Exception;
use PDO;

class StaffService
{
    /**
     * Get filtered directory of non-faculty staff members.
     */
    public function getAllStaff(array $filters = [], int $collegeId = 1): array
    {
        $sql = '
            SELECT s.*, des.name AS designation_name
            FROM staff s
            JOIN designations des ON des.id = s.designation_id
            WHERE s.college_id = :college_id
        ';

        $params = [':college_id' => $collegeId];

        if (!empty($filters['department_type'])) {
            $sql .= ' AND s.department_type = :dept_type';
            $params[':dept_type'] = $filters['department_type'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND s.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (s.first_name LIKE :q OR s.last_name LIKE :q OR s.employee_id LIKE :q OR s.email LIKE :q)';
            $params[':q'] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY s.department_type ASC, s.first_name ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Get single staff profile.
     */
    public function getStaffProfile(int $staffId): ?array
    {
        $stmt = db()->prepare('
            SELECT s.*, des.name AS designation_name,
                   u.id AS user_account_id, u.username, u.is_active AS user_active
            FROM staff s
            JOIN designations des ON des.id = s.designation_id
            LEFT JOIN users u ON u.linked_type = "staff" AND u.linked_id = s.id
            WHERE s.id = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $staffId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Onboard non-faculty staff member and auto-provision portal account.
     */
    public function createStaff(array $data): array
    {
        db()->beginTransaction();

        try {
            // 1. Insert Staff Profile
            $stmt = db()->prepare('
                INSERT INTO staff (
                    college_id, department_type, designation_id, employee_id,
                    first_name, last_name, date_of_birth, gender, mobile, email,
                    joining_date, address, status, created_at
                ) VALUES (
                    :college_id, :department_type, :designation_id, :employee_id,
                    :first_name, :last_name, :date_of_birth, :gender, :mobile, :email,
                    :joining_date, :address, "active", NOW()
                )
            ');

            $stmt->execute([
                ':college_id'      => $data['college_id'] ?? 1,
                ':department_type' => $data['department_type'],
                ':designation_id'  => (int) $data['designation_id'],
                ':employee_id'     => $data['employee_id'],
                ':first_name'      => $data['first_name'],
                ':last_name'       => $data['last_name'],
                ':date_of_birth'   => $data['date_of_birth'] ?? null,
                ':gender'          => $data['gender'] ?? 'male',
                ':mobile'          => $data['mobile'] ?? null,
                ':email'           => $data['email'] ?? null,
                ':joining_date'    => $data['joining_date'] ?? date('Y-m-d'),
                ':address'         => $data['address'] ?? null,
            ]);

            $staffId = (int) db()->lastInsertId();

            // 2. Assign Staff System Role
            $rStmt = db()->prepare('SELECT id FROM roles WHERE code = "staff" LIMIT 1');
            $rStmt->execute();
            $staffRoleId = (int) ($rStmt->fetchColumn() ?: 5);

            // 3. Create User Login Account
            $defaultPasswordHash = Security::hashPassword('Staff123!');

            $uStmt = db()->prepare('
                INSERT INTO users (
                    college_id, username, email, password_hash, role_id, linked_type, linked_id, is_active, must_change_password, created_by, created_at
                ) VALUES (
                    :college_id, :username, :email, :password_hash, :role_id, "staff", :linked_id, 1, 1, :created_by, NOW()
                )
            ');

            $uStmt->execute([
                ':college_id'    => $data['college_id'] ?? 1,
                ':username'      => $data['employee_id'],
                ':email'         => !empty($data['email']) ? $data['email'] : strtolower($data['employee_id']) . '@staff.college.edu',
                ':password_hash' => $defaultPasswordHash,
                ':role_id'       => $staffRoleId,
                ':linked_id'     => $staffId,
                ':created_by'    => auth_id() ?? 1,
            ]);

            db()->commit();

            return [
                'success'  => true,
                'message'  => 'Staff member onboarded successfully! Login credentials generated (Username: ' . $data['employee_id'] . ', Default Password: Staff123!).',
                'staff_id' => $staffId
            ];

        } catch (Exception $e) {
            db()->rollBack();
            return [
                'success' => false,
                'message' => 'Staff onboarding failed: ' . $e->getMessage()
            ];
        }
    }
}

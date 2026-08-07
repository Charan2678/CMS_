<?php

declare(strict_types=1);

namespace App\Modules\Faculty\services;

use App\Core\Security;
use Exception;
use PDO;

class FacultyService
{
    // ─── Designations ──────────────────────────────────────────
    public function getDesignations(int $collegeId = 1, ?string $departmentType = null): array
    {
        if ($departmentType !== null) {
            $stmt = db()->prepare('SELECT * FROM designations WHERE college_id = :college_id AND department_type = :dept_type AND status = 1 ORDER BY level DESC, name ASC');
            $stmt->execute([':college_id' => $collegeId, ':dept_type' => $departmentType]);
        } else {
            $stmt = db()->prepare('SELECT * FROM designations WHERE college_id = :college_id AND status = 1 ORDER BY level DESC, name ASC');
            $stmt->execute([':college_id' => $collegeId]);
        }
        return $stmt->fetchAll() ?: [];
    }

    public function createDesignation(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO designations (college_id, name, code, level, status)
            VALUES (:college_id, :name, :code, :level, :status)
        ');
        return $stmt->execute([
            ':college_id' => $data['college_id'] ?? 1,
            ':name'       => $data['name'],
            ':code'       => strtolower(str_replace(' ', '_', $data['code'] ?? $data['name'])),
            ':level'      => (int) ($data['level'] ?? 1),
            ':status'     => (int) ($data['status'] ?? 1),
        ]);
    }

    // ─── Faculty Listing & Profile ─────────────────────────────
    public function getAllFaculty(array $filters = [], int $collegeId = 1): array
    {
        $sql = '
            SELECT f.*, d.name AS department_name, d.code AS department_code,
                   des.name AS designation_name, des.level AS designation_level
            FROM faculty f
            JOIN departments d ON d.id = f.department_id
            JOIN designations des ON des.id = f.designation_id
            WHERE f.college_id = :college_id
        ';

        $params = [':college_id' => $collegeId];

        if (!empty($filters['department_id'])) {
            $sql .= ' AND f.department_id = :dept_id';
            $params[':dept_id'] = (int) $filters['department_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND f.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (f.first_name LIKE :q OR f.last_name LIKE :q OR f.employee_id LIKE :q OR f.email LIKE :q)';
            $params[':q'] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY des.level DESC, f.first_name ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    public function getFacultyProfile(int $facultyId): ?array
    {
        $stmt = db()->prepare('
            SELECT f.*, d.name AS department_name, d.code AS department_code,
                   des.name AS designation_name,
                   u.id AS user_account_id, u.username, u.is_active AS user_active
            FROM faculty f
            JOIN departments d ON d.id = f.department_id
            JOIN designations des ON des.id = f.designation_id
            LEFT JOIN users u ON u.linked_type = "faculty" AND u.linked_id = f.id
            WHERE f.id = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $facultyId]);
        $faculty = $stmt->fetch();

        if (!$faculty) {
            return null;
        }

        // Fetch assigned subjects
        $faculty['assignments'] = $this->getAssignedSubjects($facultyId);

        return $faculty;
    }

    // ─── Onboarding Faculty & Creating Account ─────────────────
    public function createFaculty(array $data): array
    {
        db()->beginTransaction();

        try {
            // 1. Insert Faculty Profile
            $stmt = db()->prepare('
                INSERT INTO faculty (
                    college_id, department_id, designation_id, employee_id,
                    first_name, last_name, date_of_birth, gender, blood_group,
                    mobile, email, qualification, specialization, experience_years,
                    joining_date, address, status, created_by, created_at
                ) VALUES (
                    :college_id, :department_id, :designation_id, :employee_id,
                    :first_name, :last_name, :date_of_birth, :gender, :blood_group,
                    :mobile, :email, :qualification, :specialization, :experience_years,
                    :joining_date, :address, "active", :created_by, NOW()
                )
            ');

            $stmt->execute([
                ':college_id'       => $data['college_id'] ?? 1,
                ':department_id'    => (int) $data['department_id'],
                ':designation_id'   => (int) $data['designation_id'],
                ':employee_id'      => $data['employee_id'],
                ':first_name'       => $data['first_name'],
                ':last_name'        => $data['last_name'],
                ':date_of_birth'    => $data['date_of_birth'] ?? null,
                ':gender'           => $data['gender'] ?? 'male',
                ':blood_group'      => $data['blood_group'] ?? null,
                ':mobile'           => $data['mobile'] ?? null,
                ':email'            => $data['email'] ?? null,
                ':qualification'    => $data['qualification'] ?? null,
                ':specialization'   => $data['specialization'] ?? null,
                ':experience_years' => !empty($data['experience_years']) ? (float)$data['experience_years'] : 0.0,
                ':joining_date'     => $data['joining_date'] ?? date('Y-m-d'),
                ':address'          => $data['address'] ?? null,
                ':created_by'       => auth_id() ?? 1,
            ]);

            $facultyId = (int) db()->lastInsertId();

            // 2. Check Role (HOD or Faculty role)
            $roleCode = !empty($data['is_hod']) ? 'hod' : 'faculty';
            $rStmt    = db()->prepare('SELECT id FROM roles WHERE code = :code LIMIT 1');
            $rStmt->execute([':code' => $roleCode]);
            $roleId = (int) ($rStmt->fetchColumn() ?: 4);

            // If HOD, update department.hod_id
            if (!empty($data['is_hod'])) {
                $hStmt = db()->prepare('UPDATE departments SET hod_id = :fid WHERE id = :did');
                $hStmt->execute([':fid' => $facultyId, ':did' => $data['department_id']]);
            }

            // 3. Create User Login Account
            $defaultPasswordHash = Security::hashPassword('Faculty123!');

            $uStmt = db()->prepare('
                INSERT INTO users (
                    college_id, username, email, password_hash, role_id, linked_type, linked_id, is_active, must_change_password, created_by, created_at
                ) VALUES (
                    :college_id, :username, :email, :password_hash, :role_id, "faculty", :linked_id, 1, 1, :created_by, NOW()
                )
            ');

            $uStmt->execute([
                ':college_id'    => $data['college_id'] ?? 1,
                ':username'      => $data['employee_id'],
                ':email'         => !empty($data['email']) ? $data['email'] : strtolower($data['employee_id']) . '@faculty.college.edu',
                ':password_hash' => $defaultPasswordHash,
                ':role_id'       => $roleId,
                ':linked_id'     => $facultyId,
                ':created_by'    => auth_id() ?? 1,
            ]);

            db()->commit();

            return [
                'success'    => true,
                'message'    => 'Faculty onboarded successfully! Login credentials generated (Username: ' . $data['employee_id'] . ', Default Password: Faculty123!).',
                'faculty_id' => $facultyId
            ];

        } catch (Exception $e) {
            db()->rollBack();
            return [
                'success' => false,
                'message' => 'Faculty onboarding failed: ' . $e->getMessage()
            ];
        }
    }

    // ─── Subject & Section Allocations ────────────────────────
    public function assignSubject(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO faculty_subject_assignments (
                faculty_id, subject_id, section_id, academic_year_id, created_at
            ) VALUES (
                :faculty_id, :subject_id, :section_id, :academic_year_id, NOW()
            )
            ON DUPLICATE KEY UPDATE faculty_id = VALUES(faculty_id)
        ');

        return $stmt->execute([
            ':faculty_id'       => (int) $data['faculty_id'],
            ':subject_id'       => (int) $data['subject_id'],
            ':section_id'       => (int) $data['section_id'],
            ':academic_year_id' => (int) $data['academic_year_id'],
        ]);
    }

    public function getAssignedSubjects(int $facultyId, int $academicYearId = 0): array
    {
        $sql = '
            SELECT fsa.*, sub.name AS subject_name, sub.code AS subject_code, sub.type AS subject_type,
                   sec.name AS section_name, sem.number AS semester_number, c.code AS course_code,
                   ay.name AS academic_year_name
            FROM faculty_subject_assignments fsa
            JOIN subjects sub ON sub.id = fsa.subject_id
            JOIN sections sec ON sec.id = fsa.section_id
            JOIN semesters sem ON sem.id = sec.semester_id
            JOIN courses c ON c.id = sem.course_id
            JOIN academic_years ay ON ay.id = fsa.academic_year_id
            WHERE fsa.faculty_id = :faculty_id
        ';

        if ($academicYearId > 0) {
            $sql .= ' AND fsa.academic_year_id = :ay_id';
        }

        $sql .= ' ORDER BY ay.id DESC, sub.code ASC';

        $stmt = db()->prepare($sql);
        $params = [':faculty_id' => $facultyId];
        if ($academicYearId > 0) {
            $params[':ay_id'] = $academicYearId;
        }

        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }
}

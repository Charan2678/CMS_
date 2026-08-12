<?php

declare(strict_types=1);

namespace App\Modules\Master\services;

use PDO;

class MasterService
{
    // ─── 1. College Info ───────────────────────────────────────
    public function getCollegeInfo(int $collegeId = 1): ?array
    {
        $stmt = db()->prepare('SELECT * FROM colleges WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $collegeId]);
        return $stmt->fetch() ?: null;
    }

    public function updateCollegeInfo(int $collegeId, array $data): bool
    {
        $stmt = db()->prepare('
            UPDATE colleges SET
                name = :name,
                code = :code,
                address = :address,
                city = :city,
                state = :state,
                pincode = :pincode,
                phone = :phone,
                email = :email,
                website = :website,
                affiliation_body = :affiliation_body,
                affiliation_number = :affiliation_number,
                updated_at = NOW()
            WHERE id = :id
        ');

        return $stmt->execute([
            ':name'               => $data['name'],
            ':code'               => $data['code'],
            ':address'            => $data['address'] ?? null,
            ':city'               => $data['city'] ?? null,
            ':state'              => $data['state'] ?? null,
            ':pincode'            => $data['pincode'] ?? null,
            ':phone'              => $data['phone'] ?? null,
            ':email'              => $data['email'] ?? null,
            ':website'            => $data['website'] ?? null,
            ':affiliation_body'   => $data['affiliation_body'] ?? null,
            ':affiliation_number' => $data['affiliation_number'] ?? null,
            ':id'                 => $collegeId,
        ]);
    }

    // ─── 2. Academic Years ─────────────────────────────────────
    public function getAcademicYears(int $collegeId = 1): array
    {
        $stmt = db()->prepare('SELECT * FROM academic_years WHERE college_id = :college_id ORDER BY start_date DESC');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function getAcademicYearById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM academic_years WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function createAcademicYear(array $data): bool
    {
        if (!empty($data['is_current'])) {
            db()->prepare('UPDATE academic_years SET is_current = 0 WHERE college_id = :college_id')->execute([':college_id' => $data['college_id']]);
        }

        $stmt = db()->prepare('
            INSERT INTO academic_years (college_id, name, start_date, end_date, is_current, status, created_at)
            VALUES (:college_id, :name, :start_date, :end_date, :is_current, :status, NOW())
        ');

        return $stmt->execute([
            ':college_id' => $data['college_id'],
            ':name'       => $data['name'],
            ':start_date' => $data['start_date'],
            ':end_date'   => $data['end_date'],
            ':is_current' => !empty($data['is_current']) ? 1 : 0,
            ':status'     => $data['status'] ?? 1,
        ]);
    }

    public function setCurrentAcademicYear(int $id, int $collegeId = 1): bool
    {
        db()->beginTransaction();
        try {
            db()->prepare('UPDATE academic_years SET is_current = 0 WHERE college_id = :college_id')->execute([':college_id' => $collegeId]);
            db()->prepare('UPDATE academic_years SET is_current = 1 WHERE id = :id AND college_id = :college_id')->execute([':id' => $id, ':college_id' => $collegeId]);
            db()->commit();
            return true;
        } catch (\Exception $e) {
            db()->rollBack();
            return false;
        }
    }

    // ─── 3. Departments ───────────────────────────────────────
    public function getDepartments(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT d.*, f.first_name AS hod_first_name, f.last_name AS hod_last_name
            FROM departments d
            LEFT JOIN faculty f ON f.id = d.hod_id
            WHERE d.college_id = :college_id
            ORDER BY d.name ASC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function getDepartmentById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM departments WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function createDepartment(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO departments (college_id, name, code, hod_id, established_year, status, created_at)
            VALUES (:college_id, :name, :code, :hod_id, :established_year, :status, NOW())
        ');

        return $stmt->execute([
            ':college_id'       => $data['college_id'],
            ':name'             => $data['name'],
            ':code'             => strtoupper($data['code']),
            ':hod_id'           => $data['hod_id'] ?? null,
            ':established_year' => $data['established_year'] ?? null,
            ':status'           => $data['status'] ?? 1,
        ]);
    }

    public function updateDepartment(int $id, array $data): bool
    {
        $stmt = db()->prepare('
            UPDATE departments SET
                name = :name,
                code = :code,
                hod_id = :hod_id,
                established_year = :established_year,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
        ');

        return $stmt->execute([
            ':name'             => $data['name'],
            ':code'             => strtoupper($data['code']),
            ':hod_id'           => $data['hod_id'] ?? null,
            ':established_year' => $data['established_year'] ?? null,
            ':status'           => $data['status'] ?? 1,
            ':id'               => $id,
        ]);
    }

    // ─── 4. Courses ───────────────────────────────────────────
    public function getCourses(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT c.*, d.name AS department_name, d.code AS department_code
            FROM courses c
            JOIN departments d ON d.id = c.department_id
            WHERE d.college_id = :college_id
            ORDER BY c.name ASC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createCourse(array $data): bool
    {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare('
                INSERT INTO courses (department_id, name, code, degree_type, duration_years, total_semesters, status, created_at)
                VALUES (:department_id, :name, :code, :degree_type, :duration_years, :total_semesters, :status, NOW())
            ');
            $stmt->execute([
                ':department_id'   => $data['department_id'],
                ':name'            => $data['name'],
                ':code'            => strtoupper($data['code']),
                ':degree_type'     => $data['degree_type'],
                ':duration_years'  => $data['duration_years'],
                ':total_semesters' => $data['total_semesters'],
                ':status'          => $data['status'] ?? 1,
            ]);

            $courseId = (int) db()->lastInsertId();

            // Automatically create Semesters 1..total_semesters
            $semStmt = db()->prepare('
                INSERT INTO semesters (course_id, number, name, status, created_at)
                VALUES (:course_id, :number, :name, 1, NOW())
            ');

            for ($i = 1; $i <= (int)$data['total_semesters']; $i++) {
                $semStmt->execute([
                    ':course_id' => $courseId,
                    ':number'    => $i,
                    ':name'      => "Semester {$i}"
                ]);
            }

            db()->commit();
            return true;
        } catch (\Exception $e) {
            db()->rollBack();
            return false;
        }
    }

    // ─── 5. Semesters ─────────────────────────────────────────
    public function getSemestersByCourse(int $courseId): array
    {
        $stmt = db()->prepare('SELECT * FROM semesters WHERE course_id = :course_id ORDER BY number ASC');
        $stmt->execute([':course_id' => $courseId]);
        return $stmt->fetchAll() ?: [];
    }

    // ─── 6. Sections ──────────────────────────────────────────
    public function getSections(int $academicYearId = 0): array
    {
        $sql = '
            SELECT s.*, sem.number AS semester_number, c.name AS course_name, c.code AS course_code, ay.name AS academic_year_name
            FROM sections s
            JOIN semesters sem ON sem.id = s.semester_id
            JOIN courses c ON c.id = sem.course_id
            JOIN academic_years ay ON ay.id = s.academic_year_id
        ';
        if ($academicYearId > 0) {
            $sql .= ' WHERE s.academic_year_id = :ay_id';
        }
        $sql .= ' ORDER BY c.name ASC, sem.number ASC, s.name ASC';

        $stmt = db()->prepare($sql);
        if ($academicYearId > 0) {
            $stmt->execute([':ay_id' => $academicYearId]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll() ?: [];
    }

    public function createSection(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO sections (semester_id, academic_year_id, name, max_strength, room_id, status, created_at)
            VALUES (:semester_id, :academic_year_id, :name, :max_strength, :room_id, :status, NOW())
        ');

        return $stmt->execute([
            ':semester_id'      => $data['semester_id'],
            ':academic_year_id' => $data['academic_year_id'],
            ':name'             => strtoupper($data['name']),
            ':max_strength'     => $data['max_strength'] ?? 60,
            ':room_id'          => $data['room_id'] ?? null,
            ':status'           => $data['status'] ?? 1,
        ]);
    }

    // ─── 7. Subjects ──────────────────────────────────────────
    public function getSubjects(int $semesterId = 0): array
    {
        $sql = '
            SELECT sub.*, sem.number AS semester_number, c.name AS course_name, c.code AS course_code
            FROM subjects sub
            JOIN semesters sem ON sem.id = sub.semester_id
            JOIN courses c ON c.id = sem.course_id
        ';
        if ($semesterId > 0) {
            $sql .= ' WHERE sub.semester_id = :semester_id';
        }
        $sql .= ' ORDER BY c.name ASC, sem.number ASC, sub.code ASC';

        $stmt = db()->prepare($sql);
        if ($semesterId > 0) {
            $stmt->execute([':semester_id' => $semesterId]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll() ?: [];
    }

    public function createSubject(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO subjects (semester_id, name, code, type, credits, max_internal_marks, max_external_marks, pass_internal_marks, pass_external_marks, status, created_at)
            VALUES (:semester_id, :name, :code, :type, :credits, :max_internal_marks, :max_external_marks, :pass_internal_marks, :pass_external_marks, :status, NOW())
        ');

        return $stmt->execute([
            ':semester_id'         => $data['semester_id'],
            ':name'                => $data['name'],
            ':code'                => strtoupper($data['code']),
            ':type'                => $data['type'] ?? 'theory',
            ':credits'             => $data['credits'] ?? 3.00,
            ':max_internal_marks'  => $data['max_internal_marks'] ?? 25,
            ':max_external_marks'  => $data['max_external_marks'] ?? 75,
            ':pass_internal_marks' => $data['pass_internal_marks'] ?? 10,
            ':pass_external_marks' => $data['pass_external_marks'] ?? 30,
            ':status'              => $data['status'] ?? 1,
        ]);
    }

    // ─── 8. Rooms ─────────────────────────────────────────────
    public function getRooms(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT r.*, b.name AS building_name
            FROM rooms r
            LEFT JOIN buildings b ON b.id = r.building_id
            ORDER BY r.name ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}

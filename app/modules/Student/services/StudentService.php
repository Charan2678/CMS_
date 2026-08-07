<?php

declare(strict_types=1);

namespace App\Modules\Student\services;

use App\Core\Security;
use Exception;
use PDO;

class StudentService
{
    /**
     * Get paginated/filtered list of students.
     */
    public function getAllStudents(int $collegeId = 1, array $filters = [], int $page = 1, int $perPage = 25): array
    {
        $whereSql = ' WHERE s.college_id = :college_id';
        $params   = [':college_id' => $collegeId];

        if (!empty($filters['department_id'])) {
            $whereSql .= ' AND sa.department_id = :dept_id';
            $params[':dept_id'] = (int) $filters['department_id'];
        }

        if (!empty($filters['course_id'])) {
            $whereSql .= ' AND sa.course_id = :course_id';
            $params[':course_id'] = (int) $filters['course_id'];
        }

        if (!empty($filters['status'])) {
            $whereSql .= ' AND s.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $whereSql .= ' AND (s.first_name LIKE :q OR s.last_name LIKE :q OR s.roll_number LIKE :q OR s.admission_number LIKE :q OR s.email LIKE :q)';
            $params[':q'] = '%' . $filters['search'] . '%';
        }

        // 1. Get Total Count
        $countSql = '
            SELECT COUNT(DISTINCT s.id) 
            FROM students s
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
        ' . $whereSql;
        $cntStmt = db()->prepare($countSql);
        $cntStmt->execute($params);
        $total = (int) $cntStmt->fetchColumn();

        // 2. Fetch Paginated Records
        $page     = max(1, $page);
        $perPage  = max(1, min(100, $perPage));
        $offset   = ($page - 1) * $perPage;

        $sql = '
            SELECT s.*, d.name AS department_name, c.name AS course_name, sem.number AS semester_number, sec.name AS section_name
            FROM students s
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            LEFT JOIN departments d ON d.id = sa.department_id
            LEFT JOIN courses c ON c.id = sa.course_id
            LEFT JOIN semesters sem ON sem.id = sa.semester_id
            LEFT JOIN sections sec ON sec.id = sa.section_id
        ' . $whereSql . ' ORDER BY s.id DESC LIMIT ' . $perPage . ' OFFSET ' . $offset;

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll() ?: [];

        return [
            'data'        => $rows,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get 360-degree student profile by ID.
     */
    public function getStudentProfile(int $studentId): ?array
    {
        $stmt = db()->prepare('
            SELECT s.*, sa.academic_year_id, sa.department_id, sa.course_id, sa.semester_id, sa.section_id,
                   d.name AS department_name, d.code AS department_code,
                   c.name AS course_name, c.code AS course_code,
                   sem.number AS semester_number,
                   sec.name AS section_name,
                   ay.name AS academic_year_name,
                   u.id AS user_account_id, u.username, u.is_active AS user_active
            FROM students s
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            LEFT JOIN departments d ON d.id = sa.department_id
            LEFT JOIN courses c ON c.id = sa.course_id
            LEFT JOIN semesters sem ON sem.id = sa.semester_id
            LEFT JOIN sections sec ON sec.id = sa.section_id
            LEFT JOIN academic_years ay ON ay.id = sa.academic_year_id
            LEFT JOIN users u ON u.linked_type = "student" AND u.linked_id = s.id
            WHERE s.id = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $studentId]);
        $student = $stmt->fetch();

        if (!$student) {
            return null;
        }

        // Fetch Guardians
        $gStmt = db()->prepare('SELECT * FROM guardians WHERE student_id = :sid ORDER BY is_primary DESC');
        $gStmt->execute([':sid' => $studentId]);
        $student['guardians'] = $gStmt->fetchAll() ?: [];

        // Fetch Documents
        $dStmt = db()->prepare('SELECT * FROM student_documents WHERE student_id = :sid ORDER BY id DESC');
        $dStmt->execute([':sid' => $studentId]);
        $student['documents'] = $dStmt->fetchAll() ?: [];

        return $student;
    }

    /**
     * Transactional Admission Execution.
     */
    public function admitStudent(array $data, array $uploadedFiles = []): array
    {
        db()->beginTransaction();

        try {
            // 1. Insert Student Personal Info
            $stmt = db()->prepare('
                INSERT INTO students (
                    college_id, roll_number, admission_number, first_name, last_name,
                    date_of_birth, gender, blood_group, mobile, email,
                    address_line1, city, state, pincode, religion, caste, category,
                    differently_abled, status, admission_date, created_by, created_at
                ) VALUES (
                    :college_id, :roll_number, :admission_number, :first_name, :last_name,
                    :date_of_birth, :gender, :blood_group, :mobile, :email,
                    :address_line1, :city, :state, :pincode, :religion, :caste, :category,
                    :differently_abled, "active", :admission_date, :created_by, NOW()
                )
            ');

            $stmt->execute([
                ':college_id'        => $data['college_id'] ?? 1,
                ':roll_number'       => $data['roll_number'],
                ':admission_number'  => $data['admission_number'],
                ':first_name'        => $data['first_name'],
                ':last_name'         => $data['last_name'],
                ':date_of_birth'     => $data['date_of_birth'],
                ':gender'            => $data['gender'],
                ':blood_group'       => $data['blood_group'] ?? null,
                ':mobile'            => $data['mobile'] ?? null,
                ':email'             => $data['email'] ?? null,
                ':address_line1'     => $data['address_line1'] ?? null,
                ':city'              => $data['city'] ?? null,
                ':state'             => $data['state'] ?? null,
                ':pincode'           => $data['pincode'] ?? null,
                ':religion'          => $data['religion'] ?? null,
                ':caste'             => $data['caste'] ?? null,
                ':category'          => $data['category'] ?? 'general',
                ':differently_abled' => !empty($data['differently_abled']) ? 1 : 0,
                ':admission_date'    => $data['admission_date'] ?? date('Y-m-d'),
                ':created_by'        => auth_id() ?? 1,
            ]);

            $studentId = (int) db()->lastInsertId();

            // 2. Insert Academic Placement
            $saStmt = db()->prepare('
                INSERT INTO student_academics (
                    student_id, academic_year_id, department_id, course_id, semester_id, section_id, is_current, created_at
                ) VALUES (
                    :student_id, :academic_year_id, :department_id, :course_id, :semester_id, :section_id, 1, NOW()
                )
            ');
            $saStmt->execute([
                ':student_id'       => $studentId,
                ':academic_year_id' => (int) $data['academic_year_id'],
                ':department_id'    => (int) $data['department_id'],
                ':course_id'        => (int) $data['course_id'],
                ':semester_id'      => (int) $data['semester_id'],
                ':section_id'       => (int) $data['section_id'],
            ]);

            // 3. Insert Guardian Info
            if (!empty($data['guardian_name'])) {
                $gStmt = db()->prepare('
                    INSERT INTO guardians (
                        student_id, relationship, name, mobile, email, occupation, annual_income, is_primary, created_at
                    ) VALUES (
                        :student_id, :relationship, :name, :mobile, :email, :occupation, :annual_income, 1, NOW()
                    )
                ');
                $gStmt->execute([
                    ':student_id'   => $studentId,
                    ':relationship' => $data['guardian_relationship'] ?? 'father',
                    ':name'         => $data['guardian_name'],
                    ':mobile'       => $data['guardian_mobile'] ?? null,
                    ':email'        => $data['guardian_email'] ?? null,
                    ':occupation'   => $data['guardian_occupation'] ?? null,
                    ':annual_income'=> !empty($data['guardian_income']) ? (float)$data['guardian_income'] : null,
                ]);
            }

            // 4. Handle Documents Upload (if present)
            if (!empty($uploadedFiles['documents']['name'])) {
                $uploadDir = STORAGE_PATH . '/uploads/students';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                foreach ($uploadedFiles['documents']['name'] as $idx => $name) {
                    if (!empty($name)) {
                        $singleFile = [
                            'name'     => $name,
                            'type'     => $uploadedFiles['documents']['type'][$idx] ?? '',
                            'tmp_name' => $uploadedFiles['documents']['tmp_name'][$idx] ?? '',
                            'error'    => $uploadedFiles['documents']['error'][$idx] ?? UPLOAD_ERR_NO_FILE,
                            'size'     => $uploadedFiles['documents']['size'][$idx] ?? 0,
                        ];

                        $val = validate_upload($singleFile);
                        if (!$val['ok']) {
                            continue; // Skip invalid or unsafe upload files
                        }

                        $ext     = pathinfo($name, PATHINFO_EXTENSION);
                        $newName = 'doc_' . $studentId . '_' . time() . '_' . $idx . '.' . $ext;
                        $dest    = $uploadDir . '/' . $newName;

                        if (move_uploaded_file($singleFile['tmp_name'], $dest)) {
                            $docStmt = db()->prepare('
                                INSERT INTO student_documents (
                                    student_id, document_type, document_name, file_path, uploaded_by, verified, created_at
                                ) VALUES (
                                    :student_id, :doc_type, :doc_name, :file_path, :uploaded_by, 0, NOW()
                                )
                            ');
                            $docStmt->execute([
                                ':student_id'  => $studentId,
                                ':doc_type'    => $data['document_types'][$idx] ?? 'other',
                                ':doc_name'    => $name,
                                ':file_path'   => '/uploads/students/' . $newName,
                                ':uploaded_by' => auth_id() ?? 1,
                            ]);
                        }
                    }
                }
            }

            // 5. System Action: Create Student User Login Account
            // Find Student Role ID (role code = 'student')
            $rStmt = db()->prepare('SELECT id FROM roles WHERE code = "student" LIMIT 1');
            $rStmt->execute();
            $studentRoleId = (int) ($rStmt->fetchColumn() ?: 6);

            $defaultPasswordHash = Security::hashPassword('Student123!');

            $uStmt = db()->prepare('
                INSERT INTO users (
                    college_id, username, email, password_hash, role_id, linked_type, linked_id, is_active, must_change_password, created_by, created_at
                ) VALUES (
                    :college_id, :username, :email, :password_hash, :role_id, "student", :linked_id, 1, 1, :created_by, NOW()
                )
            ');

            $uStmt->execute([
                ':college_id'     => $data['college_id'] ?? 1,
                ':username'       => $data['roll_number'],
                ':email'          => !empty($data['email']) ? $data['email'] : strtolower($data['roll_number']) . '@student.college.edu',
                ':password_hash'  => $defaultPasswordHash,
                ':role_id'        => $studentRoleId,
                ':linked_id'      => $studentId,
                ':created_by'     => auth_id() ?? 1,
            ]);

            db()->commit();

            // Auto-dispatch credentials email to student's personal email
            $this->sendCredentialsEmail($studentId);

            return [
                'success'    => true,
                'message'    => 'Student admitted successfully! Account credentials generated and sent to personal email (Username: ' . $data['roll_number'] . ', Default Password: Student123!).',
                'student_id' => $studentId
            ];

        } catch (Exception $e) {
            db()->rollBack();
            return [
                'success' => false,
                'message' => 'Admission failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Dispatch Login Credentials Email to Student's Personal Email.
     */
    public function sendCredentialsEmail(int $studentId): bool
    {
        $student = $this->getStudentById($studentId);
        if (!$student || empty($student['email'])) {
            return false;
        }

        $rollNumber = $student['roll_number'];
        $name       = $student['first_name'] . ' ' . $student['last_name'];
        $to         = $student['email'];
        $subject    = "Welcome to Kuppam Engineering College — ERP Login Credentials";
        $loginUrl   = env('APP_URL', 'http://localhost:8000/login');

        $body = "
            <h2 style='color:#0284c7;'>Welcome to Kuppam Engineering College, {$name}!</h2>
            <p>Your student ERP portal account has been successfully generated. Below are your official login credentials to access your attendance, fees, timetable, and exam hall tickets:</p>
            <div style='background:#f8fafc; border:1px solid #cbd5e1; padding:15px 20px; border-radius:6px; margin:20px 0;'>
                <p style='margin:5px 0;'><strong>Portal Login URL:</strong> <a href='{$loginUrl}' style='color:#0284c7;'>{$loginUrl}</a></p>
                <p style='margin:5px 0;'><strong>Username (Roll Number):</strong> <code style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>{$rollNumber}</code></p>
                <p style='margin:5px 0;'><strong>Default Password:</strong> <code style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>Student123!</code></p>
            </div>
            <p style='color:#dc2626; font-size:13px;'><strong>Security Notice:</strong> You will be prompted to set a new custom password upon your first login.</p>
        ";

        return send_mail($to, $subject, $body);
    }

    /**
     * Update Student Personal & Address Details.
     */
    public function updateStudentProfile(int $studentId, array $data): bool
    {
        $stmt = db()->prepare('
            UPDATE students
            SET first_name     = :first_name,
                last_name      = :last_name,
                mobile         = :mobile,
                email          = :email,
                date_of_birth  = :date_of_birth,
                gender         = :gender,
                blood_group    = :blood_group,
                address_line1  = :address_line1,
                city           = :city,
                state          = :state,
                pincode        = :pincode
            WHERE id = :id
        ');

        return $stmt->execute([
            ':first_name'    => $data['first_name'],
            ':last_name'     => $data['last_name'],
            ':mobile'        => $data['mobile'] ?? null,
            ':email'         => $data['email'] ?? null,
            ':date_of_birth' => $data['date_of_birth'],
            ':gender'        => $data['gender'],
            ':blood_group'   => $data['blood_group'] ?? null,
            ':address_line1' => $data['address_line1'] ?? null,
            ':city'          => $data['city'] ?? null,
            ':state'         => $data['state'] ?? null,
            ':pincode'       => $data['pincode'] ?? null,
            ':id'            => $studentId,
        ]);
    }

    /**
     * Update / Save Primary Guardian Details.
     */
    public function saveGuardianDetails(int $studentId, array $data): bool
    {
        $gStmt = db()->prepare('SELECT id FROM guardians WHERE student_id = :sid AND is_primary = 1 LIMIT 1');
        $gStmt->execute([':sid' => $studentId]);
        $existingId = $gStmt->fetchColumn();

        if ($existingId) {
            $stmt = db()->prepare('
                UPDATE guardians
                SET name = :name, relationship = :rel, mobile = :mobile, email = :email, occupation = :occupation
                WHERE id = :gid
            ');
            return $stmt->execute([
                ':name'       => $data['guardian_name'],
                ':rel'        => $data['guardian_relationship'] ?? 'father',
                ':mobile'     => $data['guardian_mobile'] ?? null,
                ':email'      => $data['guardian_email'] ?? null,
                ':occupation' => $data['guardian_occupation'] ?? null,
                ':gid'        => $existingId
            ]);
        } else {
            $stmt = db()->prepare('
                INSERT INTO guardians (student_id, relationship, name, mobile, email, occupation, is_primary, created_at)
                VALUES (:student_id, :rel, :name, :mobile, :email, :occupation, 1, NOW())
            ');
            return $stmt->execute([
                ':student_id' => $studentId,
                ':rel'        => $data['guardian_relationship'] ?? 'father',
                ':name'       => $data['guardian_name'],
                ':mobile'     => $data['guardian_mobile'] ?? null,
                ':email'      => $data['guardian_email'] ?? null,
                ':occupation' => $data['guardian_occupation'] ?? null,
            ]);
        }
    }

    /**
     * Upload a single Student Document.
     */
    public function uploadStudentDocument(int $studentId, string $docType, string $docName, array $file): array
    {
        $val = validate_upload($file);
        if (!$val['ok']) {
            return ['success' => false, 'message' => 'Upload failed: ' . $val['error']];
        }

        $targetDir = BASE_PATH . '/public/uploads/students';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
        $clean   = preg_replace('/[^a-zA-Z0-9_-]/', '_', $docName);
        $newName = 'doc_' . $studentId . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $dest    = $targetDir . '/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $webPath = '/uploads/students/' . $newName;

            $stmt = db()->prepare('
                INSERT INTO student_documents (
                    student_id, document_type, document_name, file_path, uploaded_by, verified, created_at
                ) VALUES (
                    :student_id, :doc_type, :doc_name, :file_path, :uploaded_by, 0, NOW()
                )
            ');

            $stmt->execute([
                ':student_id'  => $studentId,
                ':doc_type'    => in_array($docType, ['aadhar','birth_cert','tc','marksheet','other'], true) ? $docType : 'other',
                ':doc_name'    => !empty($docName) ? $docName : $file['name'],
                ':file_path'   => $webPath,
                ':uploaded_by' => auth_id() ?? 1,
            ]);

            return ['success' => true, 'message' => 'Document uploaded successfully!'];
        }

        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }

    /**
     * Upload and update Student Profile Photo.
     */
    public function uploadProfilePhoto(int $studentId, array $file): array
    {
        $val = validate_upload($file);
        if (!$val['ok']) {
            return ['success' => false, 'message' => 'Profile photo upload failed: ' . $val['error']];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $targetDir = BASE_PATH . '/public/uploads/profile_photos';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $newName = 'avatar_student_' . $studentId . '_' . time() . '.' . $ext;
        $dest    = $targetDir . '/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $webPath = '/uploads/profile_photos/' . $newName;

            $stmt = db()->prepare('UPDATE students SET photo_path = :photo_path WHERE id = :id');
            $stmt->execute([':photo_path' => $webPath, ':id' => $studentId]);

            return ['success' => true, 'message' => 'Profile photo updated successfully!', 'photo_path' => $webPath];
        }

        return ['success' => false, 'message' => 'Failed to save uploaded photo file.'];
    }
}

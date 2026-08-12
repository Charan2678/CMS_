<?php

declare(strict_types=1);

namespace App\Modules\Student\services;

use App\Core\Security;
use Exception;
use PDO;

class StudentService
{
    /**
     * Get students alias.
     */
    public function getStudents(int $collegeId = 1, int $page = 1, int $perPage = 100): array
    {
        return $this->getAllStudents($collegeId, [], $page, $perPage);
    }

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
     * Get Student record by ID.
     */
    public function getStudentById(int $studentId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM students WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $studentId]);
        return $stmt->fetch() ?: null;
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

        // Fetch Transport Subscription & Today's Checkin
        $tStmt = db()->prepare('
            SELECT ta.*, tr.route_name, v.registration_number AS bus_number, v.driver_name, v.driver_mobile
            FROM transport_allocations ta
            JOIN transport_routes tr ON tr.id = ta.route_id
            LEFT JOIN vehicles v ON v.id = tr.vehicle_id
            WHERE ta.student_id = :sid AND ta.status = "active"
            LIMIT 1
        ');
        $tStmt->execute([':sid' => $studentId]);
        $student['transport'] = $tStmt->fetch() ?: null;

        $cStmt = db()->prepare('
            SELECT tbp.*, v.registration_number AS bus_number, tr.route_name
            FROM transport_bus_passes tbp
            JOIN vehicles v ON v.id = tbp.vehicle_id
            JOIN transport_routes tr ON tr.id = tbp.route_id
            WHERE tbp.student_id = :sid
            ORDER BY tbp.id DESC
            LIMIT 1
        ');
        $cStmt->execute([':sid' => $studentId]);
        $student['bus_pass'] = $cStmt->fetch() ?: null;

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

            // 3. Insert Guardian Info & Auto-Provision Parent User Account
            $guardianId = null;
            $parentTempPassword = null;
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
                $guardianId = (int) db()->lastInsertId();

                // Auto-create Parent User Account
                $parentUsername = !empty($data['guardian_mobile']) ? preg_replace('/[^0-9]/', '', (string)$data['guardian_mobile']) : 'parent_' . strtolower($data['roll_number']);
                $parentEmail    = !empty($data['guardian_email']) ? $data['guardian_email'] : 'parent.' . strtolower($data['roll_number']) . '@kuppam.edu.in';
                $parentTempPassword = 'P' . substr(bin2hex(random_bytes(4)), 0, 7) . '!';
                $parentPassHash = Security::hashPassword($parentTempPassword);

                $puStmt = db()->prepare('
                    INSERT INTO users (
                        college_id, username, email, password_hash, role_id, linked_type, linked_id, is_active, must_change_password, created_by, created_at
                    ) VALUES (
                        :college_id, :username, :email, :password_hash, 11, "parent", :linked_id, 1, 1, :created_by, NOW()
                    )
                ');
                $puStmt->execute([
                    ':college_id'    => $data['college_id'] ?? 1,
                    ':username'      => $parentUsername,
                    ':email'         => $parentEmail,
                    ':password_hash' => $parentPassHash,
                    ':linked_id'     => $guardianId,
                    ':created_by'    => auth_id() ?? 1,
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

            // 5. System Action: Create Student User Login Account with Random Secure Temporary Password
            $rStmt = db()->prepare('SELECT id FROM roles WHERE code = "student" LIMIT 1');
            $rStmt->execute();
            $studentRoleId = (int) ($rStmt->fetchColumn() ?: 10);

            $studentTempPassword = 'S' . substr(bin2hex(random_bytes(4)), 0, 7) . '!';
            $defaultPasswordHash = Security::hashPassword($studentTempPassword);

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

            // Auto-dispatch credentials email to student and parent
            $this->sendCredentialsEmail($studentId, $studentTempPassword);
            if ($guardianId && $parentTempPassword) {
                $this->sendParentCredentialsEmail($guardianId, $parentTempPassword);
            }

            return [
                'success'    => true,
                'message'    => 'Student admitted successfully! Login accounts created for Student (Password: ' . $studentTempPassword . ') and Parent (Password: ' . ($parentTempPassword ?? 'N/A') . '). Credentials dispatched via email.',
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
    public function sendCredentialsEmail(int $studentId, ?string $tempPassword = null): bool
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
        $passToDisplay = $tempPassword ?? 'Student123!';

        $body = "
            <h2 style='color:#0284c7;'>Welcome to Kuppam Engineering College, {$name}!</h2>
            <p>Your student ERP portal account has been successfully generated. Below are your official login credentials to access your attendance, fees, timetable, results, and exam hall tickets:</p>
            <div style='background:#f8fafc; border:1px solid #cbd5e1; padding:15px 20px; border-radius:6px; margin:20px 0;'>
                <p style='margin:5px 0;'><strong>Portal Login URL:</strong> <a href='{$loginUrl}' style='color:#0284c7;'>{$loginUrl}</a></p>
                <p style='margin:5px 0;'><strong>Role Type:</strong> <span style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>Student</span></p>
                <p style='margin:5px 0;'><strong>Username (Roll Number):</strong> <code style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>{$rollNumber}</code></p>
                <p style='margin:5px 0;'><strong>Temporary Password:</strong> <code style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>{$passToDisplay}</code></p>
            </div>
            <p style='color:#dc2626; font-size:13px;'><strong>Security Notice:</strong> You will be prompted to set a new custom password upon your first login.</p>
        ";

        return send_mail($to, $subject, $body);
    }

    /**
     * Dispatch Login Credentials Email to Parent/Guardian.
     */
    public function sendParentCredentialsEmail(int $guardianId, ?string $tempPassword = null): bool
    {
        $stmt = db()->prepare('
            SELECT g.*, s.roll_number, s.first_name AS s_first, s.last_name AS s_last
            FROM guardians g
            JOIN students s ON s.id = g.student_id
            WHERE g.id = :gid LIMIT 1
        ');
        $stmt->execute([':gid' => $guardianId]);
        $guardian = $stmt->fetch();

        if (!$guardian || empty($guardian['email'])) {
            return false;
        }

        $parentName = $guardian['name'];
        $wardName   = $guardian['s_first'] . ' ' . $guardian['s_last'];
        $wardRoll   = $guardian['roll_number'];
        $username   = !empty($guardian['mobile']) ? preg_replace('/[^0-9]/', '', (string)$guardian['mobile']) : 'parent_' . strtolower($wardRoll);
        $to         = $guardian['email'];
        $subject    = "Kuppam Engineering College — Parent & Guardian Portal Credentials";
        $loginUrl   = env('APP_URL', 'http://localhost:8000/login');
        $passToDisplay = $tempPassword ?? 'Parent123!';

        $body = "
            <h2 style='color:#0284c7;'>Dear {$parentName},</h2>
            <p>Welcome to the Kuppam Engineering College Parent &amp; Guardian Portal. You can monitor your ward <strong>{$wardName} ({$wardRoll})</strong>'s daily attendance, published semester marks, fee dues, and class timetables in real time:</p>
            <div style='background:#f8fafc; border:1px solid #cbd5e1; padding:15px 20px; border-radius:6px; margin:20px 0;'>
                <p style='margin:5px 0;'><strong>Portal Login URL:</strong> <a href='{$loginUrl}' style='color:#0284c7;'>{$loginUrl}</a></p>
                <p style='margin:5px 0;'><strong>Role Type:</strong> <span style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>Parent</span></p>
                <p style='margin:5px 0;'><strong>Login ID (Mobile Number):</strong> <code style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>{$username}</code></p>
                <p style='margin:5px 0;'><strong>Temporary Password:</strong> <code style='background:#e2e8f0; padding:2px 6px; border-radius:4px;'>{$passToDisplay}</code></p>
            </div>
            <p style='color:#dc2626; font-size:13px;'><strong>Security Notice:</strong> Please change your temporary password upon logging into the portal.</p>
        ";

        return send_mail($to, $subject, $body);
    }

    /**
     * Manually Provision or Reset Parent Login for a Student.
     */
    public function provisionParentAccount(int $studentId): array
    {
        $student = $this->getStudentById($studentId);
        if (!$student) {
            return ['success' => false, 'message' => 'Student not found.'];
        }

        $gStmt = db()->prepare('SELECT * FROM guardians WHERE student_id = :sid AND is_primary = 1 LIMIT 1');
        $gStmt->execute([':sid' => $studentId]);
        $guardian = $gStmt->fetch();

        if (!$guardian) {
            return ['success' => false, 'message' => 'No guardian details found for this student. Please add a guardian record first.'];
        }

        $guardianId = (int) $guardian['id'];
        $parentUsername = !empty($guardian['mobile']) ? preg_replace('/[^0-9]/', '', (string)$guardian['mobile']) : 'parent_' . strtolower($student['roll_number']);
        $parentEmail = !empty($guardian['email']) ? $guardian['email'] : 'parent.' . strtolower($student['roll_number']) . '@kuppam.edu.in';
        $tempPass = 'P' . substr(bin2hex(random_bytes(4)), 0, 7) . '!';
        $passHash = Security::hashPassword($tempPass);

        // Check if user account already exists
        $uStmt = db()->prepare('SELECT id FROM users WHERE linked_type = "parent" AND linked_id = :gid LIMIT 1');
        $uStmt->execute([':gid' => $guardianId]);
        $existingUserId = $uStmt->fetchColumn();

        if ($existingUserId) {
            $upStmt = db()->prepare('UPDATE users SET username = :username, password_hash = :hash, must_change_password = 1 WHERE id = :uid');
            $upStmt->execute([':username' => $parentUsername, ':hash' => $passHash, ':uid' => $existingUserId]);
        } else {
            $inStmt = db()->prepare('
                INSERT INTO users (
                    college_id, username, email, password_hash, role_id, linked_type, linked_id, is_active, must_change_password, created_by, created_at
                ) VALUES (
                    1, :username, :email, :password_hash, 11, "parent", :linked_id, 1, 1, :created_by, NOW()
                )
            ');
            $inStmt->execute([
                ':username'      => $parentUsername,
                ':email'         => $parentEmail,
                ':password_hash' => $passHash,
                ':linked_id'     => $guardianId,
                ':created_by'    => auth_id() ?? 1,
            ]);
        }

        $this->sendParentCredentialsEmail($guardianId, $tempPass);

        return [
            'success'  => true,
            'message'  => 'Parent login generated successfully! Login ID: ' . $parentUsername . ', Temporary Password: ' . $tempPass . '. Credentials dispatched to email: ' . $parentEmail,
            'username' => $parentUsername,
            'password' => $tempPass,
        ];
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
            $ok = $stmt->execute([
                ':name'       => $data['guardian_name'],
                ':rel'        => $data['guardian_relationship'] ?? 'father',
                ':mobile'     => $data['guardian_mobile'] ?? null,
                ':email'      => $data['guardian_email'] ?? null,
                ':occupation' => $data['guardian_occupation'] ?? null,
                ':gid'        => $existingId
            ]);
            if ($ok) {
                $this->provisionParentAccount($studentId);
            }
            return $ok;
        } else {
            $stmt = db()->prepare('
                INSERT INTO guardians (student_id, relationship, name, mobile, email, occupation, is_primary, created_at)
                VALUES (:student_id, :rel, :name, :mobile, :email, :occupation, 1, NOW())
            ');
            $ok = $stmt->execute([
                ':student_id' => $studentId,
                ':rel'        => $data['guardian_relationship'] ?? 'father',
                ':name'       => $data['guardian_name'],
                ':mobile'     => $data['guardian_mobile'] ?? null,
                ':email'      => $data['guardian_email'] ?? null,
                ':occupation' => $data['guardian_occupation'] ?? null,
            ]);
            if ($ok) {
                $this->provisionParentAccount($studentId);
            }
            return $ok;
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

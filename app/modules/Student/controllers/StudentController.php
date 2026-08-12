<?php

declare(strict_types=1);

namespace App\Modules\Student\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Master\services\MasterService;
use App\Modules\Student\services\StudentService;

class StudentController extends Controller
{
    private StudentService $studentService;
    private MasterService $masterService;

    public function __construct()
    {
        $this->studentService = new StudentService();
        $this->masterService  = new MasterService();
    }

    /**
     * List all students.
     */
    public function index(): void
    {
        Permission::enforce('student.view');

        $filters = [
            'department_id' => query('department_id', ''),
            'course_id'     => query('course_id', ''),
            'status'        => query('status', ''),
            'search'        => query('search', ''),
        ];

        $page = (int) query('page', '1');
        $resultData  = $this->studentService->getAllStudents(1, $filters, $page, 25);
        $departments = $this->masterService->getDepartments(1);
        $courses     = $this->masterService->getCourses(1);

        $this->render('Student/views/index', [
            'title'       => 'Student Directory',
            'students'    => $resultData['data'],
            'pagination'  => $resultData,
            'departments' => $departments,
            'courses'     => $courses,
            'filters'     => $filters,
            'success'     => flash_get('success'),
        ], 'layout');
    }

    /**
     * Show student admission form and process admission.
     */
    public function admission(): void
    {
        Permission::enforce('student.create');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Security session expired. Please refresh and try again.';
            } else {
                $data = $_POST;
                $res  = $this->studentService->admitStudent($data, $_FILES);

                if ($res['success']) {
                    flash('success', $res['message']);
                    $this->redirect('/students/' . $res['student_id']);
                } else {
                    $error = $res['message'];
                }
            }
        }

        $academicYears = $this->masterService->getAcademicYears(1);
        $departments   = $this->masterService->getDepartments(1);
        $courses       = $this->masterService->getCourses(1);

        $allSemesters  = [];
        foreach ($courses as $c) {
            $sems = $this->masterService->getSemestersByCourse((int)$c['id']);
            foreach ($sems as $s) {
                $allSemesters[] = [
                    'id'          => $s['id'],
                    'course_id'   => $c['id'],
                    'number'      => $s['number'],
                    'display'     => "{$c['code']} - Semester {$s['number']}"
                ];
            }
        }

        $sections = $this->masterService->getSections();

        $this->render('Student/views/admission', [
            'title'         => 'New Student Admission',
            'academicYears' => $academicYears,
            'departments'   => $departments,
            'courses'       => $courses,
            'semesters'     => $allSemesters,
            'sections'      => $sections,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * Show 360-degree Student Profile.
     */
    public function show(string $id): void
    {
        Permission::enforce('student.view');

        $studentId = (int) $id;
        $student   = $this->studentService->getStudentProfile($studentId);

        if (!$student) {
            http_response_code(404);
            $this->render('Master/views/404', [], null);
            return;
        }

        $this->render('Student/views/show', [
            'title'   => "Student Profile: {$student['first_name']} {$student['last_name']}",
            'student' => $student,
        ], 'layout');
    }

    /**
     * Send or resend credentials email to student's personal email.
     */
    public function sendCredentials(string $id): void
    {
        Permission::enforce('student.edit');

        $studentId = (int) $id;
        if ($this->studentService->sendCredentialsEmail($studentId)) {
            flash('success', 'Login credentials and welcome email dispatched successfully to student email!');
        } else {
            flash('error', 'Failed to send credentials. Please ensure the student has a valid email address.');
        }

        $this->redirect('/students/' . $studentId);
    }

    /**
     * Provision or reset parent login account and dispatch credentials.
     */
    public function provisionParent(string $id): void
    {
        Permission::enforce('student.edit');

        $studentId = (int) $id;
        $res = $this->studentService->provisionParentAccount($studentId);

        if ($res['success']) {
            flash('success', $res['message']);
        } else {
            flash('error', $res['message']);
        }

        $this->redirect('/students/' . $studentId);
    }

    /**
     * My Profile page — works for all roles.
     * Students/Parents get the full profile & document center.
     * Admin/Staff/Faculty get a general account profile page.
     */
    public function myProfile(): void
    {
        if (!is_authenticated()) {
            $this->redirect('/login');
        }

        $userId   = auth_id();
        $roleType = auth_role();

        // ── Student / Parent: full profile & document center ──
        if (in_array($roleType, ['student', 'parent'])) {
            $attSvc    = new \App\Modules\Attendance\services\AttendanceService();
            $studentId = $attSvc->getStudentIdFromUser($userId);

            if (!$studentId) {
                flash('error', 'No linked student record found for your user account.');
                $this->redirect('/dashboard');
                return;
            }

            $error   = null;
            $success = null;

            if ($this->isPost()) {
                if (!csrf_verify($this->input('_csrf_token'))) {
                    $error = 'Security token invalid. Please refresh and try again.';
                } else {
                    $action = $this->input('_action', 'update_profile');

                    if ($action === 'update_profile') {
                        if ($this->studentService->updateStudentProfile($studentId, $_POST)) {
                            $success = 'Personal & contact details updated successfully!';
                        } else {
                            $error = 'Failed to update personal details.';
                        }
                    } elseif ($action === 'save_guardian') {
                        if ($this->studentService->saveGuardianDetails($studentId, $_POST)) {
                            $success = 'Parent & guardian information saved successfully!';
                        } else {
                            $error = 'Failed to save guardian information.';
                        }
                    } elseif ($action === 'upload_document') {
                        $docType = $this->input('document_type', 'other');
                        $docName = $this->input('document_name', 'Student Document');
                        $res     = $this->studentService->uploadStudentDocument($studentId, $docType, $docName, $_FILES['document_file'] ?? []);
                        if ($res['success']) {
                            $success = $res['message'];
                        } else {
                            $error = $res['message'];
                        }
                    } elseif ($action === 'upload_photo') {
                        $res = $this->studentService->uploadProfilePhoto($studentId, $_FILES['profile_photo'] ?? []);
                        if ($res['success']) {
                            $success = $res['message'];
                        } else {
                            $error = $res['message'];
                        }
                    } elseif ($action === 'change_password') {
                        $currPass = $this->input('current_password', '');
                        $newPass  = $this->input('new_password', '');
                        $confPass = $this->input('confirm_password', '');
                        if ($newPass !== $confPass) {
                            $error = 'New password and confirmation password do not match.';
                        } else {
                            $authSvc = new \App\Modules\Authentication\services\AuthService();
                            $res     = $authSvc->changePassword($userId, $currPass, $newPass);
                            if ($res['success']) {
                                $success = $res['message'];
                            } else {
                                $error = $res['message'];
                            }
                        }
                    }
                }
            }

            $student  = $this->studentService->getStudentProfile($studentId);
            $isParent = ($roleType === 'parent');
            $title    = $isParent
                ? 'Child Profile: ' . trim(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? ''))
                : 'My Profile & Document Center';

            $this->render('Student/views/profile', [
                'title'   => $title,
                'student' => $student,
                'error'   => $error,
                'success' => $success,
            ], 'layout');
            return;
        }

        // ── Admin / Staff / Faculty: general account profile ──
        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Security token invalid. Please refresh and try again.';
            } else {
                $currPass = $this->input('current_password', '');
                $newPass  = $this->input('new_password', '');
                $confPass = $this->input('confirm_password', '');

                if ($newPass !== $confPass) {
                    $error = 'New password and confirmation do not match.';
                } elseif (strlen($newPass) < 8) {
                    $error = 'New password must be at least 8 characters.';
                } else {
                    $authSvc = new \App\Modules\Authentication\services\AuthService();
                    $res     = $authSvc->changePassword($userId, $currPass, $newPass);
                    if ($res['success']) {
                        $success = $res['message'];
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $this->render('Student/views/account_profile', [
            'title'    => 'My Account',
            'username' => $_SESSION['username'] ?? '',
            'email'    => $_SESSION['email'] ?? '',
            'roleName' => $_SESSION['role_name'] ?? ucfirst($roleType),
            'userId'   => $userId,
            'error'    => $error,
            'success'  => $success,
        ], 'layout');
    }
}

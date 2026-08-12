<?php

declare(strict_types=1);

namespace App\Modules\Result\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Attendance\services\AttendanceService;
use App\Modules\Faculty\services\FacultyService;
use App\Modules\Master\services\MasterService;
use App\Modules\Result\services\ResultService;
use App\Modules\Result\services\AdmitCardService;

class ResultController extends Controller
{
    private ResultService $resultService;
    private MasterService $masterService;
    private FacultyService $facultyService;
    private AttendanceService $attendanceService;
    private AdmitCardService $admitCardService;

    public function __construct()
    {
        $this->resultService     = new ResultService();
        $this->masterService     = new MasterService();
        $this->facultyService    = new FacultyService();
        $this->attendanceService = new AttendanceService();
        $this->admitCardService  = new AdmitCardService();
    }

    /**
    /**
     * Timetable Grid Scheduler / Student Timetable View.
     */
    /**
     * HOD Class & Staff Timetable Scheduler / Management.
     */
    public function timetable(): void
    {
        // 1. Role Redirects for View-Only Users
        if (in_array(auth_role(), ['student', 'parent'], true)) {
            $this->studentTimetable();
            return;
        }

        if (auth_role() === 'faculty' && !Permission::has('timetable.manage')) {
            $this->staffTimetable();
            return;
        }

        Permission::enforce('timetable.manage');

        $error   = null;
        $success = null;

        $type           = strtolower(query('type', $this->input('type', 'student')));
        $sectionId      = (int) query('section_id', $this->input('section_id', '0'));
        $facultyId      = (int) query('faculty_id', $this->input('faculty_id', '0'));
        $departmentId   = (int) query('department_id', $this->input('department_id', '0'));
        $academicYearId = (int) query('academic_year_id', $this->input('academic_year_id', '0'));

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action', '');

                if ($action === 'delete') {
                    $slotId = (int) $this->input('slot_id', '0');
                    if ($slotId > 0 && $this->resultService->deleteTimetableSlot($slotId)) {
                        $success = 'Timetable slot deleted successfully.';
                    } else {
                        $error = 'Failed to delete timetable slot.';
                    }
                } elseif ($action === 'publish') {
                    $secId = (int) $this->input('section_id');
                    $ayId  = (int) $this->input('academic_year_id');
                    if ($secId > 0 && $ayId > 0 && $this->resultService->publishTimetable('STUDENT', $ayId, $secId, null, auth_id() ?? 1)) {
                        $success = '🎓 Student Timetable published successfully! It is now visible to students.';
                    } else {
                        $error = 'Please select a Section and Academic Year to publish.';
                    }
                } elseif ($action === 'unpublish') {
                    $secId = (int) $this->input('section_id');
                    $ayId  = (int) $this->input('academic_year_id');
                    if ($secId > 0 && $ayId > 0 && $this->resultService->unpublishTimetable('STUDENT', $ayId, $secId, null)) {
                        $success = 'Student Timetable set to DRAFT (unpublished).';
                    } else {
                        $error = 'Failed to unpublish timetable.';
                    }
                } elseif ($action === 'publish_staff') {
                    $facId = (int) $this->input('faculty_id');
                    $ayId  = (int) $this->input('academic_year_id');
                    if ($facId > 0 && $ayId > 0 && $this->resultService->publishTimetable('STAFF', $ayId, null, $facId, auth_id() ?? 1)) {
                        $success = '👨‍🏫 Staff Timetable published successfully! It is now visible to the faculty member.';
                    } else {
                        $error = 'Please select a Faculty member and Academic Year to publish.';
                    }
                } elseif ($action === 'unpublish_staff') {
                    $facId = (int) $this->input('faculty_id');
                    $ayId  = (int) $this->input('academic_year_id');
                    if ($facId > 0 && $ayId > 0 && $this->resultService->unpublishTimetable('STAFF', $ayId, null, $facId)) {
                        $success = 'Staff Timetable set to DRAFT (unpublished).';
                    } else {
                        $error = 'Failed to unpublish staff timetable.';
                    }
                } else {
                    // Slot Allocation
                    $data = [
                        'section_id'       => (int) $this->input('section_id'),
                        'academic_year_id' => (int) $this->input('academic_year_id'),
                        'day_of_week'      => $this->input('day_of_week'),
                        'period_number'    => (int) $this->input('period_number'),
                        'subject_id'       => (int) $this->input('subject_id'),
                        'faculty_id'       => (int) $this->input('faculty_id'),
                        'room_id'          => $this->input('room_id'),
                        'timetable_type'   => ($type === 'staff') ? 'STAFF' : 'STUDENT',
                    ];

                    $res = $this->resultService->saveTimetableSlotWithValidation($data);
                    if ($res['success']) {
                        $success = $res['message'];
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $sections      = $this->masterService->getSections();
        $academicYears = $this->masterService->getAcademicYears(1);
        $departments   = $this->masterService->getDepartments();
        $subjects      = $this->masterService->getSubjects();
        $facultyList   = $this->facultyService->getAllFaculty();
        $rooms         = $this->masterService->getRooms();

        // Ensure current academic year is default if non selected
        if ($academicYearId === 0 && !empty($academicYears)) {
            foreach ($academicYears as $ay) {
                if (!empty($ay['is_current'])) {
                    $academicYearId = (int) $ay['id'];
                    break;
                }
            }
            if ($academicYearId === 0) {
                $academicYearId = (int) $academicYears[0]['id'];
            }
        }

        $grid = [];
        $pubStatus = 'DRAFT';

        if ($type === 'staff') {
            if ($facultyId > 0 && $academicYearId > 0) {
                $grid = $this->resultService->getTimetableForFaculty($facultyId, $academicYearId, false);
                $pubStatus = $this->resultService->getPublicationStatus('STAFF', $academicYearId, null, $facultyId);
            }
        } else {
            if ($sectionId > 0 && $academicYearId > 0) {
                $grid = $this->resultService->getTimetableForSection($sectionId, $academicYearId, false);
                $pubStatus = $this->resultService->getPublicationStatus('STUDENT', $academicYearId, $sectionId, null);
            }
        }

        $this->render('Result/views/timetable', [
            'title'          => 'Timetable Management (Student & Staff)',
            'type'           => $type,
            'sections'       => $sections,
            'academicYears'  => $academicYears,
            'departments'    => $departments,
            'subjects'       => $subjects,
            'facultyList'    => $facultyList,
            'rooms'          => $rooms,
            'sectionId'      => $sectionId,
            'facultyId'      => $facultyId,
            'departmentId'   => $departmentId,
            'academicYearId' => $academicYearId,
            'grid'           => $grid,
            'pubStatus'      => $pubStatus,
            'periodConfig'   => ResultService::getPeriodConfig(),
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }

    /**
     * View Timetable for Student (View-only).
     */
    public function studentTimetable(): void
    {
        $userId = auth_id();
        $studentId = $userId ? $this->attendanceService->getStudentIdFromUser($userId) : null;

        $res = $studentId ? $this->resultService->getStudentTimetable($studentId) : ['info' => null, 'grid' => [], 'is_published' => false];

        $this->render('Result/views/student_timetable', [
            'title'           => 'My Class Timetable',
            'studentAcademic' => $res['info'],
            'grid'            => $res['grid'],
            'is_published'    => $res['is_published'] ?? false,
            'periodConfig'    => ResultService::getPeriodConfig(),
        ], 'layout');
    }

    /**
     * View Personal Staff Timetable for Faculty (View-only).
     */
    public function staffTimetable(): void
    {
        $userId = auth_id();

        // 1. Resolve faculty_id from linked_id on users table
        $uStmt = db()->prepare('SELECT linked_id FROM users WHERE id = :uid AND linked_type = "faculty" LIMIT 1');
        $uStmt->execute([':uid' => $userId]);
        $facultyId = (int) ($uStmt->fetchColumn() ?: 0);

        // 2. Fallback: resolve by email match
        if (!$facultyId) {
            $fStmt = db()->prepare('
                SELECT f.id FROM faculty f
                JOIN users u ON LOWER(u.email) = LOWER(f.email)
                WHERE u.id = :uid LIMIT 1
            ');
            $fStmt->execute([':uid' => $userId]);
            $facultyId = (int) ($fStmt->fetchColumn() ?: 0);
        }

        $res = $facultyId ? $this->resultService->getFacultyTimetable($facultyId) : ['info' => null, 'grid' => [], 'is_published' => false];

        $this->render('Result/views/staff_timetable', [
            'title'        => 'My Staff Timetable',
            'facultyInfo'  => $res['info'],
            'grid'         => $res['grid'],
            'is_published' => $res['is_published'] ?? false,
            'periodConfig' => ResultService::getPeriodConfig(),
        ], 'layout');
    }

    /**
     * Internal Marks Entry.
     */
    public function internalMarks(): void
    {
        Permission::enforce('marks.internal');

        $error   = null;
        $success = null;

        $sectionId      = (int) query('section_id', $this->input('section_id', '0'));
        $subjectId      = (int) query('subject_id', $this->input('subject_id', '0'));
        $academicYearId = (int) query('academic_year_id', $this->input('academic_year_id', '0'));
        $examType       = query('exam_type', $this->input('exam_type', 'cia1'));

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $marksMap = $_POST['marks'] ?? [];
                $maxMarks = (float) $this->input('max_marks', '25');

                if (empty($sectionId) || empty($subjectId) || empty($marksMap)) {
                    $error = 'Section, Subject, and Student marks are required.';
                } else {
                    if ($this->resultService->saveInternalMarks($sectionId, $subjectId, $academicYearId, $examType, $marksMap, $maxMarks)) {
                        $success = 'Internal marks recorded successfully.';
                    } else {
                        $error = 'Failed to save internal marks.';
                    }
                }
            }
        }

        $sections      = $this->masterService->getSections();
        $subjects      = $this->masterService->getSubjects();
        $academicYears = $this->masterService->getAcademicYears(1);

        $students = [];
        if ($sectionId > 0) {
            $students = $this->attendanceService->getStudentsForSection($sectionId);
        }

        $this->render('Result/views/internal_marks', [
            'title'          => 'Internal Marks Entry',
            'sections'       => $sections,
            'subjects'       => $subjects,
            'academicYears'  => $academicYears,
            'sectionId'      => $sectionId,
            'subjectId'      => $subjectId,
            'academicYearId' => $academicYearId,
            'examType'       => $examType,
            'students'       => $students,
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }

    /**
     * Results Engine & Publishing / Student Results.
     */
    public function results(): void
    {
        if (in_array(auth_role(), ['student', 'parent'], true)) {
            $this->studentResults();
            return;
        }
        Permission::enforce('result.publish');

        $error   = null;
        $success = null;

        $semesterId     = (int) query('semester_id', $this->input('semester_id', '0'));
        $academicYearId = (int) query('academic_year_id', $this->input('academic_year_id', '0'));
        $sectionId      = (int) query('section_id', $this->input('section_id', '0'));

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action');
                if ($action === 'publish') {
                    if ($this->resultService->publishResults($semesterId, $academicYearId)) {
                        $success = 'Semester results published successfully!';
                    } else {
                        $error = 'Failed to publish results.';
                    }
                } else {
                    $externalMarksMap = $_POST['external_marks'] ?? [];
                    if (empty($semesterId) || empty($academicYearId) || empty($externalMarksMap)) {
                        $error = 'Semester, Academic Year, and External Marks data are required.';
                    } else {
                        if ($this->resultService->saveExternalMarksAndComputeResult($semesterId, $academicYearId, $externalMarksMap, 100.0)) {
                            $success = 'External marks saved and semester results calculated!';
                        } else {
                            $error = 'Failed to process results calculation.';
                        }
                    }
                }
            }
        }

        $academicYears = $this->masterService->getAcademicYears(1);
        $courses       = $this->masterService->getCourses(1);
        $sections      = $this->masterService->getSections();

        $allSemesters = [];
        foreach ($courses as $c) {
            $sems = $this->masterService->getSemestersByCourse((int)$c['id']);
            foreach ($sems as $s) {
                $allSemesters[] = [
                    'id'      => $s['id'],
                    'display' => "{$c['code']} - Semester {$s['number']}"
                ];
            }
        }

        $results  = [];
        $students = [];
        $subjects = [];

        if ($semesterId > 0 && $academicYearId > 0) {
            $results = $this->resultService->getResults($semesterId, $academicYearId);
            if ($sectionId > 0) {
                $students = $this->attendanceService->getStudentsForSection($sectionId);
                $subjects = $this->masterService->getSubjects($semesterId);
            }
        }

        $this->render('Result/views/results', [
            'title'          => 'Semester Results & Marks Engine',
            'academicYears'  => $academicYears,
            'semesters'      => $allSemesters,
            'sections'       => $sections,
            'semesterId'     => $semesterId,
            'academicYearId' => $academicYearId,
            'sectionId'      => $sectionId,
            'results'        => $results,
            'students'       => $students,
            'subjects'       => $subjects,
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }

    /**
     * View Semester Results & Marksheets for Logged-In Student.
     * Now extended to show Mid + Semester results across all academic years.
     */
    public function studentResults(): void
    {
        $userId    = auth_id();
        $studentId = $userId ? $this->attendanceService->getStudentIdFromUser($userId) : null;

        // Full examination dashboard data (mid exams + semester results per AY)
        $examData  = $studentId ? $this->resultService->getStudentFullExamResults($studentId) : [];

        // Keep old $semesters for backward-compat (any partial references elsewhere)
        $semesters = $studentId ? $this->resultService->getStudentAllSemesterResults($studentId) : [];

        $this->render('Result/views/student_results', [
            'title'     => 'My Examination Results & Marksheets',
            'examData'  => $examData,
            'semesters' => $semesters,
        ], 'layout');
    }

    /**
     * Official Exam Hall Ticket / Admit Card View (Student Portal & Admin Preview).
     */
    public function admitCard(): void
    {
        $userId    = auth_id();
        $studentId = (int) query('student_id', '0');

        if (in_array(auth_role(), ['student', 'parent'], true)) {
            $studentId = $userId ? $this->attendanceService->getStudentIdFromUser($userId) : 0;
        } else {
            Permission::enforce('result.publish');
        }

        if (!$studentId) {
            flash('error', 'Student record not found.');
            $this->redirect('/dashboard');
        }

        // Get current academic placement
        $placementStmt = db()->prepare('
            SELECT sa.*, s.roll_number 
            FROM student_academics sa 
            JOIN students s ON s.id = sa.student_id
            WHERE sa.student_id = :student_id AND sa.is_current = 1 
            LIMIT 1
        ');
        $placementStmt->execute([':student_id' => $studentId]);
        $placement = $placementStmt->fetch();

        if (!$placement) {
            $this->render('Result/views/admit_card', [
                'title' => 'Official Exam Hall Ticket / Admit Card',
                'data'  => ['success' => false, 'message' => 'No active academic enrollment found for current semester.']
            ], 'layout');
            return;
        }

        $academicYearId = (int) $placement['academic_year_id'];
        $semesterId     = (int) $placement['semester_id'];

        $data = $this->admitCardService->getOrGenerateHallTicket($studentId, $academicYearId, $semesterId);

        $this->render('Result/views/admit_card', [
            'title' => 'Official Exam Hall Ticket / Admit Card',
            'data'  => $data,
        ], 'layout');
    }

    /**
     * Admin / Exam Cell Admit Card & Eligibility Management Dashboard.
     */
    public function admitCardManage(): void
    {
        Permission::enforce('result.publish');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Security token invalid.';
            } else {
                $studentId      = (int) $this->input('student_id');
                $academicYearId = (int) $this->input('academic_year_id');
                $semesterId     = (int) $this->input('semester_id');
                $reason         = trim($this->input('condonation_reason', 'Condoned by HOD/Principal for official duties.'));

                if ($this->admitCardService->condoneShortage($studentId, $academicYearId, $semesterId, auth_id() ?? 1, $reason)) {
                    $success = 'Attendance shortage condoned successfully! Hall ticket status set to Eligible.';
                } else {
                    $error = 'Failed to record attendance condonation.';
                }
            }
        }

        $sectionId      = (int) query('section_id', $this->input('section_id', '0'));
        $academicYearId = (int) query('academic_year_id', $this->input('academic_year_id', '0'));
        $rollNumber     = trim(query('roll_number', $this->input('roll_number', '')));

        $sections      = $this->masterService->getSections();
        $academicYears = $this->masterService->getAcademicYears(1);
        $report        = [];

        if (!empty($rollNumber)) {
            $studStmt = db()->prepare('
                SELECT s.*, sa.academic_year_id, sa.semester_id, sa.section_id 
                FROM students s 
                JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
                WHERE s.roll_number = :roll
                LIMIT 1
            ');
            $studStmt->execute([':roll' => $rollNumber]);
            $st = $studStmt->fetch(\PDO::FETCH_ASSOC);

            if ($st) {
                $academicYearId = (int) $st['academic_year_id'];
                $semId = (int) $st['semester_id'];
                $eligibility = $this->admitCardService->checkEligibility((int)$st['id'], $academicYearId, $semId);
                $report[] = [
                    'student'     => $st,
                    'eligibility' => $eligibility,
                    'semester_id' => $semId,
                ];
            } else {
                $error = 'Student with Roll Number "' . $rollNumber . '" not found.';
            }
        } elseif ($sectionId > 0 && $academicYearId > 0) {
            $students = $this->attendanceService->getStudentsForSection($sectionId);
            foreach ($students as $st) {
                $placementStmt = db()->prepare('SELECT semester_id FROM student_academics WHERE student_id = :id AND is_current = 1 LIMIT 1');
                $placementStmt->execute([':id' => $st['id']]);
                $semId = (int) ($placementStmt->fetchColumn() ?: 1);

                $eligibility = $this->admitCardService->checkEligibility((int)$st['id'], $academicYearId, $semId);
                $report[] = [
                    'student'     => $st,
                    'eligibility' => $eligibility,
                    'semester_id' => $semId,
                ];
            }
        }

        $this->render('Result/views/admit_card_manage', [
            'title'          => 'Exam Eligibility & Hall Ticket Management',
            'sections'       => $sections,
            'academicYears'  => $academicYears,
            'sectionId'      => $sectionId,
            'academicYearId' => $academicYearId,
            'report'         => $report,
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }

    /**
     * Exam Timetable Scheduler.
     */
    public function examTimetable(): void
    {
        $role = auth_role();
        $error = null;
        $success = null;

        $academicYearId = (int) query('academic_year_id', $this->input('academic_year_id', '0'));
        $departmentId   = (int) query('department_id', $this->input('department_id', '0'));
        $courseId       = (int) query('course_id', $this->input('course_id', '0'));
        $semesterId     = (int) query('semester_id', $this->input('semester_id', '0'));

        // Server-side validation of parent-child relationships to prevent state desync
        if ($departmentId > 0 && $courseId > 0) {
            $isValidCourse = db()->query("SELECT COUNT(*) FROM courses WHERE id = $courseId AND department_id = $departmentId")->fetchColumn() > 0;
            if (!$isValidCourse) {
                $courseId = 0;
                $semesterId = 0;
            }
        }

        $examType       = query('exam_type', $this->input('exam_type', 'regular'));
        if (!in_array($examType, ['regular', 'arrear'], true)) {
            $examType = 'regular';
        }
        $rollNumber     = trim($this->input('roll_number', ''));
        $studentInfo    = null;

        // Handle Form Submission
        if ($this->isPost() && in_array($role, ['super_admin', 'admin', 'head_of_coe'], true)) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $examDates = $_POST['exam_date'] ?? [];
                $timeSlots = $_POST['time_slot'] ?? [];
                $roomIds   = $_POST['room_id'] ?? [];

                db()->beginTransaction();
                try {
                    foreach ($examDates as $subjectId => $date) {
                        if (empty($date)) continue;

                        $subjId = (int) $subjectId;
                        $slot   = $timeSlots[$subjId] ?? '09:30 AM - 12:30 PM';
                        $roomId = !empty($roomIds[$subjId]) ? (int)$roomIds[$subjId] : null;

                        // Check if exists
                        $chk = db()->prepare('
                            SELECT id FROM exam_schedules 
                            WHERE academic_year_id = :acyr AND semester_id = :sem AND subject_id = :subj AND exam_type = :type
                        ');
                        $chk->execute([
                            ':acyr' => $academicYearId,
                            ':sem'  => $semesterId,
                            ':subj' => $subjId,
                            ':type' => $examType
                        ]);
                        $existingId = $chk->fetchColumn();

                        if ($existingId) {
                            $upd = db()->prepare('
                                UPDATE exam_schedules 
                                SET exam_date = :date, time_slot = :slot, room_id = :room
                                WHERE id = :id
                            ');
                            $upd->execute([
                                ':date' => $date,
                                ':slot' => $slot,
                                ':room' => $roomId,
                                ':id'   => $existingId
                            ]);
                        } else {
                            $ins = db()->prepare('
                                INSERT INTO exam_schedules (academic_year_id, department_id, course_id, semester_id, exam_type, subject_id, exam_date, time_slot, room_id)
                                VALUES (:acyr, :dept, :course, :sem, :type, :subj, :date, :slot, :room)
                            ');
                            $ins->execute([
                                ':acyr'   => $academicYearId,
                                ':dept'   => $departmentId,
                                ':course' => $courseId,
                                ':sem'    => $semesterId,
                                ':type'   => $examType,
                                ':subj'   => $subjId,
                                ':date'   => $date,
                                ':slot'   => $slot,
                                ':room'   => $roomId
                            ]);
                        }
                    }
                    db()->commit();
                    $success = ucfirst($examType) . ' exam timetable updated successfully.';
                } catch (\Exception $e) {
                    db()->rollBack();
                    $error = 'Failed to save exam schedule: ' . $e->getMessage();
                }
            }
        }

        // Fetch basic options for dropdowns
        $academicYears = $this->masterService->getAcademicYears(1);
        $departments   = $this->masterService->getDepartments(1);
        $courses       = $departmentId > 0 ? db()->query("SELECT * FROM courses WHERE department_id = $departmentId AND status = 1")->fetchAll(\PDO::FETCH_ASSOC) : [];
        $semesters     = $courseId > 0 ? $this->masterService->getSemestersByCourse($courseId) : [];
        
        // Rooms list
        $rooms = db()->query('SELECT * FROM rooms WHERE status = 1 ORDER BY name ASC')->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $subjectsList = [];
        $arrearSubjectsList = [];
        $schedules = [];
        $arrearSchedules = [];

        // If search by roll number (e.g. for student view or roll number search)
        if ($role === 'student' || !empty($rollNumber)) {
            $searchUser = ($role === 'student') ? ($_SESSION['username'] ?? '') : $rollNumber;
            
            $studStmt = db()->prepare('
                SELECT s.id AS student_id, sa.academic_year_id, sa.department_id, sa.course_id, sa.semester_id, s.first_name, s.last_name, s.roll_number
                FROM students s
                JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
                WHERE s.roll_number = :roll
                LIMIT 1
            ');
            $studStmt->execute([':roll' => $searchUser]);
            $studentInfo = $studStmt->fetch(\PDO::FETCH_ASSOC);

            if ($studentInfo) {
                $studentId = (int) $studentInfo['student_id'];
                $academicYearId = (int) $studentInfo['academic_year_id'];
                $departmentId   = (int) $studentInfo['department_id'];
                $courseId       = (int) $studentInfo['course_id'];
                $semesterId     = (int) $studentInfo['semester_id'];

                // 1. Regular Subjects
                $subjectsStmt = db()->prepare('SELECT * FROM subjects WHERE semester_id = :sem AND status = 1');
                $subjectsStmt->execute([':sem' => $semesterId]);
                $subjectsList = $subjectsStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

                // 2. Arrear Subjects (failed and not subsequently passed)
                $arrearStmt = db()->prepare("
                    SELECT sub.*, em.grade, em.academic_year_id AS fail_year 
                    FROM external_marks em
                    JOIN subjects sub ON sub.id = em.subject_id
                    WHERE em.student_id = :student_id AND em.grade = 'F'
                      AND NOT EXISTS (
                          SELECT 1 FROM external_marks em2 
                          WHERE em2.student_id = em.student_id 
                            AND em2.subject_id = em.subject_id 
                            AND em2.grade != 'F' 
                            AND em2.created_at > em.created_at
                      )
                ");
                $arrearStmt->execute([':student_id' => $studentId]);
                $arrearSubjectsList = $arrearStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            }
        } elseif ($semesterId > 0) {
            $subjectsStmt = db()->prepare('SELECT * FROM subjects WHERE semester_id = :sem AND status = 1');
            $subjectsStmt->execute([':sem' => $semesterId]);
            $subjectsList = $subjectsStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        }

        // Fetch regular schedules for filter selection
        if ($semesterId > 0 && $academicYearId > 0) {
            $schedStmt = db()->prepare('
                SELECT * FROM exam_schedules 
                WHERE academic_year_id = :acyr AND semester_id = :sem AND exam_type = :type
            ');
            $schedStmt->execute([
                ':acyr' => $academicYearId,
                ':sem'  => $semesterId,
                ':type' => $examType
            ]);
            $schedulesRaw = $schedStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            foreach ($schedulesRaw as $s) {
                $schedules[$s['subject_id']] = $s;
            }
        }

        // Fetch student's consolidated exam schedules (both regular and arrear)
        if ($studentInfo) {
            $regStmt = db()->prepare('
                SELECT * FROM exam_schedules 
                WHERE academic_year_id = :acyr AND semester_id = :sem AND exam_type = \'regular\'
            ');
            $regStmt->execute([
                ':acyr' => $studentInfo['academic_year_id'],
                ':sem'  => $studentInfo['semester_id']
            ]);
            $regRaw = $regStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            $schedules = [];
            foreach ($regRaw as $s) {
                $schedules[$s['subject_id']] = $s;
            }

            if (!empty($arrearSubjectsList)) {
                $arrSubjIds = array_map(fn($item) => (int)$item['id'], $arrearSubjectsList);
                $placeholders = implode(',', array_fill(0, count($arrSubjIds), '?'));
                
                $arrStmt = db()->prepare("
                    SELECT * FROM exam_schedules 
                    WHERE subject_id IN ($placeholders) AND exam_type = 'arrear'
                ");
                $arrStmt->execute($arrSubjIds);
                $arrRaw = $arrStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                $arrearSchedules = [];
                foreach ($arrRaw as $s) {
                    $arrearSchedules[$s['subject_id']] = $s;
                }
            }
        }

        $this->render('Result/views/exam_timetable', [
            'title'              => 'Exam Timetable Scheduler',
            'role'               => $role,
            'academicYears'      => $academicYears,
            'departments'        => $departments,
            'courses'            => $courses,
            'semesters'          => $semesters,
            'rooms'              => $rooms,
            'academicYearId'     => $academicYearId,
            'departmentId'       => $departmentId,
            'courseId'           => $courseId,
            'semesterId'         => $semesterId,
            'examType'           => $examType,
            'rollNumber'         => $rollNumber,
            'studentInfo'        => $studentInfo,
            'subjectsList'       => $subjectsList,
            'arrearSubjectsList' => $arrearSubjectsList,
            'schedules'          => $schedules,
            'arrearSchedules'    => $arrearSchedules,
            'error'              => $error,
            'success'            => $success,
        ], 'layout');
    }
}



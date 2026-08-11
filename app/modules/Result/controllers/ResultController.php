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
    public function timetable(): void
    {
        if (in_array(auth_role(), ['student', 'parent'], true)) {
            $this->studentTimetable();
            return;
        }

        Permission::enforce('timetable.manage');

        $error   = null;
        $success = null;

        $sectionId      = (int) query('section_id', $this->input('section_id', '0'));
        $academicYearId = (int) query('academic_year_id', $this->input('academic_year_id', '0'));

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'section_id'       => (int) $this->input('section_id'),
                    'academic_year_id' => (int) $this->input('academic_year_id'),
                    'day_of_week'      => $this->input('day_of_week'),
                    'period_number'    => (int) $this->input('period_number'),
                    'subject_id'       => (int) $this->input('subject_id'),
                    'faculty_id'       => (int) $this->input('faculty_id'),
                    'start_time'       => $this->input('start_time', '09:00:00'),
                    'end_time'         => $this->input('end_time', '10:00:00'),
                ];

                if (empty($data['section_id']) || empty($data['subject_id']) || empty($data['faculty_id'])) {
                    $error = 'Section, Subject, and Faculty are required.';
                } else {
                    if ($this->resultService->saveTimetableSlot($data)) {
                        $success = 'Timetable slot allocated successfully.';
                    } else {
                        $error = 'Failed to allocate timetable slot.';
                    }
                }
            }
        }

        $sections      = $this->masterService->getSections();
        $academicYears = $this->masterService->getAcademicYears(1);
        $subjects      = $this->masterService->getSubjects();
        $facultyList   = $this->facultyService->getAllFaculty();

        $grid = [];
        if ($sectionId > 0 && $academicYearId > 0) {
            $grid = $this->resultService->getTimetableForSection($sectionId, $academicYearId);
        }

        $this->render('Result/views/timetable', [
            'title'          => 'Class Timetable Scheduler',
            'sections'       => $sections,
            'academicYears'  => $academicYears,
            'subjects'       => $subjects,
            'facultyList'    => $facultyList,
            'sectionId'      => $sectionId,
            'academicYearId' => $academicYearId,
            'grid'           => $grid,
            'error'          => $error,
            'success'        => $success,
        ], 'layout');
    }

    /**
     * View Timetable for Student.
     */
    public function studentTimetable(): void
    {
        $userId = auth_id();
        $studentId = $userId ? $this->attendanceService->getStudentIdFromUser($userId) : null;

        $res = $studentId ? $this->resultService->getStudentTimetable($studentId) : ['info' => null, 'grid' => []];

        $this->render('Result/views/student_timetable', [
            'title'           => 'My Class Timetable',
            'studentAcademic' => $res['info'],
            'grid'            => $res['grid'],
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
     */
    public function studentResults(): void
    {
        $userId = auth_id();
        $studentId = $userId ? $this->attendanceService->getStudentIdFromUser($userId) : null;

        $semesters = $studentId ? $this->resultService->getStudentAllSemesterResults($studentId) : [];

        $this->render('Result/views/student_results', [
            'title'     => 'My Semester Results',
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

        $sections      = $this->masterService->getSections();
        $academicYears = $this->masterService->getAcademicYears(1);
        $report        = [];

        if ($sectionId > 0 && $academicYearId > 0) {
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
}

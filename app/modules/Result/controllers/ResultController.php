<?php

declare(strict_types=1);

namespace App\Modules\Result\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Attendance\services\AttendanceService;
use App\Modules\Faculty\services\FacultyService;
use App\Modules\Master\services\MasterService;
use App\Modules\Result\services\ResultService;

class ResultController extends Controller
{
    private ResultService $resultService;
    private MasterService $masterService;
    private FacultyService $facultyService;
    private AttendanceService $attendanceService;

    public function __construct()
    {
        $this->resultService     = new ResultService();
        $this->masterService     = new MasterService();
        $this->facultyService    = new FacultyService();
        $this->attendanceService = new AttendanceService();
    }

    /**
    /**
     * Timetable Grid Scheduler / Student Timetable View.
     */
    public function timetable(): void
    {
        if (auth_role() === 'student') {
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
        if (auth_role() === 'student') {
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
}

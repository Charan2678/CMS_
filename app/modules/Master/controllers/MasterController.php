<?php

declare(strict_types=1);

namespace App\Modules\Master\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Master\services\MasterService;

class MasterController extends Controller
{
    private MasterService $masterService;

    public function __construct()
    {
        $this->masterService = new MasterService();
    }

    /**
     * Manage College Info
     */
    public function collegeInfo(): void
    {
        Permission::enforce('master.college.view');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('master.college.edit');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'name'               => $this->input('name'),
                    'code'               => $this->input('code'),
                    'address'            => $this->input('address'),
                    'city'               => $this->input('city'),
                    'state'              => $this->input('state'),
                    'pincode'            => $this->input('pincode'),
                    'phone'              => $this->input('phone'),
                    'email'              => $this->input('email'),
                    'website'            => $this->input('website'),
                    'affiliation_body'   => $this->input('affiliation_body'),
                    'affiliation_number' => $this->input('affiliation_number'),
                ];

                if ($this->masterService->updateCollegeInfo(1, $data)) {
                    $success = 'College information updated successfully.';
                } else {
                    $error = 'Failed to update college information.';
                }
            }
        }

        $college = $this->masterService->getCollegeInfo(1);

        $this->render('Master/views/college_info', [
            'title'   => 'College Information',
            'college' => $college,
            'error'   => $error,
            'success' => $success,
        ], 'layout');
    }

    /**
     * Manage Academic Years
     */
    public function academicYears(): void
    {
        Permission::enforce('master.academicyear.view');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('master.academicyear.create');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $action = $this->input('_action');
                if ($action === 'set_current') {
                    $ayId = (int) $this->input('academic_year_id');
                    if ($this->masterService->setCurrentAcademicYear($ayId, 1)) {
                        $success = 'Active academic year updated.';
                    } else {
                        $error = 'Failed to update active academic year.';
                    }
                } else {
                    $data = [
                        'college_id' => 1,
                        'name'       => $this->input('name'),
                        'start_date' => $this->input('start_date'),
                        'end_date'   => $this->input('end_date'),
                        'is_current' => (int) $this->input('is_current'),
                        'status'     => (int) $this->input('status', '1'),
                    ];

                    if (empty($data['name']) || empty($data['start_date']) || empty($data['end_date'])) {
                        $error = 'Please fill in all required fields.';
                    } else {
                        if ($this->masterService->createAcademicYear($data)) {
                            $success = 'Academic year created successfully.';
                        } else {
                            $error = 'Failed to create academic year.';
                        }
                    }
                }
            }
        }

        $academicYears = $this->masterService->getAcademicYears(1);

        $this->render('Master/views/academic_years', [
            'title'         => 'Academic Years',
            'academicYears' => $academicYears,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * Manage Departments
     */
    public function departments(): void
    {
        Permission::enforce('master.department.view');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('master.department.create');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'college_id'       => 1,
                    'name'             => $this->input('name'),
                    'code'             => $this->input('code'),
                    'established_year' => $this->input('established_year') ?: null,
                    'status'           => (int) $this->input('status', '1'),
                ];

                if (empty($data['name']) || empty($data['code'])) {
                    $error = 'Department name and code are required.';
                } else {
                    if ($this->masterService->createDepartment($data)) {
                        $success = 'Department created successfully.';
                    } else {
                        $error = 'Failed to create department. Code must be unique.';
                    }
                }
            }
        }

        $departments = $this->masterService->getDepartments(1);

        $this->render('Master/views/departments', [
            'title'       => 'Departments',
            'departments' => $departments,
            'error'       => $error,
            'success'     => $success,
        ], 'layout');
    }

    /**
     * Manage Courses & Semesters
     */
    public function courses(): void
    {
        Permission::enforce('master.course.view');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('master.course.create');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'department_id'   => (int) $this->input('department_id'),
                    'name'            => $this->input('name'),
                    'code'            => $this->input('code'),
                    'degree_type'     => $this->input('degree_type'),
                    'duration_years'  => (int) $this->input('duration_years'),
                    'total_semesters' => (int) $this->input('total_semesters'),
                    'status'          => (int) $this->input('status', '1'),
                ];

                if (empty($data['department_id']) || empty($data['name']) || empty($data['code'])) {
                    $error = 'Please fill in all required course fields.';
                } else {
                    if ($this->masterService->createCourse($data)) {
                        $success = 'Course created successfully with all semesters automatically generated.';
                    } else {
                        $error = 'Failed to create course. Code must be unique per department.';
                    }
                }
            }
        }

        $courses     = $this->masterService->getCourses(1);
        $departments = $this->masterService->getDepartments(1);

        $this->render('Master/views/courses', [
            'title'       => 'Courses & Semesters',
            'courses'     => $courses,
            'departments' => $departments,
            'error'       => $error,
            'success'     => $success,
        ], 'layout');
    }

    /**
     * Manage Sections
     */
    public function sections(): void
    {
        Permission::enforce('master.section.view');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('master.section.create');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'semester_id'      => (int) $this->input('semester_id'),
                    'academic_year_id' => (int) $this->input('academic_year_id'),
                    'name'             => $this->input('name'),
                    'max_strength'     => (int) $this->input('max_strength', '60'),
                    'status'           => (int) $this->input('status', '1'),
                ];

                if (empty($data['semester_id']) || empty($data['academic_year_id']) || empty($data['name'])) {
                    $error = 'Semester, Academic Year, and Section Name are required.';
                } else {
                    if ($this->masterService->createSection($data)) {
                        $success = 'Section created successfully.';
                    } else {
                        $error = 'Failed to create section. Section name must be unique for the semester & academic year.';
                    }
                }
            }
        }

        $sections      = $this->masterService->getSections();
        $courses       = $this->masterService->getCourses(1);
        $academicYears = $this->masterService->getAcademicYears(1);

        // Flatten semesters for selection dropdown
        $allSemesters = [];
        foreach ($courses as $c) {
            $sems = $this->masterService->getSemestersByCourse((int)$c['id']);
            foreach ($sems as $s) {
                $allSemesters[] = [
                    'id'          => $s['id'],
                    'display'     => "{$c['code']} - Semester {$s['number']}",
                    'course_name' => $c['name']
                ];
            }
        }

        $this->render('Master/views/sections', [
            'title'         => 'Sections',
            'sections'      => $sections,
            'academicYears' => $academicYears,
            'semesters'     => $allSemesters,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * Manage Subjects
     */
    public function subjects(): void
    {
        Permission::enforce('master.subject.view');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('master.subject.create');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'semester_id'         => (int) $this->input('semester_id'),
                    'name'                => $this->input('name'),
                    'code'                => $this->input('code'),
                    'type'                => $this->input('type', 'theory'),
                    'credits'             => (float) $this->input('credits', '3.0'),
                    'max_internal_marks'  => (int) $this->input('max_internal_marks', '25'),
                    'max_external_marks'  => (int) $this->input('max_external_marks', '75'),
                    'pass_internal_marks' => (int) $this->input('pass_internal_marks', '10'),
                    'pass_external_marks' => (int) $this->input('pass_external_marks', '30'),
                    'status'              => (int) $this->input('status', '1'),
                ];

                if (empty($data['semester_id']) || empty($data['name']) || empty($data['code'])) {
                    $error = 'Semester, Subject Name, and Subject Code are required.';
                } else {
                    if ($this->masterService->createSubject($data)) {
                        $success = 'Subject created successfully.';
                    } else {
                        $error = 'Failed to create subject. Subject code must be unique within the semester.';
                    }
                }
            }
        }

        $subjects = $this->masterService->getSubjects();
        $courses  = $this->masterService->getCourses(1);

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

        $this->render('Master/views/subjects', [
            'title'     => 'Subjects',
            'subjects'  => $subjects,
            'semesters' => $allSemesters,
            'error'     => $error,
            'success'   => $success,
        ], 'layout');
    }
}

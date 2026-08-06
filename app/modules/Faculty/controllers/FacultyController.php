<?php

declare(strict_types=1);

namespace App\Modules\Faculty\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Faculty\services\FacultyService;
use App\Modules\Master\services\MasterService;

class FacultyController extends Controller
{
    private FacultyService $facultyService;
    private MasterService $masterService;

    public function __construct()
    {
        $this->facultyService = new FacultyService();
        $this->masterService  = new MasterService();
    }

    /**
     * List all faculty members.
     */
    public function index(): void
    {
        Permission::enforce('faculty.view');

        $filters = [
            'department_id' => query('department_id', ''),
            'status'        => query('status', ''),
            'search'        => query('search', ''),
        ];

        $facultyList = $this->facultyService->getAllFaculty($filters);
        $departments = $this->masterService->getDepartments(1);

        $this->render('Faculty/views/index', [
            'title'       => 'Faculty Directory',
            'facultyList' => $facultyList,
            'departments' => $departments,
            'filters'     => $filters,
            'success'     => flash_get('success'),
        ], 'layout');
    }

    /**
     * Onboard new faculty member.
     */
    public function create(): void
    {
        Permission::enforce('faculty.create');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Security session expired. Please refresh and try again.';
            } else {
                $data = $_POST;
                $res  = $this->facultyService->createFaculty($data);

                if ($res['success']) {
                    flash('success', $res['message']);
                    $this->redirect('/faculty/' . $res['faculty_id']);
                } else {
                    $error = $res['message'];
                }
            }
        }

        $departments  = $this->masterService->getDepartments(1);
        $designations = $this->facultyService->getDesignations(1);

        $this->render('Faculty/views/create', [
            'title'        => 'Faculty Onboarding',
            'departments'  => $departments,
            'designations' => $designations,
            'error'        => $error,
            'success'      => $success,
        ], 'layout');
    }

    /**
     * 360-degree Faculty Profile.
     */
    public function show(string $id): void
    {
        Permission::enforce('faculty.view');

        $facultyId = (int) $id;
        $faculty   = $this->facultyService->getFacultyProfile($facultyId);

        if (!$faculty) {
            http_response_code(404);
            $this->render('Master/views/404', [], null);
            return;
        }

        $this->render('Faculty/views/show', [
            'title'   => "Faculty Profile: {$faculty['first_name']} {$faculty['last_name']}",
            'faculty' => $faculty,
        ], 'layout');
    }

    /**
     * Assign Subject and Section to Faculty.
     */
    public function assignSubject(): void
    {
        Permission::enforce('faculty.assign_subject');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'faculty_id'       => (int) $this->input('faculty_id'),
                    'subject_id'       => (int) $this->input('subject_id'),
                    'section_id'       => (int) $this->input('section_id'),
                    'academic_year_id' => (int) $this->input('academic_year_id'),
                ];

                if (empty($data['faculty_id']) || empty($data['subject_id']) || empty($data['section_id']) || empty($data['academic_year_id'])) {
                    $error = 'All fields are required to allocate a subject assignment.';
                } else {
                    if ($this->facultyService->assignSubject($data)) {
                        $success = 'Subject & Section assigned to faculty successfully.';
                    } else {
                        $error = 'Failed to assign subject.';
                    }
                }
            }
        }

        $facultyList   = $this->facultyService->getAllFaculty();
        $academicYears = $this->masterService->getAcademicYears(1);
        $subjects      = $this->masterService->getSubjects();
        $sections      = $this->masterService->getSections();

        $this->render('Faculty/views/assign_subject', [
            'title'         => 'Assign Subject & Section to Faculty',
            'facultyList'   => $facultyList,
            'academicYears' => $academicYears,
            'subjects'      => $subjects,
            'sections'      => $sections,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * Manage Designations.
     */
    public function designations(): void
    {
        Permission::enforce('faculty.view');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            Permission::enforce('faculty.create');
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'college_id' => 1,
                    'name'       => $this->input('name'),
                    'code'       => $this->input('code'),
                    'level'      => (int) $this->input('level', '1'),
                    'status'     => (int) $this->input('status', '1'),
                ];

                if (empty($data['name'])) {
                    $error = 'Designation name is required.';
                } else {
                    if ($this->facultyService->createDesignation($data)) {
                        $success = 'Designation created successfully.';
                    } else {
                        $error = 'Failed to create designation.';
                    }
                }
            }
        }

        $designations = $this->facultyService->getDesignations(1);

        $this->render('Faculty/views/designations', [
            'title'        => 'Faculty Designations',
            'designations' => $designations,
            'error'        => $error,
            'success'      => $success,
        ], 'layout');
    }
}

<?php

declare(strict_types=1);

namespace App\Modules\Fee\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Fee\services\FeeService;
use App\Modules\Master\services\MasterService;

class FeeController extends Controller
{
    private FeeService $feeService;
    private MasterService $masterService;

    public function __construct()
    {
        $this->feeService    = new FeeService();
        $this->masterService = new MasterService();
    }

    /**
     * Fee Categories Management.
     */
    public function categories(): void
    {
        Permission::enforce('fee.category');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'college_id'    => 1,
                    'name'          => $this->input('name'),
                    'code'          => $this->input('code'),
                    'is_refundable' => (int) $this->input('is_refundable', '0'),
                    'status'        => (int) $this->input('status', '1'),
                ];

                if (empty($data['name'])) {
                    $error = 'Category name is required.';
                } else {
                    if ($this->feeService->createFeeCategory($data)) {
                        $success = 'Fee category created successfully.';
                    } else {
                        $error = 'Failed to create fee category.';
                    }
                }
            }
        }

        $categories = $this->feeService->getFeeCategories(1);

        $this->render('Fee/views/categories', [
            'title'      => 'Fee Categories',
            'categories' => $categories,
            'error'      => $error,
            'success'    => $success,
        ], 'layout');
    }

    /**
     * Fee Structures Setup.
     */
    public function structures(): void
    {
        Permission::enforce('fee.structure');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $data = [
                    'college_id'       => 1,
                    'academic_year_id' => (int) $this->input('academic_year_id'),
                    'course_id'        => (int) $this->input('course_id'),
                    'semester_id'      => (int) $this->input('semester_id'),
                    'fee_category_id'  => (int) $this->input('fee_category_id'),
                    'amount'           => (float) $this->input('amount'),
                    'due_date'         => $this->input('due_date'),
                ];

                if (empty($data['academic_year_id']) || empty($data['course_id']) || empty($data['semester_id']) || empty($data['fee_category_id']) || empty($data['amount'])) {
                    $error = 'All fields are required to configure a fee structure.';
                } else {
                    if ($this->feeService->createFeeStructure($data)) {
                        $success = 'Fee structure configured successfully.';
                    } else {
                        $error = 'Failed to configure fee structure.';
                    }
                }
            }
        }

        $structures    = $this->feeService->getFeeStructures(1);
        $categories    = $this->feeService->getFeeCategories(1);
        $academicYears = $this->masterService->getAcademicYears(1);
        $courses       = $this->masterService->getCourses(1);

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

        $this->render('Fee/views/structures', [
            'title'         => 'Fee Structures Configuration',
            'structures'    => $structures,
            'categories'    => $categories,
            'academicYears' => $academicYears,
            'courses'       => $courses,
            'semesters'     => $allSemesters,
            'error'         => $error,
            'success'       => $success,
        ], 'layout');
    }

    /**
     * Student Fee Assignment.
     */
    public function assign(): void
    {
        Permission::enforce('fee.assign');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $sectionId      = (int) $this->input('section_id');
                $feeStructureId = (int) $this->input('fee_structure_id');

                if (empty($sectionId) || empty($feeStructureId)) {
                    $error = 'Section and Fee Structure are required.';
                } else {
                    $count = $this->feeService->assignFeeToSection($sectionId, $feeStructureId);
                    if ($count > 0) {
                        $success = "Fee structure successfully assigned to {$count} students in section.";
                    } else {
                        $error = 'No active students found in section or assignment failed.';
                    }
                }
            }
        }

        $sections   = $this->masterService->getSections();
        $structures = $this->feeService->getFeeStructures(1);

        $this->render('Fee/views/assign', [
            'title'      => 'Assign Student Fees',
            'sections'   => $sections,
            'structures' => $structures,
            'error'      => $error,
            'success'    => $success,
        ], 'layout');
    }

    /**
     * Payments Processing.
     */
    /**
     * Payments Processing / Student Fee Receipts.
     */
    public function payments(): void
    {
        if (auth_role() === 'student') {
            $this->studentFees();
            return;
        }

        Permission::enforce('fee.payment');

        $error   = null;
        $success = null;

        if ($this->isPost()) {
            if (!csrf_verify($this->input('_csrf_token'))) {
                $error = 'Invalid security token.';
            } else {
                $studentFeeId  = (int) $this->input('student_fee_id');
                $amountPaid    = (float) $this->input('amount_paid');
                $paymentMethod = $this->input('payment_method', 'cash');
                $transactionId = $this->input('transaction_id') ?: null;
                $remarks       = $this->input('remarks') ?: null;

                if (empty($studentFeeId) || $amountPaid <= 0) {
                    $error = 'Valid Student Fee selection and amount paid are required.';
                } else {
                    $res = $this->feeService->recordPayment($studentFeeId, $amountPaid, $paymentMethod, $transactionId, $remarks);
                    if ($res['success']) {
                        flash('success', $res['message']);
                        $this->redirect('/fee/receipt/' . $res['receipt_id']);
                    } else {
                        $error = $res['message'];
                    }
                }
            }
        }

        $filters = [
            'status' => query('status', ''),
            'search' => query('search', ''),
        ];

        $studentFees = $this->feeService->getStudentFees(1, $filters);

        $this->render('Fee/views/payments', [
            'title'       => 'Payment Collection & Fee Ledger',
            'studentFees' => $studentFees,
            'filters'     => $filters,
            'error'       => $error,
            'success'     => $success,
        ], 'layout');
    }

    /**
     * View Fee Statements & Download Receipts for Student.
     */
    public function studentFees(): void
    {
        $attendanceService = new \App\Modules\Attendance\services\AttendanceService();
        $userId = auth_id();
        $studentId = $userId ? $attendanceService->getStudentIdFromUser($userId) : null;

        $res = $studentId ? $this->feeService->getFeesForStudent($studentId) : ['fees' => [], 'receipts' => []];

        $this->render('Fee/views/student_fees', [
            'title'    => 'My Fee Receipts & Dues',
            'fees'     => $res['fees'],
            'receipts' => $res['receipts'],
        ], 'layout');
    }

    /**
     * Print Official Fee Receipt.
     */
    public function receipt(string $id): void
    {
        Permission::enforce('fee.receipt');

        $receiptId = (int) $id;
        $receipt   = $this->feeService->getReceiptDetails($receiptId);

        if (!$receipt) {
            http_response_code(404);
            $this->render('Master/views/404', [], null);
            return;
        }

        $this->render('Fee/views/receipt', [
            'title'   => "Official Receipt: {$receipt['receipt_number']}",
            'receipt' => $receipt,
        ], null);
    }
}

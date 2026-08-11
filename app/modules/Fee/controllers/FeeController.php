<?php

declare(strict_types=1);

namespace App\Modules\Fee\controllers;

use App\Core\Controller;
use App\Core\Permission;
use App\Modules\Fee\services\FeeService;
use App\Modules\Fee\services\PaymentGatewayService;
use App\Modules\Master\services\MasterService;

class FeeController extends Controller
{
    private FeeService $feeService;
    private MasterService $masterService;
    private PaymentGatewayService $gatewayService;

    public function __construct()
    {
        $this->feeService     = new FeeService();
        $this->masterService  = new MasterService();
        $this->gatewayService = new PaymentGatewayService();
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
     * Payments Processing / Student Fee Receipts & Verification Ledger.
     */
    public function payments(): void
    {
        if (in_array(auth_role(), ['student', 'parent'], true)) {
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
                $action = $this->input('_action', 'manual_payment');

                if ($action === 'verify_utr') {
                    $txId = (int) $this->input('transaction_id');
                    $res = $this->gatewayService->captureAndPostPayment($txId, (int) auth_id());
                    if ($res['success']) {
                        $success = $res['message'];
                    } else {
                        $error = $res['message'];
                    }
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
        }

        $filters = [
            'status' => query('status', ''),
            'search' => query('search', ''),
        ];

        $studentFees          = $this->feeService->getStudentFees(1, $filters);
        $pendingVerifications = $this->gatewayService->getPendingVerifications();

        $this->render('Fee/views/payments', [
            'title'                => 'Payment Collection & Fee Ledger',
            'studentFees'          => $studentFees,
            'pendingVerifications' => $pendingVerifications,
            'filters'              => $filters,
            'error'                => $error,
            'success'              => $success,
        ], 'layout');
    }

    /**
     * Interactive Multi-QR & Online Checkout Screen for Student/Parent.
     */
    public function pay(string $id): void
    {
        $studentFeeId = (int) $id;
        $sfStmt = db()->prepare('
            SELECT sf.*, fc.name AS category_name, c.code AS course_code, sem.number AS semester_number,
                   fs.due_date, ay.name AS academic_year_name, s.roll_number, s.first_name, s.last_name, s.email,
                   COALESCE((SELECT SUM(amount_paid) FROM payments WHERE student_fee_id = sf.id), 0.00) AS total_paid
            FROM student_fees sf
            JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            JOIN courses c ON c.id = fs.course_id
            JOIN semesters sem ON sem.id = fs.semester_id
            JOIN academic_years ay ON ay.id = sf.academic_year_id
            JOIN students s ON s.id = sf.student_id
            WHERE sf.id = :id LIMIT 1
        ');
        $sfStmt->execute([':id' => $studentFeeId]);
        $fee = $sfStmt->fetch();

        if (!$fee) {
            http_response_code(404);
            $this->render('Master/views/404', [], null);
            return;
        }

        $dueBalance = max(0.00, (float)$fee['final_amount'] - (float)$fee['total_paid']);

        // Determine fee category type
        $catName = strtolower($fee['category_name']);
        $feeType = 'academic';
        if (str_contains($catName, 'hostel') || str_contains($catName, 'mess')) {
            $feeType = 'hostel';
        } elseif (str_contains($catName, 'bus') || str_contains($catName, 'transport')) {
            $feeType = 'transport';
        }

        $upiDetails = $this->gatewayService->getUpiDetailsForFeeType($feeType);
        $upiUri     = $this->gatewayService->generateUpiUri($feeType, $dueBalance, "FEE-{$studentFeeId}", $fee['roll_number']);

        $this->render('Fee/views/pay', [
            'title'       => 'Secure Online Fee Payment',
            'fee'         => $fee,
            'dueBalance'  => $dueBalance,
            'feeType'     => $feeType,
            'upiDetails'  => $upiDetails,
            'upiUri'      => $upiUri,
        ], 'layout');
    }

    /**
     * Submit Bank UTR reference after QR Scan.
     */
    public function submitUtr(): void
    {
        if (!$this->isPost() || !csrf_verify($this->input('_csrf_token'))) {
            flash('error', 'Invalid security token.');
            $this->redirect('/fee/payments');
        }

        $studentFeeId = (int) $this->input('student_fee_id');
        $studentId    = (int) $this->input('student_id');
        $feeType      = $this->input('fee_type', 'academic');
        $amount       = (float) $this->input('amount');
        $utrNumber    = $this->input('utr_number');

        $txRes = $this->gatewayService->createTransaction($studentId, $studentFeeId, $feeType, $amount, 'upi_qr', 'upi');
        if (!$txRes['success']) {
            flash('error', $txRes['message']);
            $this->redirect('/fee/pay/' . $studentFeeId);
        }

        $subRes = $this->gatewayService->submitUtrReference($txRes['transaction_id'], $utrNumber);
        if ($subRes['success']) {
            flash('success', $subRes['message']);
        } else {
            flash('error', $subRes['message']);
        }

        $this->redirect('/fee/payments');
    }

    /**
     * Instant Netbanking / Gateway Payment Simulator.
     */
    public function instantPay(): void
    {
        if (!$this->isPost() || !csrf_verify($this->input('_csrf_token'))) {
            flash('error', 'Invalid security token.');
            $this->redirect('/fee/payments');
        }

        $studentFeeId = (int) $this->input('student_fee_id');
        $studentId    = (int) $this->input('student_id');
        $feeType      = $this->input('fee_type', 'academic');
        $amount       = (float) $this->input('amount');
        $method       = $this->input('payment_method', 'netbanking');

        $txRes = $this->gatewayService->createTransaction($studentId, $studentFeeId, $feeType, $amount, 'gateway', $method);
        if (!$txRes['success']) {
            flash('error', $txRes['message']);
            $this->redirect('/fee/pay/' . $studentFeeId);
        }

        $capRes = $this->gatewayService->captureAndPostPayment($txRes['transaction_id'], (int) auth_id());
        if ($capRes['success']) {
            flash('success', $capRes['message']);
        } else {
            flash('error', $capRes['message']);
        }

        $this->redirect('/fee/payments');
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

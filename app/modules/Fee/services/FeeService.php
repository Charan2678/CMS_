<?php

declare(strict_types=1);

namespace App\Modules\Fee\services;

use Exception;
use PDO;

class FeeService
{
    // ─── 1. Fee Categories ─────────────────────────────────────
    public function getFeeCategories(int $collegeId = 1): array
    {
        $stmt = db()->prepare('SELECT * FROM fee_categories WHERE college_id = :college_id ORDER BY name ASC');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createFeeCategory(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO fee_categories (college_id, name, code, is_refundable, status)
            VALUES (:college_id, :name, :code, :is_refundable, :status)
        ');
        return $stmt->execute([
            ':college_id'    => $data['college_id'] ?? 1,
            ':name'          => $data['name'],
            ':code'          => strtolower(str_replace(' ', '_', $data['code'] ?? $data['name'])),
            ':is_refundable' => !empty($data['is_refundable']) ? 1 : 0,
            ':status'        => $data['status'] ?? 1,
        ]);
    }

    // ─── 2. Fee Structures ─────────────────────────────────────
    public function getFeeStructures(int $collegeId = 1): array
    {
        $stmt = db()->prepare('
            SELECT fs.*, fc.name AS category_name, c.name AS course_name, c.code AS course_code,
                   sem.number AS semester_number, ay.name AS academic_year_name
            FROM fee_structures fs
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            JOIN courses c ON c.id = fs.course_id
            JOIN semesters sem ON sem.id = fs.semester_id
            JOIN academic_years ay ON ay.id = fs.academic_year_id
            WHERE fs.college_id = :college_id
            ORDER BY ay.id DESC, c.code ASC, sem.number ASC
        ');
        $stmt->execute([':college_id' => $collegeId]);
        return $stmt->fetchAll() ?: [];
    }

    public function createFeeStructure(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO fee_structures (
                college_id, academic_year_id, course_id, semester_id, fee_category_id, amount, due_date, status, created_at
            ) VALUES (
                :college_id, :academic_year_id, :course_id, :semester_id, :fee_category_id, :amount, :due_date, 1, NOW()
            )
            ON DUPLICATE KEY UPDATE amount = VALUES(amount), due_date = VALUES(due_date)
        ');

        return $stmt->execute([
            ':college_id'       => $data['college_id'] ?? 1,
            ':academic_year_id' => (int) $data['academic_year_id'],
            ':course_id'        => (int) $data['course_id'],
            ':semester_id'      => (int) $data['semester_id'],
            ':fee_category_id'  => (int) $data['fee_category_id'],
            ':amount'           => (float) $data['amount'],
            ':due_date'         => $data['due_date'],
        ]);
    }

    // ─── 3. Student Fee Assignments ────────────────────────────
    public function assignFeeToSection(int $sectionId, int $feeStructureId): int
    {
        // Fetch fee structure details
        $fsStmt = db()->prepare('SELECT * FROM fee_structures WHERE id = :id LIMIT 1');
        $fsStmt->execute([':id' => $feeStructureId]);
        $fs = $fsStmt->fetch();

        if (!$fs) {
            return 0;
        }

        // Fetch students in section
        $sStmt = db()->prepare('
            SELECT student_id FROM student_academics
            WHERE section_id = :sec_id AND is_current = 1
        ');
        $sStmt->execute([':sec_id' => $sectionId]);
        $students = $sStmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

        $assignedCount = 0;
        $insStmt = db()->prepare('
            INSERT INTO student_fees (
                student_id, fee_structure_id, academic_year_id, amount_due, discount, final_amount, status, created_at
            ) VALUES (
                :student_id, :fee_structure_id, :academic_year_id, :amount_due, 0.00, :final_amount, "pending", NOW()
            )
            ON DUPLICATE KEY UPDATE amount_due = VALUES(amount_due), final_amount = amount_due - discount
        ');

        foreach ($students as $sid) {
            $insStmt->execute([
                ':student_id'       => (int) $sid,
                ':fee_structure_id' => $feeStructureId,
                ':academic_year_id' => $fs['academic_year_id'],
                ':amount_due'       => $fs['amount'],
                ':final_amount'     => $fs['amount'],
            ]);
            $assignedCount++;
        }

        return $assignedCount;
    }

    public function getStudentFees(int $collegeId = 1, array $filters = []): array
    {
        $sql = '
            SELECT sf.*, s.roll_number, s.first_name, s.last_name,
                   fc.name AS category_name, c.code AS course_code, sem.number AS semester_number,
                   COALESCE((SELECT SUM(amount_paid) FROM payments WHERE student_fee_id = sf.id), 0.00) AS total_paid
            FROM student_fees sf
            JOIN students s ON s.id = sf.student_id
            JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            JOIN courses c ON c.id = fs.course_id
            JOIN semesters sem ON sem.id = fs.semester_id
            WHERE s.college_id = :college_id
        ';

        $params = [':college_id' => $collegeId];

        if (!empty($filters['status'])) {
            $sql .= ' AND sf.status = :status';
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (s.roll_number LIKE :q OR s.first_name LIKE :q OR s.last_name LIKE :q)';
            $params[':q'] = '%' . $filters['search'] . '%';
        }

        $sql .= ' ORDER BY sf.id DESC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }

    // ─── 4. Payment Collection & Official Receipts ─────────────
    public function recordPayment(int $studentFeeId, float $amountPaid, string $paymentMethod, ?string $transactionId = null, ?string $remarks = null): array
    {
        db()->beginTransaction();

        try {
            // 1. Fetch Student Fee
            $sfStmt = db()->prepare('SELECT * FROM student_fees WHERE id = :id LIMIT 1 FOR UPDATE');
            $sfStmt->execute([':id' => $studentFeeId]);
            $sf = $sfStmt->fetch();

            if (!$sf) {
                db()->rollBack();
                return ['success' => false, 'message' => 'Student fee assignment record not found.'];
            }

            // 2. Insert Payment Record
            $pStmt = db()->prepare('
                INSERT INTO payments (
                    student_fee_id, student_id, amount_paid, payment_method, transaction_id, payment_date, received_by, remarks, created_at
                ) VALUES (
                    :sf_id, :student_id, :amount_paid, :method, :tx_id, CURDATE(), :received_by, :remarks, NOW()
                )
            ');

            $receivedBy = auth_id() ?? 1;
            $pStmt->execute([
                ':sf_id'       => $studentFeeId,
                ':student_id'  => $sf['student_id'],
                ':amount_paid' => $amountPaid,
                ':method'      => $paymentMethod,
                ':tx_id'       => $transactionId,
                ':received_by' => $receivedBy,
                ':remarks'     => $remarks,
            ]);

            $paymentId = (int) db()->lastInsertId();

            // 3. Compute Total Paid for this Student Fee
            $totStmt = db()->prepare('SELECT SUM(amount_paid) FROM payments WHERE student_fee_id = :sf_id');
            $totStmt->execute([':sf_id' => $studentFeeId]);
            $totalPaid = (float) $totStmt->fetchColumn();

            $finalAmount = (float) $sf['final_amount'];
            $newStatus   = 'pending';

            if ($totalPaid >= $finalAmount) {
                $newStatus = 'paid';
            } elseif ($totalPaid > 0) {
                $newStatus = 'partial';
            }

            // Update Student Fee status
            $uSf = db()->prepare('UPDATE student_fees SET status = :status, updated_at = NOW() WHERE id = :id');
            $uSf->execute([':status' => $newStatus, ':id' => $studentFeeId]);

            // 4. Generate Official Receipt
            $receiptNumber = 'RCP-' . date('Y') . '-' . str_pad((string)$paymentId, 6, '0', STR_PAD_LEFT);

            $rStmt = db()->prepare('
                INSERT INTO receipts (
                    payment_id, receipt_number, generated_at, generated_by
                ) VALUES (
                    :payment_id, :receipt_number, NOW(), :generated_by
                )
            ');

            $rStmt->execute([
                ':payment_id'     => $paymentId,
                ':receipt_number' => $receiptNumber,
                ':generated_by'   => $receivedBy,
            ]);

            $receiptId = (int) db()->lastInsertId();

            db()->commit();

            // Auto-activate or update transport route and bus pass if transport fee or modification fee was paid
            $catCodeStmt = db()->prepare('
                SELECT fc.code, fc.name FROM student_fees sf 
                JOIN fee_structures fs ON fs.id = sf.fee_structure_id 
                JOIN fee_categories fc ON fc.id = fs.fee_category_id 
                WHERE sf.id = :sfid LIMIT 1
            ');
            $catCodeStmt->execute([':sfid' => $studentFeeId]);
            $catInfo = $catCodeStmt->fetch();
            $catCode = strtolower((string)($catInfo['code'] ?? ''));
            $catName = strtolower((string)($catInfo['name'] ?? ''));

            if (str_contains($catCode, 'transport') || str_contains($catName, 'transport') || str_contains($catName, 'bus') || str_contains($catName, 'route')) {
                try {
                    (new \App\Modules\Transport\services\TransportService())->getOrCreateBusPass((int)$sf['student_id']);
                } catch (\Throwable $e) {
                    // Non-blocking bus pass sync
                }
            }

            return [
                'success'        => true,
                'message'        => 'Payment recorded successfully! Receipt Generated: ' . $receiptNumber,
                'receipt_id'     => $receiptId,
                'receipt_number' => $receiptNumber
            ];

        } catch (Exception $e) {
            db()->rollBack();
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ];
        }
    }

    public function getReceiptDetails(int $receiptId): ?array
    {
        $stmt = db()->prepare('
            SELECT r.*,
                   p.amount_paid, p.payment_method, p.transaction_id, p.payment_date, p.remarks,
                   p.student_id, p.student_fee_id, p.received_by,
                   COALESCE(p.mode, "counter") AS payment_mode,
                   COALESCE(p.utr_reference, p.transaction_id) AS utr_reference,
                   COALESCE(sf.amount_due, p.amount_paid) AS amount_due,
                   COALESCE(sf.discount, 0.00) AS discount,
                   COALESCE(sf.final_amount, p.amount_paid) AS final_amount,
                   COALESCE(sf.status, "paid") AS fee_status,
                   COALESCE(fc.name, "Academic Fee") AS fee_category_name,
                   COALESCE(fc.code, "FEE") AS fee_category_code,
                   COALESCE(ay.name, "Academic Year 2025-2026") AS academic_year_name,
                   s.roll_number, s.first_name AS student_first_name, s.last_name AS student_last_name,
                   s.email AS student_email, s.mobile AS student_mobile,
                   c.name AS course_name, c.code AS course_code,
                   d.name AS department_name, d.code AS department_code,
                   sem.number AS semester_number,
                   col.name AS college_name, col.code AS college_code, col.address AS college_address,
                   col.city AS college_city, col.state AS college_state, col.pincode AS college_pincode,
                   col.phone AS college_phone, col.email AS college_email, col.website AS college_website,
                   col.logo_path AS college_logo, col.affiliation_body, col.affiliation_number,
                   u.username AS cashier_username
            FROM receipts r
            JOIN payments p ON p.id = r.payment_id
            LEFT JOIN student_fees sf ON sf.id = p.student_fee_id
            LEFT JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            LEFT JOIN fee_categories fc ON fc.id = fs.fee_category_id
            LEFT JOIN academic_years ay ON ay.id = sf.academic_year_id
            LEFT JOIN students s ON s.id = p.student_id
            LEFT JOIN colleges col ON col.id = COALESCE(s.college_id, 1)
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            LEFT JOIN courses c ON c.id = COALESCE(sa.course_id, fs.course_id)
            LEFT JOIN departments d ON d.id = c.department_id
            LEFT JOIN semesters sem ON sem.id = COALESCE(sa.semester_id, fs.semester_id)
            LEFT JOIN users u ON u.id = p.received_by
            WHERE r.id = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $receiptId]);
        $receipt = $stmt->fetch();
        if (!$receipt) {
            return null;
        }

        // Calculate total previously paid for this student fee to compute remaining balance
        $paidTotalStmt = db()->prepare('
            SELECT COALESCE(SUM(amount_paid), 0.00) FROM payments WHERE student_fee_id = :sf_id
        ');
        $paidTotalStmt->execute([':sf_id' => $receipt['student_fee_id'] ?? 0]);
        $totalPaidForFee = (float) $paidTotalStmt->fetchColumn();
        $balanceRemaining = max(0.00, (float)$receipt['final_amount'] - $totalPaidForFee);

        $receipt['total_paid_overall'] = $totalPaidForFee;
        $receipt['balance_remaining']  = $balanceRemaining;
        $receipt['amount_in_words']    = number_to_words_inr((float)$receipt['amount_paid']);

        return $receipt;
    }

    /**
     * Get fee structures, payments, and receipts for a specific student.
     */
    public function getFeesForStudent(int $studentId): array
    {
        // 1. Fetch assigned fee structures
        $stmt = db()->prepare('
            SELECT sf.*, fc.name AS category_name, c.code AS course_code, sem.number AS semester_number,
                   fs.due_date, ay.name AS academic_year_name,
                   COALESCE((SELECT SUM(amount_paid) FROM payments WHERE student_fee_id = sf.id), 0.00) AS total_paid
            FROM student_fees sf
            JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            JOIN courses c ON c.id = fs.course_id
            JOIN semesters sem ON sem.id = fs.semester_id
            JOIN academic_years ay ON ay.id = sf.academic_year_id
            WHERE sf.student_id = :student_id
            ORDER BY sf.id DESC
        ');
        $stmt->execute([':student_id' => $studentId]);
        $fees = $stmt->fetchAll() ?: [];

        // 2. Fetch payment receipts list
        $rcpStmt = db()->prepare('
            SELECT r.id AS receipt_id, r.receipt_number, r.generated_at,
                   p.amount_paid, p.payment_method, p.payment_date, p.transaction_id,
                   fc.name AS category_name
            FROM receipts r
            JOIN payments p ON p.id = r.payment_id
            JOIN student_fees sf ON sf.id = p.student_fee_id
            JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            WHERE p.student_id = :student_id
            ORDER BY r.id DESC
        ');
        $rcpStmt->execute([':student_id' => $studentId]);
        $receipts = $rcpStmt->fetchAll() ?: [];

        return [
            'fees'     => $fees,
            'receipts' => $receipts
        ];
    }
}

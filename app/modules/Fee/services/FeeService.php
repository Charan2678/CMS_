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
            SELECT r.*, p.amount_paid, p.payment_method, p.transaction_id, p.payment_date, p.remarks,
                   sf.amount_due, sf.discount, sf.final_amount, sf.status AS fee_status,
                   fc.name AS fee_category_name,
                   s.roll_number, s.first_name AS student_first_name, s.last_name AS student_last_name, s.email AS student_email,
                   c.name AS course_name, c.code AS course_code, sem.number AS semester_number,
                   col.name AS college_name, col.address AS college_address, col.phone AS college_phone, col.email AS college_email
            FROM receipts r
            JOIN payments p ON p.id = r.payment_id
            JOIN student_fees sf ON sf.id = p.student_fee_id
            JOIN fee_structures fs ON fs.id = sf.fee_structure_id
            JOIN fee_categories fc ON fc.id = fs.fee_category_id
            JOIN students s ON s.id = p.student_id
            JOIN colleges col ON col.id = s.college_id
            LEFT JOIN student_academics sa ON sa.student_id = s.id AND sa.is_current = 1
            LEFT JOIN courses c ON c.id = sa.course_id
            LEFT JOIN semesters sem ON sem.id = sa.semester_id
            WHERE r.id = :id
            LIMIT 1
        ');
        $stmt->execute([':id' => $receiptId]);
        return $stmt->fetch() ?: null;
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

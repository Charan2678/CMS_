<?php

declare(strict_types=1);

namespace App\Modules\Fee\services;

use App\Modules\Settings\services\NotificationService;
use Exception;
use PDO;

class PaymentGatewayService
{
    private NotificationService $notifSvc;

    public function __construct()
    {
        $this->notifSvc = new NotificationService();
    }

    /**
     * Get UPI VPA and payee details based on fee category.
     * College & Bus share one official QR; Hostel will have a separate QR.
     */
    public function getUpiDetailsForFeeType(string $feeType): array
    {
        $type = strtolower($feeType);

        if (in_array($type, ['hostel', 'mess', 'hostel_room'], true)) {
            return [
                'category'      => 'hostel',
                'title'         => 'Hostel & Mess Fees Account',
                'payee_name'    => env('HOSTEL_PAYEE_NAME', 'KUPPAM EDUCATIONAL SOCIETY HOSTEL'),
                'vpa'           => env('HOSTEL_UPI_VPA', '106508632000311@cnrb'),
                'account_num'   => '106508632000311',
                'ifsc'          => 'CNRB0001065',
                'bank_name'     => 'Canara Bank (Official Hostel & Mess Account)',
                'badge_color'   => '#8b5cf6',
                'qr_image'      => '/assets/images/hostel_qr.png',
                'merchant_code' => env('HOSTEL_MERCHANT_CODE', '8299'),
            ];
        }

        // Official College Academic & Bus Transport combined QR
        return [
            'category'      => 'college_and_bus',
            'title'         => 'College Academic & Bus Transport Account',
            'payee_name'    => env('COLLEGE_BUS_PAYEE_NAME', 'KUPPAM ENGINEERING COLLEGE'),
            'vpa'           => env('COLLEGE_BUS_UPI_VPA', '106508598000310@cnrb'),
            'account_num'   => '106508598000310',
            'ifsc'          => 'CNRB0001065',
            'bank_name'     => 'Canara Bank (Official College & Bus Account)',
            'badge_color'   => '#0284c7',
            'qr_image'      => '/assets/images/college_bus_qr.png',
            'merchant_code' => env('COLLEGE_BUS_MERCHANT_CODE', '8299'),
        ];
    }

    /**
     * Generate dynamic UPI Deep Link URI.
     */
    public function generateUpiUri(string $feeType, float $amount, string $refNumber, string $studentRoll): string
    {
        $details = $this->getUpiDetailsForFeeType($feeType);
        $vpa     = rawurlencode($details['vpa']);
        $pn      = rawurlencode($details['payee_name']);
        $am      = number_format($amount, 2, '.', '');
        $tn      = rawurlencode("Fee {$refNumber} {$studentRoll}");
        $mc      = !empty($details['merchant_code']) ? "&mc=" . rawurlencode($details['merchant_code']) : "";

        return "upi://pay?pa={$vpa}&pn={$pn}{$mc}&am={$am}&tn={$tn}&cu=INR";
    }

    /**
     * Initialize a payment gateway or QR transaction record.
     */
    public function createTransaction(
        int $studentId,
        ?int $studentFeeId,
        string $feeType,
        float $amount,
        string $gateway = 'upi_qr'
    ): array {
        $txRef = 'TXN-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $userId = $this->getStudentUserId($studentId) ?? auth_id() ?? 1;

        $validFeeType = in_array($feeType, ['academic','hostel','transport','canteen','other'], true) ? $feeType : 'academic';
        $validGateway = in_array($gateway, ['razorpay','upi_qr','netbanking','card','cash'], true) ? $gateway : 'upi_qr';

        try {
            $stmt = db()->prepare('
                INSERT INTO payment_gateway_transactions (
                    college_id, student_fee_id, user_id, fee_type, gateway, gateway_order_id,
                    amount, currency, status, created_at
                ) VALUES (
                    1, :student_fee_id, :user_id, :fee_type, :gateway, :gateway_order_id,
                    :amount, "INR", "created", NOW()
                )
            ');

            $stmt->execute([
                ':student_fee_id'  => $studentFeeId,
                ':user_id'         => $userId,
                ':fee_type'        => $validFeeType,
                ':gateway'         => $validGateway,
                ':gateway_order_id'=> $txRef,
                ':amount'          => $amount,
            ]);

            $txId = (int) db()->lastInsertId();

            return [
                'success'               => true,
                'transaction_id'        => $txId,
                'transaction_reference' => $txRef,
                'amount'                => $amount,
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to initialize payment transaction: ' . $e->getMessage()];
        }
    }

    /**
     * Submit 12-digit UTR Reference Number after student scans UPI QR.
     */
    public function submitUtrReference(int $transactionId, string $utrNumber): array
    {
        $cleanUtr = trim($utrNumber);
        if (strlen($cleanUtr) < 6) {
            return ['success' => false, 'message' => 'Please enter a valid Bank UTR / UPI Transaction Reference Number.'];
        }

        try {
            $stmt = db()->prepare('
                UPDATE payment_gateway_transactions
                SET utr_reference = :utr, status = "pending_verification", updated_at = NOW()
                WHERE id = :id
            ');
            $ok = $stmt->execute([
                ':utr' => $cleanUtr,
                ':id'  => $transactionId,
            ]);

            return [
                'success' => $ok,
                'message' => 'UTR reference (' . $cleanUtr . ') submitted successfully! Accounts counter staff will verify and post official receipt.',
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to record UTR: ' . $e->getMessage()];
        }
    }

    /**
     * Capture / Confirm payment and record into core payments table.
     */
    public function captureAndPostPayment(int $transactionId, ?int $verifiedByUserId = null): array
    {
        db()->beginTransaction();

        try {
            $stmt = db()->prepare('
                SELECT pgt.*, sf.student_id, s.roll_number, s.first_name, s.last_name, sf.fee_structure_id
                FROM payment_gateway_transactions pgt
                LEFT JOIN student_fees sf ON sf.id = pgt.student_fee_id
                LEFT JOIN students s ON s.id = sf.student_id
                WHERE pgt.id = :id FOR UPDATE
            ');
            $stmt->execute([':id' => $transactionId]);
            $tx = $stmt->fetch();

            if (!$tx) {
                db()->rollBack();
                return ['success' => false, 'message' => 'Transaction not found.'];
            }

            if ($tx['status'] === 'captured') {
                db()->rollBack();
                return ['success' => false, 'message' => 'Transaction has already been captured and paid.'];
            }

            // Fallback student details if student_fee was direct
            $studentId = (int) ($tx['student_id'] ?: 1);
            $sfId      = !empty($tx['student_fee_id']) ? (int)$tx['student_fee_id'] : (int) db()->query("SELECT id FROM student_fees WHERE student_id = {$studentId} LIMIT 1")->fetchColumn();
            if (!$sfId) {
                $sfId = (int) db()->query("SELECT id FROM student_fees LIMIT 1")->fetchColumn() ?: 1;
            }

            // 1. Update Transaction status
            $upTx = db()->prepare('
                UPDATE payment_gateway_transactions
                SET status = "captured", gateway_payment_id = COALESCE(gateway_payment_id, :gw_id), updated_at = NOW()
                WHERE id = :id
            ');
            $gwId = $tx['utr_reference'] ?: ('CAP-' . time());
            $upTx->execute([':gw_id' => $gwId, ':id' => $transactionId]);

            // 2. Insert into Core Payments Table
            $receiptNo = 'REC-' . date('Ymd') . '-' . rand(1000, 9999);
            $payMode   = $tx['gateway'] === 'upi_qr' ? 'upi_qr' : ($tx['gateway'] === 'razorpay' ? 'gateway' : 'manual');
            $enumMethod = match($tx['gateway']) {
                'upi_qr'     => 'upi',
                'razorpay'   => 'online_gateway',
                'card'       => 'card',
                'netbanking' => 'online',
                default      => 'cash',
            };

            $inPay = db()->prepare('
                INSERT INTO payments (
                    student_fee_id, student_id, amount_paid, payment_method, mode, utr_reference, fee_category_type,
                    payment_date, received_by, remarks, created_at
                ) VALUES (
                    :sf_id, :student_id, :amount, :pay_method, :mode, :utr, :fee_category,
                    CURDATE(), :received_by, :notes, NOW()
                )
            ');

            $inPay->execute([
                ':sf_id'         => $sfId,
                ':student_id'    => $studentId,
                ':amount'        => $tx['amount'],
                ':pay_method'    => $enumMethod,
                ':mode'          => $payMode,
                ':utr'           => $tx['utr_reference'],
                ':fee_category'  => $tx['fee_type'],
                ':received_by'   => $verifiedByUserId ?? 1,
                ':notes'         => "Online Settlement Ref: {$tx['gateway_order_id']}" . ($tx['utr_reference'] ? " | UTR: {$tx['utr_reference']}" : ""),
            ]);

            $paymentId = (int) db()->lastInsertId();

            // 3. Insert into receipts table
            $inRec = db()->prepare('
                INSERT INTO receipts (payment_id, receipt_number, generated_at, generated_by)
                VALUES (:payment_id, :receipt_number, NOW(), :generated_by)
            ');
            $inRec->execute([
                ':payment_id'     => $paymentId,
                ':receipt_number' => $receiptNo,
                ':generated_by'   => $verifiedByUserId ?? 1,
            ]);

            // 4. Update student_fees table if applicable
            if (!empty($tx['student_fee_id'])) {
                $sfId = (int) $tx['student_fee_id'];
                $totStmt = db()->prepare('SELECT SUM(amount_paid) FROM payments WHERE student_fee_id = :sf_id');
                $totStmt->execute([':sf_id' => $sfId]);
                $totPaid = (float) $totStmt->fetchColumn();

                $sfStmt = db()->prepare('SELECT final_amount FROM student_fees WHERE id = :id');
                $sfStmt->execute([':id' => $sfId]);
                $finalAmt = (float) $sfStmt->fetchColumn();

                $newStatus = $totPaid >= $finalAmt ? 'paid' : ($totPaid > 0 ? 'partial' : 'pending');
                $uSf = db()->prepare('UPDATE student_fees SET status = :st, updated_at = NOW() WHERE id = :id');
                $uSf->execute([':st' => $newStatus, ':id' => $sfId]);
            }

            db()->commit();

            // Auto-activate or update Transport Bus Pass if fee type is transport or route change
            if (($tx['fee_type'] === 'transport' || $tx['fee_type'] === 'transport_change' || str_contains((string)$tx['fee_type'], 'transport')) && $studentId) {
                try {
                    (new \App\Modules\Transport\services\TransportService())->getOrCreateBusPass((int)$studentId);
                } catch (\Throwable $e) {
                    // Log or ignore non-blocking pass activation
                }
            }

            // 5. Dispatch Receipts & Notifications
            $studentUserId = $this->getStudentUserId((int)$studentId);
            $parentUserId  = $this->getParentUserId((int)$studentId);
            $formattedAmt  = number_format((float)$tx['amount'], 2);

            $notifTitle = "Fee Payment Receipt: ₹{$formattedAmt} Confirmed";
            $notifMsg   = "Your fee payment of ₹{$formattedAmt} (Receipt: {$receiptNo}) for {$tx['fee_type']} fee has been successfully processed.";

            if ($studentUserId) {
                $this->notifSvc->notify($studentUserId, $notifTitle, $notifMsg, '/fee/payments', 'success', 'high');
            }
            if ($parentUserId) {
                $this->notifSvc->notify($parentUserId, "[Ward] " . $notifTitle, "Payment of ₹{$formattedAmt} confirmed for {$tx['first_name']} ({$tx['roll_number']}).", '/fee/payments', 'success', 'high');
            }

            return [
                'success'        => true,
                'message'        => "Payment of ₹{$formattedAmt} verified and captured successfully! Official Receipt: {$receiptNo}",
                'receipt_number' => $receiptNo,
            ];
        } catch (Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Settlement failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get all transactions pending verification for Accounts counter staff.
     */
    public function getPendingVerifications(): array
    {
        $stmt = db()->prepare('
            SELECT pgt.*, pgt.gateway_order_id AS transaction_reference,
                   s.roll_number, s.first_name, s.last_name, s.mobile,
                   sf.final_amount AS total_fee_amount
            FROM payment_gateway_transactions pgt
            LEFT JOIN student_fees sf ON sf.id = pgt.student_fee_id
            LEFT JOIN students s ON s.id = sf.student_id
            WHERE pgt.status = "pending_verification"
            ORDER BY pgt.id DESC
        ');
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    private function getStudentUserId(int $studentId): ?int
    {
        $stmt = db()->prepare('SELECT id FROM users WHERE linked_type = "student" AND linked_id = :id LIMIT 1');
        $stmt->execute([':id' => $studentId]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    private function getParentUserId(int $studentId): ?int
    {
        $stmt = db()->prepare('
            SELECT u.id FROM users u
            JOIN guardians g ON g.id = u.linked_id AND u.linked_type = "parent"
            WHERE g.student_id = :sid LIMIT 1
        ');
        $stmt->execute([':sid' => $studentId]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }
}

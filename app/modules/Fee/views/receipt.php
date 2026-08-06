<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Official Fee Receipt') ?></title>
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            padding: 2rem;
        }
        .receipt-container {
            max-width: 680px;
            margin: 0 auto;
            background: white;
            padding: 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .header h1 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }
        .header p {
            font-size: 0.875rem;
            color: #64748b;
        }
        .receipt-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f5f9;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        .grid-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }
        .table-ledger {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }
        .table-ledger th, .table-ledger td {
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
        }
        .table-ledger th {
            background: #f8fafc;
            text-align: left;
        }
        .footer {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
            font-size: 0.8125rem;
            color: #64748b;
        }
        @media print {
            body { background: white; padding: 0; }
            .receipt-container { box-shadow: none; border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 1.5rem;">
        <button onclick="window.print()" style="background: #6366f1; color: white; border: none; padding: 0.625rem 1.5rem; border-radius: 0.375rem; font-weight: 600; cursor: pointer;">🖨️ Print Receipt</button>
        <a href="/fee/payments" style="margin-left: 1rem; color: #475569; text-decoration: none;">← Back to Payments</a>
    </div>

    <div class="receipt-container">
        <div class="header">
            <h1><?= e($receipt['college_name']) ?></h1>
            <p><?= e($receipt['college_address'] ?? 'Official Academic Campus') ?> | Contact: <?= e($receipt['college_phone'] ?? 'N/A') ?></p>
        </div>

        <div class="receipt-title">
            <span>OFFICIAL FEE RECEIPT</span>
            <span style="color: #6366f1;"><?= e($receipt['receipt_number']) ?></span>
        </div>

        <div class="grid-details">
            <div>
                <span style="color: #64748b; display: block;">Student Name:</span>
                <strong><?= e($receipt['student_first_name'] . ' ' . $receipt['student_last_name']) ?></strong>
            </div>
            <div>
                <span style="color: #64748b; display: block;">Roll / Student ID:</span>
                <strong><?= e($receipt['roll_number']) ?></strong>
            </div>
            <div>
                <span style="color: #64748b; display: block;">Course & Semester:</span>
                <strong><?= e($receipt['course_code'] ?? 'N/A') ?> (Sem <?= e($receipt['semester_number'] ?? '1') ?>)</strong>
            </div>
            <div>
                <span style="color: #64748b; display: block;">Payment Date:</span>
                <strong><?= e($receipt['payment_date']) ?></strong>
            </div>
        </div>

        <table class="table-ledger">
            <thead>
                <tr>
                    <th>Fee Particulars</th>
                    <th>Payment Method</th>
                    <th>Transaction Ref</th>
                    <th style="text-align: right;">Amount Paid (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= e($receipt['fee_category_name']) ?></td>
                    <td style="text-transform: uppercase;"><?= e($receipt['payment_method']) ?></td>
                    <td><?= e($receipt['transaction_id'] ?? 'N/A') ?></td>
                    <td style="text-align: right; font-weight: 700;">₹<?= number_format((float)$receipt['amount_paid'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div style="text-align: right; font-size: 0.875rem; margin-bottom: 1.5rem;">
            <div>Total Fee Amount: ₹<?= number_format((float)$receipt['final_amount'], 2) ?></div>
            <div>Discount / Concession: ₹<?= number_format((float)$receipt['discount'], 2) ?></div>
            <div style="font-size: 1.125rem; font-weight: 700; color: #16a34a; margin-top: 0.5rem;">
                Amount Collected: ₹<?= number_format((float)$receipt['amount_paid'], 2) ?>
            </div>
        </div>

        <div class="footer">
            <div>
                <div>Status: <strong style="text-transform: uppercase; color: #16a34a;"><?= e($receipt['fee_status']) ?></strong></div>
                <div>Generated: <?= e($receipt['generated_at']) ?></div>
            </div>
            <div style="text-align: right; margin-top: 1rem;">
                <div>________________________</div>
                <div>Authorized Cashier / Cash Counter</div>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Official Payment Receipt') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --primary-accent: #2563eb;
            --primary-light: #eff6ff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #059669;
            --success-light: #ecfdf5;
            --font-main: 'Inter', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background-color: #f1f5f9;
            color: var(--text-dark);
            line-height: 1.5;
            padding: 2rem 1rem;
            -webkit-font-smoothing: antialiased;
        }

        /* Action Toolbar */
        .toolbar {
            max-width: 840px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: var(--primary-accent);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-outline {
            background-color: #ffffff;
            color: var(--text-dark);
            border-color: var(--border);
        }
        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
        }

        /* Invoice Sheet Canvas */
        .invoice-card {
            max-width: 840px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        /* Subtle Security Watermark */
        .watermark-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 8rem;
            font-weight: 900;
            color: rgba(5, 150, 105, 0.04);
            letter-spacing: 0.25em;
            pointer-events: none;
            user-select: none;
            z-index: 0;
            white-space: nowrap;
        }

        .invoice-content {
            position: relative;
            z-index: 1;
        }

        /* Header Layout */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 1.75rem;
            margin-bottom: 2rem;
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .brand-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 8px;
        }

        .brand-info h1 {
            font-size: 1.35rem;
            font-weight: 900;
            color: var(--primary);
            letter-spacing: -0.02em;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }

        .brand-tagline {
            font-size: 0.75rem;
            color: var(--primary-accent);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        .brand-address {
            font-size: 0.75rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .invoice-meta-badge {
            text-align: right;
            min-width: 230px;
        }

        .invoice-title-pill {
            display: inline-block;
            background: var(--primary);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }

        .receipt-number {
            font-family: var(--font-mono);
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--primary-accent);
            margin-bottom: 0.25rem;
        }

        .receipt-date {
            font-size: 0.8125rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* 2-Column Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.25rem;
        }

        .info-box-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8125rem;
            margin-bottom: 0.4rem;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: var(--text-muted);
            font-weight: 500;
        }

        .info-value {
            color: var(--text-dark);
            font-weight: 700;
            text-align: right;
        }

        /* Status Banner */
        .status-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--success-light);
            border: 1px solid rgba(5, 150, 105, 0.3);
            border-radius: 8px;
            padding: 0.875rem 1.25rem;
            margin-bottom: 2rem;
        }

        .paid-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--success);
            color: #ffffff;
            font-size: 0.8125rem;
            font-weight: 900;
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            letter-spacing: 0.05em;
        }

        .status-msg {
            font-size: 0.8125rem;
            color: #065f46;
            font-weight: 600;
        }

        /* Itemized Table */
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8125rem;
            margin-bottom: 1.75rem;
        }

        .ledger-table th {
            background: var(--primary);
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            text-align: left;
        }

        .ledger-table th:last-child,
        .ledger-table td:last-child {
            text-align: right;
        }

        .ledger-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-dark);
        }

        .ledger-table tbody tr:nth-child(even) {
            background: #fbfcfe;
        }

        .item-main-title {
            font-weight: 800;
            color: var(--primary);
            font-size: 0.875rem;
        }

        .item-sub-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        /* Summary & Words Calculation Section */
        .calculation-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        .words-box {
            background: #f8fafc;
            border: 1px dashed var(--border);
            border-radius: 8px;
            padding: 1.25rem;
            font-size: 0.8125rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .words-title {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .words-text {
            font-weight: 700;
            color: var(--primary);
            font-style: italic;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .qr-verification-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            border-top: 1px solid var(--border);
            padding-top: 0.75rem;
        }

        .qr-thumb {
            width: 54px;
            height: 54px;
            border-radius: 4px;
            border: 1px solid var(--border);
            background: #ffffff;
            padding: 2px;
        }

        .qr-info-text {
            font-size: 0.7rem;
            color: var(--text-muted);
            line-height: 1.3;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.25rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.8125rem;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
        }

        .summary-row.total-row {
            border-top: 2px solid var(--border);
            padding-top: 0.75rem;
            margin-top: 0.75rem;
            font-size: 1.15rem;
            font-weight: 900;
            color: var(--success);
        }

        .summary-row.balance-row {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--primary-accent);
            margin-top: 0.35rem;
        }

        /* Signatures & Legal Disclaimer */
        .invoice-footer {
            border-top: 1px solid var(--border);
            padding-top: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: flex-end;
        }

        .legal-notice {
            font-size: 0.7rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .signature-block {
            text-align: right;
        }

        .seal-stamp {
            display: inline-block;
            border: 2px dashed rgba(37, 99, 235, 0.4);
            color: var(--primary-accent);
            padding: 0.5rem 1rem;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .sign-line {
            width: 200px;
            margin-left: auto;
            border-bottom: 1.5px solid var(--text-dark);
            margin-bottom: 0.35rem;
        }

        .sign-title {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
        }

        .sign-subtitle {
            font-size: 0.6875rem;
            color: var(--text-muted);
        }

        /* Print Optimization */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
                color: #000000;
            }
            .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
                border-radius: 0;
            }
            .watermark-bg {
                display: none;
            }
            @page {
                size: A4 portrait;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="toolbar no-print">
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button type="button" onclick="window.print()" class="btn btn-primary">
                <span>🖨️</span> Print Official Receipt
            </button>
            <button type="button" onclick="window.print()" class="btn btn-outline">
                <span>📥</span> Save as PDF
            </button>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="/fee/payments" class="btn btn-outline">
                <span>←</span> Back to Fee Payments
            </a>
            <a href="/dashboard" class="btn btn-outline">
                <span>🏠</span> Dashboard
            </a>
        </div>
    </div>

    <!-- Official Business Invoice / Receipt Sheet -->
    <div class="invoice-card">
        <div class="watermark-bg">PAID</div>

        <div class="invoice-content">
            <!-- Header -->
            <header class="invoice-header">
                <div class="brand-block">
                    <img src="<?= !empty($receipt['college_logo']) ? e($receipt['college_logo']) : '/assets/images/logo.png' ?>" alt="College Logo" class="brand-logo">
                    <div class="brand-info">
                        <h1><?= e($receipt['college_name'] ?? 'Kuppam Engineering College') ?></h1>
                        <div class="brand-tagline">
                            <?= e($receipt['affiliation_body'] ?? 'Affiliated to JNTUA, Ananthapuramu • Approved by AICTE, New Delhi') ?>
                        </div>
                        <div class="brand-address">
                            <?= e($receipt['college_address'] ?? 'KES Nagar, PB Road, Kuppam - 517425, Chittoor Dist., Andhra Pradesh') ?><br>
                            📞 <?= e($receipt['college_phone'] ?? '+91 8570 256262') ?> &bull; ✉️ <?= e($receipt['college_email'] ?? 'principal@kec.ac.in') ?> &bull; 🌐 <?= e($receipt['college_website'] ?? 'www.kec.ac.in') ?>
                        </div>
                    </div>
                </div>

                <div class="invoice-meta-badge">
                    <div class="invoice-title-pill">TAX INVOICE / RECEIPT</div>
                    <div class="receipt-number"><?= e($receipt['receipt_number']) ?></div>
                    <div class="receipt-date">
                        Issued: <strong><?= date('d M Y, h:i A', strtotime($receipt['generated_at'] ?? ($receipt['payment_date'] ?? 'now'))) ?></strong>
                    </div>
                </div>
            </header>

            <!-- 2-Column Info Grid -->
            <section class="info-grid">
                <!-- Billed To: Student / Customer -->
                <div class="info-box">
                    <div class="info-box-title">
                        <span>👤</span> Billed To / Student Details
                    </div>
                    <div class="info-row">
                        <span class="info-label">Student Name:</span>
                        <span class="info-value"><?= e($receipt['student_first_name'] . ' ' . $receipt['student_last_name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Roll / Student ID:</span>
                        <span class="info-value" style="font-family: var(--font-mono); color: var(--primary-accent);"><?= e($receipt['roll_number']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Program &amp; Branch:</span>
                        <span class="info-value"><?= e($receipt['course_name'] ?? ($receipt['course_code'] ?? 'B.Tech')) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Department:</span>
                        <span class="info-value"><?= e($receipt['department_name'] ?? 'Computer Science & Engineering') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Semester / Term:</span>
                        <span class="info-value">Semester <?= e($receipt['semester_number'] ?? '1') ?> (<?= e($receipt['academic_year_name'] ?? '2025-2026') ?>)</span>
                    </div>
                    <?php if (!empty($receipt['student_mobile'])): ?>
                    <div class="info-row">
                        <span class="info-label">Mobile Contact:</span>
                        <span class="info-value">📞 <?= e($receipt['student_mobile']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Payment & Transaction Details -->
                <div class="info-box">
                    <div class="info-box-title">
                        <span>💳</span> Payment &amp; Transaction Details
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Method:</span>
                        <span class="info-value" style="text-transform: uppercase;"><?= e($receipt['payment_method']) ?> (<?= ucfirst(e($receipt['payment_mode'])) ?>)</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Transaction / Bank UTR:</span>
                        <span class="info-value" style="font-family: var(--font-mono); font-size: 0.75rem;"><?= e($receipt['utr_reference'] ?? ($receipt['transaction_id'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment Date:</span>
                        <span class="info-value"><?= date('d M Y', strtotime($receipt['payment_date'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fee Category:</span>
                        <span class="info-value"><?= e($receipt['fee_category_name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Cashier / Processed By:</span>
                        <span class="info-value"><?= e($receipt['cashier_username'] ?? 'Accounts Desk') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Account Code:</span>
                        <span class="info-value" style="font-family: var(--font-mono);"><?= e($receipt['college_code'] ?? 'KEC') ?>-<?= e($receipt['fee_category_code'] ?? 'ACC') ?></span>
                    </div>
                </div>
            </section>

            <!-- Status Banner -->
            <div class="status-banner">
                <div class="paid-tag">
                    <span>✓</span> PAID &amp; VERIFIED
                </div>
                <div class="status-msg">
                    Payment of <strong>₹<?= number_format((float)$receipt['amount_paid'], 2) ?></strong> successfully captured &amp; posted to institutional accounts ledger.
                </div>
            </div>

            <!-- Itemized Particulars Ledger Table -->
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 45%;">Fee Particulars / Product Description</th>
                        <th style="width: 15%;">Account Code</th>
                        <th style="width: 15%;">Period</th>
                        <th style="width: 20%; text-align: right;">Amount Paid (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>
                            <div class="item-main-title"><?= e($receipt['fee_category_name']) ?></div>
                            <div class="item-sub-desc">
                                Student ID: <?= e($receipt['roll_number']) ?> &bull; <?= e($receipt['course_code'] ?? 'ACADEMIC') ?> Sem <?= e($receipt['semester_number'] ?? '1') ?>
                            </div>
                        </td>
                        <td>
                            <code style="font-family: var(--font-mono); color: var(--primary-accent);"><?= e($receipt['fee_category_code']) ?></code>
                        </td>
                        <td>
                            <?= e($receipt['academic_year_name'] ?? '2025-2026') ?>
                        </td>
                        <td style="text-align: right; font-weight: 800; font-size: 0.9375rem;">
                            ₹<?= number_format((float)$receipt['amount_paid'], 2) ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Calculation Grid & Amount in Words -->
            <section class="calculation-grid">
                <!-- Left: Words & QR Security -->
                <div class="words-box">
                    <div>
                        <div class="words-title">Amount Paid in Words (INR):</div>
                        <div class="words-text">"<?= e($receipt['amount_in_words']) ?>"</div>

                        <?php if (!empty($receipt['remarks'])): ?>
                        <div class="words-title" style="margin-top: 0.5rem;">Transaction Notes:</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);"><?= e($receipt['remarks']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="qr-verification-row">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= rawurlencode('KEC-RECEIPT:' . $receipt['receipt_number'] . '|AMT:' . $receipt['amount_paid'] . '|ROLL:' . $receipt['roll_number']) ?>" alt="Verification QR Code" class="qr-thumb">
                        <div class="qr-info-text">
                            <strong>Digital Verification QR</strong><br>
                            Scan to verify authenticity against central college financial ledger.
                        </div>
                    </div>
                </div>

                <!-- Right: Summary Table -->
                <div class="summary-card">
                    <div class="summary-row">
                        <span>Total Prescribed Fee:</span>
                        <strong style="color: var(--text-dark);">₹<?= number_format((float)$receipt['amount_due'], 2) ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Scholarship / Concession:</span>
                        <span>- ₹<?= number_format((float)$receipt['discount'], 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Net Payable Amount:</span>
                        <strong style="color: var(--text-dark);">₹<?= number_format((float)$receipt['final_amount'], 2) ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>GST / Applicable Taxes:</span>
                        <span>₹0.00 (Exempt)</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total Paid:</span>
                        <span>₹<?= number_format((float)$receipt['amount_paid'], 2) ?></span>
                    </div>
                    <div class="summary-row balance-row">
                        <span>Remaining Balance Due:</span>
                        <span>
                            <?php if ((float)$receipt['balance_remaining'] <= 0): ?>
                                <span style="color: var(--success); font-weight: 800;">₹0.00 (Fully Settled)</span>
                            <?php else: ?>
                                <span style="color: #ea580c; font-weight: 800;">₹<?= number_format((float)$receipt['balance_remaining'], 2) ?></span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </section>

            <!-- Legal Disclaimer & Signatures -->
            <footer class="invoice-footer">
                <div class="legal-notice">
                    <strong>Important Terms &amp; Conditions:</strong><br>
                    1. Fees once paid are non-refundable and non-transferable under any circumstances.<br>
                    2. This is a computer-generated official payment receipt validated by the institution's ERP system.<br>
                    3. Retain this invoice copy for examination hall ticket clearance, scholarship audits, and hostel gate verification.
                </div>

                <div class="signature-block">
                    <div class="seal-stamp">
                        🏛️ KEC ACCOUNTS VERIFIED
                    </div>
                    <div class="sign-line"></div>
                    <div class="sign-title">Authorized Finance Officer</div>
                    <div class="sign-subtitle">Kuppam Engineering College</div>
                </div>
            </footer>
        </div>
    </div>

</body>
</html>

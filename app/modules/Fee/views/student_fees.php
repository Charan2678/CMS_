<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin: 0;">My Fee Receipts & Dues Ledger</h1>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
            View your institutional fee statements, payment records, and official downloadable receipts.
        </p>
    </div>
</div>

<?php 
    $totalAssigned = 0.0;
    $totalPaid = 0.0;
    foreach ($fees as $f) {
        $totalAssigned += (float) $f['final_amount'];
        $totalPaid     += (float) $f['total_paid'];
    }
    $totalBalance = max(0.0, $totalAssigned - $totalPaid);
?>

<!-- Stat Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="metric-card">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(2, 132, 199, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--accent-color);">
            💵
        </div>
        <div>
            <div class="metric-label">Total Fee Payable</div>
            <div class="metric-value">₹<?= number_format($totalAssigned, 2) ?></div>
        </div>
    </div>

    <div class="metric-card">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--success);">
            ✅
        </div>
        <div>
            <div class="metric-label">Total Amount Paid</div>
            <div class="metric-value" style="color: var(--success);">₹<?= number_format($totalPaid, 2) ?></div>
        </div>
    </div>

    <div class="metric-card">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(239, 68, 68, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--danger);">
            ⏳
        </div>
        <div>
            <div class="metric-label">Outstanding Dues</div>
            <div class="metric-value" style="color: <?= $totalBalance > 0 ? 'var(--danger)' : 'var(--success)' ?>;">₹<?= number_format($totalBalance, 2) ?></div>
        </div>
    </div>
</div>

<!-- Assigned Fee Structures Table -->
<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>🏷️</span> Fee Breakdown & Status
    </h2>

    <?php if (empty($fees)): ?>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No fee structures currently assigned to your account.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Academic Year</th>
                        <th>Semester</th>
                        <th style="text-align: right;">Amount</th>
                        <th style="text-align: right;">Paid</th>
                        <th style="text-align: right;">Balance</th>
                        <th style="text-align: center;">Due Date</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fees as $f): ?>
                        <?php 
                            $final = (float) $f['final_amount'];
                            $paid  = (float) $f['total_paid'];
                            $bal   = max(0.0, $final - $paid);
                        ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= e($f['category_name']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($f['academic_year_name']) ?></td>
                            <td style="color: var(--text-secondary);">Semester <?= $f['semester_number'] ?></td>
                            <td style="text-align: right; color: var(--text-primary); font-weight: 600;">₹<?= number_format($final, 2) ?></td>
                            <td style="text-align: right; color: var(--success); font-weight: 600;">₹<?= number_format($paid, 2) ?></td>
                            <td style="text-align: right; color: <?= $bal > 0 ? 'var(--danger)' : 'var(--success)' ?>; font-weight: 600;">₹<?= number_format($bal, 2) ?></td>
                            <td style="text-align: center; color: var(--text-secondary);"><?= !empty($f['due_date']) ? date('d M Y', strtotime($f['due_date'])) : 'N/A' ?></td>
                            <td style="text-align: center;">
                                <?php if ($f['status'] === 'paid'): ?>
                                    <span class="badge badge-success">PAID</span>
                                <?php elseif ($f['status'] === 'partial'): ?>
                                    <span class="badge badge-warning">PARTIAL</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">PENDING</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($bal > 0): ?>
                                    <a href="/fee/pay/<?= $f['id'] ?>" class="btn btn-sm btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <span>📱</span> Pay Online / QR
                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--success); font-weight: 700; font-size: 0.8125rem;">✅ Cleared</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Official Payment Receipts Table -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>📜</span> Downloadable Payment Receipts
    </h2>

    <?php if (empty($receipts)): ?>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No payment receipts issued yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Category</th>
                        <th>Payment Date</th>
                        <th>Mode</th>
                        <th style="text-align: right;">Amount Paid</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receipts as $rcp): ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--accent-color); font-family: monospace;"><?= e($rcp['receipt_number']) ?></td>
                            <td style="color: var(--text-primary);"><?= e($rcp['category_name']) ?></td>
                            <td style="color: var(--text-secondary);"><?= date('d M Y', strtotime($rcp['payment_date'])) ?></td>
                            <td style="color: var(--text-secondary); text-transform: uppercase; font-size: 0.8125rem;"><?= e($rcp['payment_method']) ?></td>
                            <td style="text-align: right; color: var(--success); font-weight: 700;">₹<?= number_format((float)$rcp['amount_paid'], 2) ?></td>
                            <td style="text-align: right;">
                                <a href="/fee/receipt/<?= $rcp['receipt_id'] ?>" target="_blank" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; text-decoration: none;">
                                    <span>🖨️</span> View Receipt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$rc = $receipt ?? [];
?>

<div style="max-width: 720px; margin: 0 auto; padding: 1.5rem;">
    <div style="margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;" class="no-print">
        <a href="/transport/history" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Payments History</a>
        <button type="button" onclick="window.print()" class="btn btn-primary" style="font-size: 0.8125rem; font-weight: 700;">Print Receipt 🖨️</button>
    </div>

    <!-- Printable Official Receipt Card -->
    <div class="card" style="padding: 2.5rem; border: 2px solid var(--border-color); border-radius: 16px; background: var(--bg-surface); position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <img src="/assets/images/logo.png" alt="KEC Logo" style="width: 54px; height: 54px; object-fit: contain;">
                <div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; line-height: 1.2;">KUPPAM ENGINEERING COLLEGE</h2>
                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.2rem;">Approved by AICTE • Affiliated to JNTUA • Kuppam, Chittoor Dist., A.P.</div>
                </div>
            </div>
            <div style="text-align: right;">
                <span class="badge badge-success" style="font-size: 0.875rem; padding: 0.4rem 0.75rem;">OFFICIAL RECEIPT</span>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.35rem; font-family: monospace;">Receipt #: TR-<?= (int)($rc['id'] ?? 1001) ?></div>
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: #2563eb; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">
                🚌 ANNUAL TRANSPORT FEE RECEIPT (ACADEMIC YEAR 2026–2027)
            </h3>
        </div>

        <!-- Student & Route Information Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; background: var(--bg-main); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); font-size: 0.875rem;">
            <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.75rem; font-weight: 600;">STUDENT NAME:</span>
                    <strong style="color: var(--text-primary); display: block; font-size: 0.9375rem;"><?= e(($rc['first_name'] ?? 'Student') . ' ' . ($rc['last_name'] ?? '')) ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.75rem; font-weight: 600;">ROLL NUMBER / ID:</span>
                    <strong style="font-family: monospace; color: var(--accent-color); display: block;"><?= e($rc['roll_number'] ?? '2026-CSE-001') ?></strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.75rem; font-weight: 600;">DEPARTMENT:</span>
                    <strong style="display: block;"><?= e($rc['department_name'] ?? 'Computer Science & Engineering') ?></strong>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.75rem; font-weight: 600;">BUS ROUTE &amp; CODE:</span>
                    <strong style="color: var(--text-primary); display: block; font-size: 0.9375rem;"><?= e($rc['route_name']) ?> (<?= e($rc['route_code']) ?>)</strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.75rem; font-weight: 600;">BUS / VEHICLE NUMBER:</span>
                    <strong style="color: #2563eb; display: block;"><?= e($rc['bus_number']) ?> (<?= e($rc['bus_reg_number']) ?>)</strong>
                </div>
                <div>
                    <span style="color: var(--text-secondary); font-size: 0.75rem; font-weight: 600;">PAYMENT DATE:</span>
                    <strong style="display: block;"><?= date('d M Y', strtotime($rc['payment_date'] ?? 'now')) ?></strong>
                </div>
            </div>
        </div>

        <!-- Payment Breakdown Table -->
        <table class="table" style="margin-bottom: 1.5rem;">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Payment Method</th>
                    <th>Transaction / UTR ID</th>
                    <th style="text-align: right;">Amount Paid</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: 700; color: var(--text-primary);">Annual College Bus Transport Fee (2026–27)</td>
                    <td><?= e(str_replace('_', ' ', $rc['payment_method'] ?? 'UPI QR')) ?></td>
                    <td style="font-family: monospace; font-weight: 700;"><?= e($rc['transaction_id']) ?></td>
                    <td style="text-align: right; font-weight: 800; font-size: 1.1rem; color: #10b981;">
                        ₹<?= number_format((float)$rc['amount'], 2) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Seal & Signatures -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px dashed var(--border-color);">
            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                <div>Computer generated official receipt.</div>
                <div>Verified by Transport Accounts Desk</div>
                <div style="margin-top: 0.2rem; font-weight: 700; color: #10b981;">STATUS: VERIFIED &amp; PAID ✅</div>
            </div>

            <div style="text-align: center;">
                <div style="font-family: 'Brush Script MT', cursive, sans-serif; font-size: 1.5rem; color: #1e3a8a; margin-bottom: 0.25rem;">Transport Office</div>
                <div style="border-top: 1px solid var(--border-color); padding-top: 0.25rem; font-weight: 700; font-size: 0.8125rem; color: var(--text-primary);">
                    Authorized Signatory
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, header, .sidebar, footer { display: none !important; }
    .main-wrapper { margin: 0 !important; padding: 0 !important; }
    body { background: #fff !important; }
}
</style>

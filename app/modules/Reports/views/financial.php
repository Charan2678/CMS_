<!-- Financial Key Metrics Cards — Full Width -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; width: 100%;">
    <div class="metric-card" style="border-left: 4px solid var(--accent-color) !important;">
        <div>
            <div class="metric-label">Total Billed Fees</div>
            <div class="metric-value" style="color: var(--accent-color);">₹<?= number_format($totalBilled, 2) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Total invoices generated</div>
        </div>
        <div class="metric-icon" style="font-size: 2rem;">📄</div>
    </div>

    <div class="metric-card" style="border-left: 4px solid var(--success) !important;">
        <div>
            <div class="metric-label">Total Collected Revenue</div>
            <div class="metric-value" style="color: var(--success);">₹<?= number_format($totalCollected, 2) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Realized payments</div>
        </div>
        <div class="metric-icon" style="font-size: 2rem;">💰</div>
    </div>

    <div class="metric-card" style="border-left: 4px solid var(--danger) !important;">
        <div>
            <div class="metric-label">Outstanding Dues</div>
            <div class="metric-value" style="color: var(--danger);">₹<?= number_format($totalPending, 2) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Pending balance due</div>
        </div>
        <div class="metric-icon" style="font-size: 2rem;">⏳</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; width: 100%;">
    <!-- Revenue by Payment Method -->
    <div class="card">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>💳</span> Revenue by Payment Channel
        </h3>

        <div style="overflow-x: auto; width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Channel</th>
                        <th>Transactions</th>
                        <th style="text-align: right;">Total Collected</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($methodBreakdown)): ?>
                        <tr>
                            <td colspan="3" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No transaction data recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($methodBreakdown as $pm): ?>
                            <tr>
                                <td style="font-weight: 700; text-transform: uppercase; color: var(--accent-color);"><?= e($pm['payment_method']) ?></td>
                                <td style="color: var(--text-secondary);"><?= e($pm['count']) ?> Txns</td>
                                <td style="text-align: right; font-weight: 800; color: var(--success);">₹<?= number_format((float)$pm['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Revenue by Fee Category -->
    <div class="card">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>🏷️</span> Billed Fees by Category
        </h3>

        <div style="overflow-x: auto; width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fee Category</th>
                        <th style="text-align: right;">Total Billed Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categoryBreakdown)): ?>
                        <tr>
                            <td colspan="2" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No fee structure data recorded.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categoryBreakdown as $cat): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($cat['category_name']) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--accent-color);">₹<?= number_format((float)$cat['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

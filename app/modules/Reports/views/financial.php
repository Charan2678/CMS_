<!-- Financial Key Metrics Cards -->
<div class="grid-metrics">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Billed Fees</div>
            <div class="metric-value" style="color: #a5b4fc;">₹<?= number_format($totalBilled, 2) ?></div>
        </div>
        <div class="metric-icon">📄</div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Total Collected Revenue</div>
            <div class="metric-value" style="color: #86efac;">₹<?= number_format($totalCollected, 2) ?></div>
        </div>
        <div class="metric-icon">💰</div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Outstanding Dues</div>
            <div class="metric-value" style="color: #fca5a5;">₹<?= number_format($totalPending, 2) ?></div>
        </div>
        <div class="metric-icon">⏳</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <!-- Revenue by Payment Method -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Revenue by Payment Channel</h3>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Channel</th>
                    <th style="padding: 0.75rem;">Transactions</th>
                    <th style="padding: 0.75rem; text-align: right;">Total Collected</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($methodBreakdown)): ?>
                    <tr>
                        <td colspan="3" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No transaction data recorded.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($methodBreakdown as $pm): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; text-transform: uppercase; color: #a5b4fc;"><?= e($pm['payment_method']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($pm['count']) ?> Txns</td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #86efac;">₹<?= number_format((float)$pm['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Revenue by Fee Category -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Billed Fees by Category</h3>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Fee Category</th>
                    <th style="padding: 0.75rem; text-align: right;">Total Billed Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categoryBreakdown)): ?>
                    <tr>
                        <td colspan="2" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No fee structure data recorded.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categoryBreakdown as $cat): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 600;"><?= e($cat['category_name']) ?></td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #a5b4fc;">₹<?= number_format((float)$cat['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

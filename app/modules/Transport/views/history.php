<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">💳 My Transport Payments History</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">View your past transport fee payment transactions, verification statuses, and download official fee receipts</p>
    </div>
    <a href="/transport/routes" class="btn btn-primary" style="font-size: 0.8125rem;">🚌 View Bus Routes &amp; Details</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>

<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
        <span>Transport Payment Audit Log (<?= count($payments) ?> Payments)</span>
        <span style="font-size: 0.75rem; color: var(--text-secondary);">Verified College Receipts</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Academic Year</th>
                    <th>Selected Route</th>
                    <th>Bus Number</th>
                    <th>Amount Paid</th>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-secondary);">
                            No transport fee payments recorded yet. <a href="/transport/routes">Select a bus route to get started</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $p): ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                            <td><span class="badge badge-info"><?= e($p['academic_year'] ?? '2026-2027') ?></span></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($p['route_name']) ?> (<?= e($p['route_code']) ?>)</td>
                            <td style="font-weight: 700; color: #2563eb;"><?= e($p['bus_number']) ?></td>
                            <td style="font-weight: 800; font-size: 1rem; color: #10b981;">₹<?= number_format((float)$p['amount'], 2) ?></td>
                            <td style="font-family: monospace; font-size: 0.8125rem;"><?= e($p['transaction_id']) ?></td>
                            <td>
                                <?php if ($p['verification_status'] === 'verified' || $p['payment_status'] === 'paid'): ?>
                                    <span class="badge badge-success">PAID ✅</span>
                                <?php elseif ($p['verification_status'] === 'rejected' || $p['payment_status'] === 'rejected'): ?>
                                    <span class="badge badge-danger">REJECTED 🔴</span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="background:#f59e0b; color:#fff;">PENDING VERIFICATION ⏳</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['verification_status'] === 'verified' || $p['payment_status'] === 'paid'): ?>
                                    <a href="/transport/receipt/<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" style="font-weight: 700;">View Receipt 📄</a>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Verification Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Route History & Change Audit Card -->
<div class="card" style="margin-top: 1.5rem;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
        <span>🚌 Previous Transport Subscriptions &amp; Route History</span>
        <span style="font-size: 0.75rem; color: var(--text-secondary);">Fleet History Log</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Academic Year</th>
                    <th>Route Name &amp; Code</th>
                    <th>Bus Number</th>
                    <th>Pickup Point &amp; Time</th>
                    <th>Annual Fee</th>
                    <th>Subscription Status</th>
                    <th>Assigned Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($routeHistory)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No past transport subscription history found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($routeHistory as $rh): ?>
                        <tr>
                            <td><span class="badge badge-info"><?= e($rh['academic_year'] ?? '2026-2027') ?></span></td>
                            <td style="font-weight: 700; color: var(--text-primary);">
                                <?= e($rh['route_name']) ?> (<?= e($rh['route_code']) ?>)
                            </td>
                            <td style="font-weight: 700; color: #2563eb;"><?= e($rh['bus_number']) ?></td>
                            <td style="font-size: 0.8125rem;">🚏 <?= e($rh['pickup_point']) ?> (<?= e($rh['pickup_time']) ?>)</td>
                            <td style="font-weight: 700;">₹<?= number_format((float)$rh['annual_fee'], 2) ?></td>
                            <td>
                                <?php if ($rh['subscription_status'] === 'active'): ?>
                                    <span class="badge badge-success">ACTIVE BUS ✅</span>
                                <?php elseif ($rh['subscription_status'] === 'transferred'): ?>
                                    <span class="badge badge-secondary" style="background:#64748b; color:#fff;">TRANSFERRED 🔄</span>
                                <?php elseif ($rh['subscription_status'] === 'cancelled'): ?>
                                    <span class="badge badge-danger">CANCELLED 🛑</span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="background:#f59e0b; color:#fff;"><?= strtoupper(e($rh['subscription_status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size: 0.8125rem; color: var(--text-secondary);"><?= date('d M Y', strtotime($rh['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


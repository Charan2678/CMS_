<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">🔄 Transport Route &amp; Bus Change Requests</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Review student route transfer requests, verify fee differences, approve seat transfers, or log rejection reasons</p>
    </div>
    <a href="/transport" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Transport Overview</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- Requests Audit Table -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
        <span>Student Route Transfer Requests (<?= count($changeRequests) ?> Requests)</span>
        <span style="font-size: 0.75rem; color: var(--text-secondary);">Real-time Fleet Re-allocations</span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Dept</th>
                    <th>Current Route &amp; Bus</th>
                    <th>New Requested Route</th>
                    <th>Fee Difference</th>
                    <th>Payment Status</th>
                    <th>Request Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($changeRequests)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2.5rem; color: var(--text-secondary);">
                            No route change requests currently pending.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($changeRequests as $req): ?>
                        <?php $fdiff = (float)$req['fee_difference']; ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: var(--accent-color);"><?= e($req['roll_number']) ?></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($req['first_name'] . ' ' . $req['last_name']) ?></td>
                            <td><span class="badge badge-info"><?= e($req['department'] ?? 'CSE') ?></span></td>
                            <td style="font-size: 0.8125rem; color: var(--text-secondary);">
                                <strong><?= e($req['old_route_code']) ?></strong> (<?= e($req['old_bus']) ?>)
                            </td>
                            <td style="font-size: 0.8125rem; color: #2563eb; font-weight: 700;">
                                <strong><?= e($req['new_route_code']) ?></strong> (<?= e($req['new_bus']) ?>)
                                <div style="font-size: 0.725rem; color: var(--text-secondary); font-weight: normal;"><?= e($req['new_route_name']) ?></div>
                            </td>
                            <td>
                                <?php if ($fdiff > 0): ?>
                                    <strong style="color: #2563eb;">+ ₹<?= number_format($fdiff, 2) ?></strong>
                                    <div style="font-size: 0.68rem; color: var(--text-secondary);">Additional Due</div>
                                <?php elseif ($fdiff < 0): ?>
                                    <strong style="color: #f59e0b;">- ₹<?= number_format(abs($fdiff), 2) ?></strong>
                                    <div style="font-size: 0.68rem; color: #d97706;">Pending Credit Adjustment</div>
                                <?php else: ?>
                                    <span style="font-weight: 700; color: #10b981;">₹0.00</span>
                                    <div style="font-size: 0.68rem; color: var(--text-secondary);">No Additional Payment</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($req['payment_status'] === 'paid'): ?>
                                    <span class="badge badge-success">PAID ✅</span>
                                <?php elseif ($req['payment_status'] === 'pending'): ?>
                                    <span class="badge badge-warning" style="background:#f59e0b; color:#fff;">PENDING VERIFICATION ⏳</span>
                                <?php elseif ($req['payment_status'] === 'unpaid'): ?>
                                    <span class="badge badge-danger">UNPAID 💳</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">N/A (₹0)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($req['request_status'] === 'approved'): ?>
                                    <span class="badge badge-success">APPROVED ✅</span>
                                <?php elseif ($req['request_status'] === 'rejected'): ?>
                                    <span class="badge badge-danger" title="<?= e($req['rejection_reason']) ?>">REJECTED 🔴</span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="background:#f59e0b; color:#fff;">PENDING APPROVAL ⏳</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($req['request_status'] === 'pending'): ?>
                                    <div style="display: flex; gap: 0.35rem;">
                                        <form method="POST" action="/transport/change-requests" style="margin: 0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_action" value="approve_request">
                                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; background: #10b981;">Approve ✅</button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="openRejectModal(<?= $req['id'] ?>, '<?= e($req['first_name']) ?>')" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Reject 🔴</button>
                                    </div>
                                <?php elseif ($req['request_status'] === 'rejected'): ?>
                                    <span style="font-size: 0.75rem; color: var(--danger);" title="<?= e($req['rejection_reason']) ?>">
                                        Reason: <?= e(mb_strimwidth($req['rejection_reason'] ?? '', 0, 20, '...')) ?>
                                    </span>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: #10b981; font-weight: 700;">Transferred &amp; Active ✅</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Rejection Modal -->
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="max-width: 460px; width: 100%; background: var(--bg-surface); border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.25);">
        <div style="padding: 1.15rem 1.35rem; background: var(--danger); color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800;">🔴 Reject Route Change Request</h3>
            <button type="button" onclick="closeRejectModal()" style="background: none; border: none; color: #fff; font-size: 1.25rem; cursor: pointer; font-weight: 800;">&times;</button>
        </div>
        <form method="POST" action="/transport/change-requests" style="padding: 1.35rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="reject_request">
            <input type="hidden" name="request_id" id="rejectRequestId">

            <div style="margin-bottom: 1rem;">
                <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.35rem;">Rejection Reason (Required) *</label>
                <textarea name="rejection_reason" rows="3" class="form-control" required placeholder="e.g. Requested bus route is at full capacity / Seat unallocated" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;"></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary" style="flex: 1;">Cancel</button>
                <button type="submit" class="btn btn-danger" style="flex: 1; font-weight: 800;">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id, studentName) {
    document.getElementById('rejectRequestId').value = id;
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>

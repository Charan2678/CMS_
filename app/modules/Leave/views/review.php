<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<div class="card" style="border-top: 4px solid var(--accent-color);">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📝</span> Institutional Leave &amp; Outpass Review Portal
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Review, approve, or reject student, faculty, and staff leave requests.</p>
        </div>
    </div>

    <?php if (empty($leaves)): ?>
        <div style="text-align: center; padding: 3rem 1rem; color: var(--text-secondary);">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✨</div>
            <p style="font-size: 0.9375rem; font-weight: 600;">No pending or historical leave requests found.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                        <th style="padding: 0.75rem;">Applicant</th>
                        <th style="padding: 0.75rem;">Type</th>
                        <th style="padding: 0.75rem;">Duration</th>
                        <th style="padding: 0.75rem;">Reason &amp; Purpose</th>
                        <th style="padding: 0.75rem;">Status</th>
                        <th style="padding: 0.75rem; text-align: center;">Action / Review</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $l): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($l['applicant_name'] ?? 'Applicant') ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    <?= e(ucfirst($l['applicant_type'])) ?> <?= !empty($l['roll_number']) ? ' &bull; ' . e($l['roll_number']) : '' ?>
                                </div>
                            </td>
                            <td style="padding: 0.75rem;">
                                <span class="badge badge-info" style="text-transform: uppercase;"><?= e(str_replace('_', ' ', $l['leave_type'])) ?></span>
                            </td>
                            <td style="padding: 0.75rem; white-space: nowrap;">
                                <strong><?= date('d M', strtotime($l['from_date'])) ?></strong> &rarr; <strong><?= date('d M Y', strtotime($l['to_date'])) ?></strong>
                            </td>
                            <td style="padding: 0.75rem; max-width: 250px;">
                                <div style="line-height: 1.35;"><?= e($l['reason']) ?></div>
                                <?php if (!empty($l['expected_return_time'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--accent-color); margin-top: 0.25rem;">
                                        Expected Return: <?= date('d M, h:i A', strtotime($l['expected_return_time'])) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($l['remarks'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.25rem;">
                                        Reviewer Remarks: <?= e($l['remarks']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ($l['status'] === 'approved'): ?>
                                    <span class="badge badge-success">✅ Approved</span>
                                <?php elseif ($l['status'] === 'rejected'): ?>
                                    <span class="badge badge-danger">❌ Rejected</span>
                                <?php elseif ($l['status'] === 'completed'): ?>
                                    <span class="badge badge-secondary">🏁 Completed</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">⏳ Pending Review</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php if ($l['status'] === 'pending'): ?>
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                        <form method="POST" action="/leave/review" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="leave_id" value="<?= $l['id'] ?>">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-sm btn-success" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                                                Approve
                                            </button>
                                        </form>

                                        <button type="button" onclick="openRejectModal(<?= $l['id'] ?>)" class="btn btn-sm btn-danger" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                                            Reject
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 99999; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; max-width: 420px; width: 100%; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--danger);">Reject Leave Request</h3>
        <form method="POST" action="/leave/review">
            <?= csrf_field() ?>
            <input type="hidden" name="leave_id" id="rejectLeaveId" value="">
            <input type="hidden" name="status" value="rejected">
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Rejection Reason / Remarks</label>
                <textarea name="remarks" rows="3" class="form-control" placeholder="Provide reason for rejecting..." style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-main); color: var(--text-primary);" required></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-secondary" style="padding: 0.4rem 0.75rem;">Cancel</button>
                <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.75rem;">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id) {
    document.getElementById('rejectLeaveId').value = id;
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>

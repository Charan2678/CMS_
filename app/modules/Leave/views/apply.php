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

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Application Form Card -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📝</span> <?= $isParent ? "Apply Leave for Ward" : "Submit Leave / Outpass Request" ?>
        </h2>

        <form method="POST" action="/leave/apply">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 0.35rem; display: block;">Request Type</label>
                <select name="leave_type" id="leave_type_select" class="form-control" style="width: 100%; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" onchange="toggleOutpassFields(this.value)" required>
                    <option value="casual">Casual Leave</option>
                    <option value="sick">Medical / Sick Leave</option>
                    <option value="hostel_outpass">🚪 Hostel Outpass (Night / Weekend)</option>
                    <option value="duty">On-Duty / Academic Leave</option>
                    <option value="other">Other Reason</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 0.35rem; display: block;">From Date</label>
                    <input type="date" name="from_date" value="<?= date('Y-m-d') ?>" class="form-control" style="width: 100%; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 0.35rem; display: block;">To Date</label>
                    <input type="date" name="to_date" value="<?= date('Y-m-d') ?>" class="form-control" style="width: 100%; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                </div>
            </div>

            <div id="outpassFieldWrapper" class="form-group" style="margin-bottom: 1rem; display: none;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 0.35rem; display: block;">Expected Hostel Return Date &amp; Time</label>
                <input type="datetime-local" name="expected_return_time" class="form-control" style="width: 100%; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 0.35rem; display: block;">Reason / Detailed Purpose</label>
                <textarea name="reason" rows="3" class="form-control" placeholder="Please provide specific reason for leave or destination for outpass..." style="width: 100%; padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; border-radius: 8px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <span>🚀</span> Submit Request for Review
            </button>
        </form>
    </div>

    <!-- Application History Table -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📋</span> Application History &amp; Status
        </h2>

        <?php if (empty($myLeaves)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; text-align: center; padding: 2rem 0;">No prior leave or outpass applications recorded.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.6rem 0.75rem;">Type</th>
                            <th style="padding: 0.6rem 0.75rem;">Dates</th>
                            <th style="padding: 0.6rem 0.75rem;">Reason</th>
                            <th style="padding: 0.6rem 0.75rem;">Status</th>
                            <th style="padding: 0.6rem 0.75rem;">Reviewed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($myLeaves as $l): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem;">
                                    <span class="badge badge-info" style="text-transform: uppercase;"><?= e(str_replace('_', ' ', $l['leave_type'])) ?></span>
                                </td>
                                <td style="padding: 0.75rem; white-space: nowrap;">
                                    <?= date('d M Y', strtotime($l['from_date'])) ?> &rarr; <?= date('d M Y', strtotime($l['to_date'])) ?>
                                </td>
                                <td style="padding: 0.75rem; max-width: 180px;">
                                    <?= e($l['reason']) ?>
                                    <?php if (!empty($l['remarks'])): ?>
                                        <div style="font-size: 0.7rem; color: var(--accent-color); margin-top: 0.2rem;">Remarks: <?= e($l['remarks']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <?php if ($l['status'] === 'approved'): ?>
                                        <span class="badge badge-success">✅ Approved</span>
                                    <?php elseif ($l['status'] === 'rejected'): ?>
                                        <span class="badge badge-danger">❌ Rejected</span>
                                    <?php elseif ($l['status'] === 'completed'): ?>
                                        <span class="badge badge-secondary">🏁 Returned</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">⏳ Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem; color: var(--text-secondary);">
                                    <?= e($l['reviewer_name'] ?? 'Pending') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleOutpassFields(val) {
    var wrapper = document.getElementById('outpassFieldWrapper');
    if (wrapper) {
        wrapper.style.display = val === 'hostel_outpass' ? 'block' : 'none';
    }
}
</script>

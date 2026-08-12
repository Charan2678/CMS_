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
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('door-open', 'icon-md') ?> Hostel Outpass &amp; Security Gate Register
            </h2>
            <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Track student resident outpasses, expected return times, and check-in arrivals.</p>
        </div>
        <div>
            <a href="/hostel/management" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.45rem 1rem; border-radius: 20px; font-weight: 700; font-size: 0.85rem; background: rgba(2, 132, 199, 0.05); border: 1.5px solid #0284c7; color: #0284c7; transition: all 0.2s ease;">
                ← Back to Hostel Overview
            </a>
        </div>
    </div>

    <?php if (empty($outpasses)): ?>
        <div style="text-align: center; padding: 3rem 1rem; color: var(--text-secondary);">
            <div style="color: var(--accent-color); margin-bottom: 0.5rem;">
                <?= icon('door-open', 'icon-xl') ?>
            </div>
            <p style="font-size: 0.9375rem; font-weight: 600;">No active or past hostel outpasses found.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.8125rem;">
                <thead>
                    <tr>
                        <th>Resident Student</th>
                        <th>Hostel &amp; Room</th>
                        <th>Leave Period</th>
                        <th>Expected Return</th>
                        <th>Destination / Reason</th>
                        <th>Status</th>
                        <th style="text-align: center;">Gate Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($outpasses as $op): ?>
                        <?php 
                            $isOverdue = ($op['status'] === 'approved') && !empty($op['expected_return_time']) && (strtotime($op['expected_return_time']) < time());
                        ?>
                        <tr style="<?= $isOverdue ? 'background: rgba(239, 68, 68, 0.08);' : '' ?>">
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($op['student_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.35rem; margin-top: 0.15rem;">
                                    <?= e($op['roll_number']) ?> &bull; <?= icon('phone', 'icon-xs') ?> <?= e($op['student_phone'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td>
                                <div><?= e($op['block_name'] ?? 'Block A') ?></div>
                                <div style="font-size: 0.75rem; color: var(--accent-color); font-weight: 600;">Room <?= e($op['room_number'] ?? 'N/A') ?></div>
                            </td>
                            <td style="white-space: nowrap; color: var(--text-secondary);">
                                <?= date('d M', strtotime($op['from_date'])) ?> &rarr; <?= date('d M Y', strtotime($op['to_date'])) ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <?php if (!empty($op['expected_return_time'])): ?>
                                    <div style="font-weight: 700; color: <?= $isOverdue ? 'var(--danger)' : 'var(--text-primary)' ?>;">
                                        <?= date('d M, h:i A', strtotime($op['expected_return_time'])) ?>
                                    </div>
                                    <?php if ($isOverdue): ?>
                                        <span class="badge badge-danger" style="font-size: 0.65rem;"><?= icon('alert-triangle', 'icon-xs') ?> Overdue Return</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 200px; color: var(--text-primary);">
                                <?= e($op['reason']) ?>
                            </td>
                            <td>
                                <?php if ($op['status'] === 'approved'): ?>
                                    <span class="badge badge-warning"><?= icon('door-open', 'icon-xs') ?> Outside Premises</span>
                                <?php elseif ($op['status'] === 'completed'): ?>
                                    <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> Checked-In</span>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 0.2rem;">
                                        <?= date('d M, h:i A', strtotime($op['actual_return_time'] ?? 'now')) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= e(ucfirst($op['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($op['status'] === 'approved'): ?>
                                    <form method="POST" action="/leave/outpass-checkin">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="leave_id" value="<?= $op['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-primary" style="padding: 0.35rem 0.65rem; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                                            <?= icon('log-in', 'icon-xs') ?> Mark Checked-In
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Logged</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

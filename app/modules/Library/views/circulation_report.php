<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">📈 Circulation Analytics &amp; Transaction Report</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Historical transaction log of all book issues, returns, and renewals</p>
    </div>
    <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
</div>

<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Full Circulation Ledger (<?= count($issues) ?> Records)
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Issue ID</th>
                    <th>Member Roll / Name</th>
                    <th>Book Title</th>
                    <th>Issued Date</th>
                    <th>Due Date</th>
                    <th>Returned Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($issues as $is): ?>
                    <tr>
                        <td style="font-family: monospace;">#<?= $is['id'] ?></td>
                        <td>
                            <strong style="color: var(--text-primary);"><?= e(trim(($is['first_name'] ?? '') . ' ' . ($is['last_name'] ?? ''))) ?></strong>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); font-family: monospace;"><?= e($is['roll_number'] ?? 'ID: ' . $is['issued_to_id']) ?></div>
                        </td>
                        <td style="font-weight: 600;"><?= e($is['book_title']) ?></td>
                        <td><?= e($is['issued_date'] ?? $is['created_at']) ?></td>
                        <td><?= e($is['due_date']) ?></td>
                        <td><?= $is['returned_date'] ? e($is['returned_date']) : '<span style="color:var(--text-secondary);">—</span>' ?></td>
                        <td>
                            <?php if ($is['status'] === 'returned'): ?>
                                <span class="badge badge-success">Returned</span>
                            <?php elseif ($is['status'] === 'overdue' || strtotime($is['due_date']) < strtotime(date('Y-m-d'))): ?>
                                <span class="badge badge-danger">Overdue</span>
                            <?php else: ?>
                                <span class="badge badge-info">Active</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

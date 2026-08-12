<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--danger); margin: 0;">🚨 Overdue Books Log</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Comprehensive audit of defaulting members and days overdue</p>
    </div>
    <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
</div>

<div class="card" style="border-top: 4px solid var(--danger);">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Overdue Books Audit Log (<?= count($overdueList) ?> Overdue Items)
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Member Name</th>
                    <th>Roll Number</th>
                    <th>Book Title</th>
                    <th>Due Date</th>
                    <th>Days Overdue</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($overdueList)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            🎉 No books are currently overdue.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($overdueList as $ov): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e(trim(($ov['first_name'] ?? '') . ' ' . ($ov['last_name'] ?? ''))) ?></td>
                            <td style="font-family: monospace; color: var(--accent-color);"><?= e($ov['roll_number'] ?? 'ID: ' . $ov['issued_to_id']) ?></td>
                            <td style="font-weight: 600;"><?= e($ov['book_title']) ?></td>
                            <td style="color: var(--danger); font-weight: 700;"><?= e($ov['due_date']) ?></td>
                            <td><span class="badge badge-danger">+<?= (int) ($ov['days_overdue'] ?? 1) ?> Days</span></td>
                            <td>
                                <form method="POST" action="/library/issue" style="margin: 0;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_action" value="return_book">
                                    <input type="hidden" name="issue_id" value="<?= $ov['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">Process Return</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

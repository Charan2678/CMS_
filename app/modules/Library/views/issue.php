<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">📖 Book Issue &amp; Return Desk</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Process student borrowings, returns, and track active circulation</p>
    </div>
    <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Issue Book Form -->
    <div class="card">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
            ➕ Issue Book
        </div>
        <form method="POST" action="/library/issue" style="padding: 1.25rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="issue_book">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Book *</label>
                <select name="book_id" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    <option value="">-- Choose Book --</option>
                    <?php foreach ($books as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= (int)$b['available_copies'] < 1 ? 'disabled' : '' ?>>
                            <?= e($b['title']) ?> (Stock: <?= $b['available_copies'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Student *</label>
                <select name="student_id" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    <option value="">-- Choose Student --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= e($s['roll_number']) ?> — <?= e($s['first_name'] . ' ' . $s['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Lending Days</label>
                <input type="number" name="due_days" value="14" min="1" max="60" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Confirm Issue</button>
        </form>
    </div>

    <!-- Active Circulation Table -->
    <div class="card">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
            Active Borrowing Records (<?= count($issues) ?>)
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Book Title</th>
                        <th>Issued Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($issues as $is): ?>
                        <tr>
                            <td style="font-weight: 600;"><?= e($is['roll_number'] ?? 'ID: ' . $is['issued_to_id']) ?></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($is['book_title']) ?></td>
                            <td><?= e($is['issued_date'] ?? $is['created_at']) ?></td>
                            <td><?= e($is['due_date']) ?></td>
                            <td>
                                <?php if ($is['status'] === 'returned'): ?>
                                    <span class="badge badge-success">Returned</span>
                                <?php elseif ($is['status'] === 'overdue' || strtotime($is['due_date']) < strtotime(date('Y-m-d'))): ?>
                                    <span class="badge badge-danger">Overdue</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($is['status'] !== 'returned'): ?>
                                    <form method="POST" action="/library/issue" style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="return_book">
                                        <input type="hidden" name="issue_id" value="<?= $is['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Return</button>
                                    </form>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

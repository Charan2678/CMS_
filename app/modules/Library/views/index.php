<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($canManage)): ?>
<!-- Circulation & Management Action Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- 1. Issue Book Form -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
            <span>📖</span> Issue Book to Student
        </h3>
        <form method="POST" action="/library">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="issue_book">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Book *</label>
                <select name="book_id" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                    <option value="">-- Choose Book from Catalogue --</option>
                    <?php foreach ($books as $bk): ?>
                        <?php $avail = (int)$bk['available_copies']; ?>
                        <option value="<?= $bk['id'] ?>" <?= $avail <= 0 ? 'disabled style="color: var(--danger);"' : '' ?>>
                            <?= e($bk['title']) ?> (<?= $avail ?> Copies Available)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Student *</label>
                <select name="student_id" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                    <option value="">-- Choose Student --</option>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>">
                            <?= e($st['roll_number']) ?> — <?= e($st['first_name'] . ' ' . $st['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Loan Period (Days)</label>
                <input type="number" name="due_days" value="14" min="1" max="60" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 700;">Issue Book</button>
        </form>
    </div>

    <!-- 2. Add New Book Form -->
    <div class="card" style="border-top: 4px solid #8b5cf6;">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
            <span>➕</span> Add Book to Catalogue
        </h3>
        <form method="POST" action="/library">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="create_book">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Book Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Clean Architecture" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Author *</label>
                    <input type="text" name="author" required placeholder="e.g. Robert C. Martin" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Category / Subject</label>
                    <input type="text" name="category" placeholder="e.g. Computer Science" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Total Copies</label>
                    <input type="number" name="total_copies" value="5" min="1" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
            </div>

            <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 0.6rem; font-weight: 700; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid #8b5cf6;">Add to Catalogue</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 3. Circulation Issues Register -->
<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>📋</span> Active &amp; Historical Book Issues Register
    </h2>

    <?php if (empty($issues)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">No book issue records found.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                        <th style="padding: 0.65rem 0.75rem;">Book Title</th>
                        <th style="padding: 0.65rem 0.75rem;">Borrower Student</th>
                        <th style="padding: 0.65rem 0.75rem;">Issue Date</th>
                        <th style="padding: 0.65rem 0.75rem;">Due Date</th>
                        <th style="padding: 0.65rem 0.75rem;">Status / Overdue Fine</th>
                        <th style="padding: 0.65rem 0.75rem; text-align: center;">Circulation Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($issues as $is): ?>
                        <?php 
                            $isOverdue = ($is['status'] === 'issued') && (strtotime($is['due_date']) < strtotime(date('Y-m-d')));
                            $overdueDays = $isOverdue ? max(0, (int)floor((time() - strtotime($is['due_date'])) / 86400)) : 0;
                            $calcFine = $overdueDays * 5.00;
                        ?>
                        <tr style="border-bottom: 1px solid var(--border-color); <?= $isOverdue ? 'background: rgba(239, 68, 68, 0.08);' : '' ?>">
                            <td style="padding: 0.75rem; font-weight: 700; color: var(--text-primary);">
                                <?= e($is['book_title']) ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 600; color: var(--accent-color);"><?= e($is['first_name'] . ' ' . $is['last_name']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);"><?= e($is['roll_number']) ?></div>
                            </td>
                            <td style="padding: 0.75rem; color: var(--text-secondary); white-space: nowrap;">
                                <?= date('d M Y', strtotime($is['issue_date'])) ?>
                            </td>
                            <td style="padding: 0.75rem; white-space: nowrap;">
                                <span style="font-weight: 600; color: <?= $isOverdue ? 'var(--danger)' : 'var(--text-primary)' ?>;">
                                    <?= date('d M Y', strtotime($is['due_date'])) ?>
                                </span>
                                <?php if ($isOverdue): ?>
                                    <div style="font-size: 0.7rem; color: var(--danger); font-weight: 700;">🚨 <?= $overdueDays ?> Days Overdue</div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ($is['status'] === 'returned'): ?>
                                    <span class="badge badge-success">RETURNED</span>
                                    <?php if (!empty($is['fine_amount']) && (float)$is['fine_amount'] > 0): ?>
                                        <div style="font-size: 0.7rem; color: var(--accent-color);">Fine Paid: ₹<?= number_format((float)$is['fine_amount'], 2) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-warning">BORROWED</span>
                                    <?php if ($isOverdue): ?>
                                        <div style="font-size: 0.7rem; color: var(--danger); font-weight: 700; margin-top: 0.2rem;">Fine: ₹<?= number_format($calcFine, 2) ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php if ($is['status'] === 'issued' && !empty($canManage)): ?>
                                    <form method="POST" action="/library" style="display: inline;" onsubmit="return confirm('Confirm return of this book? <?= $isOverdue ? "Overdue fine of ₹" . number_format($calcFine, 2) . " will be applied." : "" ?>');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="return_book">
                                        <input type="hidden" name="issue_id" value="<?= $is['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                                            📥 Return Book
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- 4. Catalogue Books Inventory -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>📚</span> Library Catalogue &amp; Shelf Inventory
    </h2>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                    <th style="padding: 0.65rem 0.75rem;">Book Title</th>
                    <th style="padding: 0.65rem 0.75rem;">Author</th>
                    <th style="padding: 0.65rem 0.75rem;">Category</th>
                    <th style="padding: 0.65rem 0.75rem;">Availability</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: var(--text-primary);"><?= e($b['title']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($b['author']) ?></td>
                        <td style="padding: 0.75rem;"><span class="badge badge-info"><?= e($b['category']) ?></span></td>
                        <td style="padding: 0.75rem; font-weight: 800; color: <?= (int)$b['available_copies'] > 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                            <?= e($b['available_copies']) ?> / <?= e($b['total_copies']) ?> Available
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

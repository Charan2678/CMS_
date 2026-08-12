<?php
$student        = $selectedStudentProfile ?? null;
$mHistoryData   = $studentMonthlyHistory ?? [];
$history        = $mHistoryData['history'] ?? [];
$taken          = (int)($mHistoryData['monthly_taken'] ?? 0);
$limit          = (int)($mHistoryData['monthly_limit'] ?? 4);
$remaining      = (int)($mHistoryData['monthly_remaining'] ?? 4);
$availableM     = $mHistoryData['available_months'] ?? [];
$currMonthVal   = $selectedMonth ?? date('Y-m');
?>

<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">🎓 Student Monthly Library History &amp; Usage</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Inspect student-wise monthly book borrowing history, 4-book quotas, and circulation analytics</p>
    </div>
    <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
</div>

<!-- Student Search & Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form method="GET" action="/library/reports/students" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; margin: 0;">
        <div style="flex: 2; min-width: 240px;">
            <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Select Student *</label>
            <select name="student_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 8px;">
                <?php foreach ($students as $st): ?>
                    <option value="<?= $st['id'] ?>" <?= (int)$st['id'] === (int)$selectedStudentId ? 'selected' : '' ?>>
                        🎓 <?= e($st['roll_number'] ?? 'STU-' . $st['id']) ?> — <?= e(($st['first_name'] ?? '') . ' ' . ($st['last_name'] ?? '')) ?> (<?= e($st['department_name'] ?? 'CSE') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Academic Month</label>
            <select name="month" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 8px;">
                <?php foreach ($availableM as $m): ?>
                    <option value="<?= e($m['month_key']) ?>" <?= $m['month_key'] === $currMonthVal ? 'selected' : '' ?>>
                        📅 <?= e($m['month_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem; font-weight: 700;">View Student History</button>
    </form>
</div>

<!-- Student Monthly Summary Card -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.35rem; border-left: 4px solid #2563eb;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.75rem; text-transform: uppercase; color: var(--accent-color); font-weight: 700;">STUDENT PROFILE &amp; MONTHLY QUOTA</div>
            <h2 style="font-size: 1.2rem; font-weight: 800; color: var(--text-primary); margin: 0.2rem 0 0.1rem 0;">
                <?= e(($student['first_name'] ?? 'Student') . ' ' . ($student['last_name'] ?? '')) ?>
                <span style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 600;">(Roll #: <?= e($student['roll_number'] ?? '2026-CSE-001') ?>)</span>
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary);">
                Department: <strong><?= e($student['department_name'] ?? 'Computer Science & Engineering') ?></strong> | Target Month: <strong><?= date('F Y', strtotime($currMonthVal . '-01')) ?></strong>
            </div>
        </div>

        <div style="display: flex; gap: 1.25rem; align-items: center; background: var(--bg-main); padding: 0.75rem 1.25rem; border-radius: 12px; border: 1px solid var(--border-color);">
            <div style="text-align: center;">
                <div style="font-size: 0.725rem; color: var(--text-secondary); font-weight: 600;">BOOKS TAKEN THIS MONTH</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: <?= $taken >= 4 ? 'var(--danger)' : '#2563eb' ?>;"><?= $taken ?> / <?= $limit ?></div>
            </div>
            <div style="width: 1px; height: 35px; background: var(--border-color);"></div>
            <div style="text-align: center;">
                <div style="font-size: 0.725rem; color: var(--text-secondary); font-weight: 600;">REMAINING QUOTA</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: <?= $remaining > 0 ? '#10b981' : 'var(--danger)' ?>;"><?= $remaining ?> Books</div>
            </div>
        </div>
    </div>
</div>

<!-- Student Monthly Book Transactions Table -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
        <span>Book Borrowing Log for <?= date('F Y', strtotime($currMonthVal . '-01')) ?></span>
        <span style="font-size: 0.75rem; color: var(--text-secondary);">Quota Limit: 4 Books / Month</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Book Title &amp; Details</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No book transactions recorded for this student in <?= date('F Y', strtotime($currMonthVal . '-01')) ?>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: var(--accent-color);">BK-<?= (int)$h['book_id'] ?></td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($h['book_title']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);"><?= e($h['author'] ?? 'Author') ?></div>
                            </td>
                            <td style="font-weight: 600;"><?= date('d M Y', strtotime($h['issued_date'])) ?></td>
                            <td style="font-weight: 600;"><?= date('d M Y', strtotime($h['due_date'])) ?></td>
                            <td>
                                <?php if (!empty($h['returned_date'])): ?>
                                    <strong style="color: #10b981;"><?= date('d M Y', strtotime($h['returned_date'])) ?></strong>
                                <?php else: ?>
                                    <span style="color: var(--text-secondary);">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($h['status'] === 'returned'): ?>
                                    <span class="badge badge-success">Returned</span>
                                <?php elseif ($h['status'] === 'reserved'): ?>
                                    <span class="badge badge-warning" style="background:#f59e0b; color:#fff;">Reserved</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Issued</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Department Wise Summary -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Department Wise Summary
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Department Code</th>
                    <th>Department Name</th>
                    <th>Active Cardholders</th>
                    <th>Active Books Issued</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deptUsage as $du): ?>
                    <tr>
                        <td><strong style="color: var(--accent-color);"><?= e($du['code']) ?></strong></td>
                        <td style="font-weight: 600; color: var(--text-primary);"><?= e($du['name']) ?></td>
                        <td><?= number_format($du['active_members']) ?> Members</td>
                        <td><span class="badge badge-info"><?= number_format($du['books_issued']) ?> Books</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

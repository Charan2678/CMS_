<?php
$monthVal   = $monthlyData['selected_month'] ?? date('Y-m');
$taken      = (int)($monthlyData['monthly_taken'] ?? 0);
$limit      = (int)($monthlyData['monthly_limit'] ?? 4);
$remaining  = (int)($monthlyData['monthly_remaining'] ?? 4);
$history    = $monthlyData['history'] ?? [];
$monthsList = $monthlyData['available_months'] ?? [];
$isLimitReached = ($taken >= $limit);
?>

<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">📅 Monthly Book History &amp; Quota Log</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">View month-by-month library transactions and monitor your 4-book monthly quota limit</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="/library/catalog" class="btn btn-primary" style="font-size: 0.8125rem;">🔍 Browse Catalog</a>
        <a href="/library/my-books" class="btn btn-secondary" style="font-size: 0.8125rem;">📖 My Issued Books</a>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>

<!-- Monthly Quota Card -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.35rem; border-left: 4px solid <?= $isLimitReached ? 'var(--danger)' : '#2563eb' ?>;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📚</span> Monthly Library Limit Status
            </h2>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0;">
                Each student is allocated a maximum quota of <strong>4 books per calendar month</strong>. Quota automatically resets at the beginning of each month.
            </p>
        </div>
        <div style="display: flex; gap: 1.25rem; align-items: center; background: var(--bg-main); padding: 0.75rem 1.25rem; border-radius: 12px; border: 1px solid var(--border-color);">
            <div style="text-align: center;">
                <div style="font-size: 0.725rem; color: var(--text-secondary); font-weight: 600;">BOOKS TAKEN THIS MONTH</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: <?= $isLimitReached ? 'var(--danger)' : '#2563eb' ?>;"><?= $taken ?> / <?= $limit ?></div>
            </div>
            <div style="width: 1px; height: 35px; background: var(--border-color);"></div>
            <div style="text-align: center;">
                <div style="font-size: 0.725rem; color: var(--text-secondary); font-weight: 600;">REMAINING QUOTA</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: <?= $remaining > 0 ? '#10b981' : 'var(--danger)' ?>;"><?= $remaining ?> Books</div>
            </div>
        </div>
    </div>

    <?php if ($isLimitReached): ?>
        <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; font-size: 0.8125rem; color: var(--danger); font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
            <span>⚠️</span> Monthly Book Limit Reached! You have taken 4 / 4 books for <?= date('F Y', strtotime($monthVal . '-01')) ?>. Further reservations are blocked until next month.
        </div>
    <?php endif; ?>
</div>

<!-- Month Selector Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div style="font-weight: 700; font-size: 0.9375rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
        📅 Select Academic Month:
    </div>
    <form method="GET" action="/library/history" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
        <select name="month" onchange="this.form.submit()" class="form-control" style="padding: 0.4rem 1rem; font-weight: 700; font-size: 0.875rem; border-radius: 8px; border: 1px solid var(--border-color);">
            <?php foreach ($monthsList as $m): ?>
                <option value="<?= e($m['month_key']) ?>" <?= $m['month_key'] === $monthVal ? 'selected' : '' ?>>
                    📅 <?= e($m['month_name']) ?> (<?= (int)$m['total_books'] ?> books)
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<!-- Monthly Book Transactions Table -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
        <span>Book History for <?= date('F Y', strtotime($monthVal . '-01')) ?> (<?= count($history) ?> Records)</span>
        <span style="font-size: 0.75rem; color: var(--text-secondary);">Monthly Transaction Audit Log</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author &amp; Category</th>
                    <th>Issue / Reserve Date</th>
                    <th>Return Date</th>
                    <th>Loan Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2.5rem; color: var(--text-secondary);">
                            No books were borrowed or reserved during <?= date('F Y', strtotime($monthVal . '-01')) ?>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($h['book_title']) ?></td>
                            <td>
                                <div><?= e($h['author'] ?? 'Unknown Author') ?></div>
                                <span class="badge badge-info" style="font-size: 0.65rem; margin-top: 0.2rem;"><?= e($h['category'] ?? 'General') ?></span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= date('d M Y', strtotime($h['issued_date'])) ?></td>
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

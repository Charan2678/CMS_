<?php
$taken     = (int)($summary['monthly_taken'] ?? 0);
$limit     = (int)($summary['monthly_limit'] ?? 4);
$remaining = (int)($summary['monthly_remaining'] ?? 4);
?>

<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">📖 My Issued &amp; Reserved Library Books</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Track your current active book reservations, issued library books, due dates, and return statuses</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
        <a href="/library/catalog" class="btn btn-primary" style="font-size: 0.8125rem;">🔍 Browse Library Catalog</a>
        <a href="/library/history" class="btn btn-secondary" style="font-size: 0.8125rem;">📅 Monthly Book History</a>
    </div>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #2563eb;">
        <div style="font-size: 0.8125rem; color: #2563eb; font-weight: 600;">ACTIVE ISSUED BOOKS</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #2563eb; margin-top: 0.2rem;"><?= (int)($summary['books_issued'] ?? 0) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #f59e0b;">
        <div style="font-size: 0.8125rem; color: #f59e0b; font-weight: 600;">ACTIVE RESERVATIONS</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #d97706; margin-top: 0.2rem;"><?= (int)($summary['books_reserved'] ?? 0) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #8b5cf6;">
        <div style="font-size: 0.8125rem; color: #8b5cf6; font-weight: 600;">TAKEN THIS MONTH</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #8b5cf6; margin-top: 0.2rem;"><?= $taken ?> / <?= $limit ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #10b981;">
        <div style="font-size: 0.8125rem; color: #10b981; font-weight: 600;">MONTHLY QUOTA REMAINING</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #10b981; margin-top: 0.2rem;"><?= $remaining ?> Books</div>
    </div>
</div>

<!-- Issued Books Table -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        My Currently Issued &amp; Reserved Books
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($issuedBooks)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            You have no active book loans or reservations. <a href="/library/catalog">Click here to browse books</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($issuedBooks as $b): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($b['book_title']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($b['author']) ?></td>
                            <td><span class="badge badge-info"><?= e($b['category']) ?></span></td>
                            <td style="font-weight: 600;"><?= date('d M Y', strtotime($b['issued_date'])) ?></td>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= date('d M Y', strtotime($b['due_date'])) ?></td>
                            <td>
                                <?php if ($b['status'] === 'issued'): ?>
                                    <span class="badge badge-success">Issued</span>
                                <?php elseif ($b['status'] === 'reserved'): ?>
                                    <span class="badge badge-warning" style="background:#f59e0b; color:#fff;">Reserved</span>
                                <?php elseif ($b['status'] === 'returned'): ?>
                                    <span class="badge badge-secondary">Returned</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Overdue</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

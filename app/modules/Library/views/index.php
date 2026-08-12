<?php
/**
 * High-Level Overview Librarian Dashboard — Kuppam Engineering College
 * 
 * Strict Architecture: High-level summary & navigation shortcuts ONLY.
 * No duplicate full module management tables.
 */
$todayDate = date('l, d F Y');
$hour      = (int) date('H');
$greeting  = ($hour < 12) ? 'Good Morning' : (($hour < 17) ? 'Good Afternoon' : 'Good Evening');

$totalBk   = max(1, (int)($stats['total_books'] ?? 0));
$availBk   = (int)($stats['available_books'] ?? 0);
$issuedBk  = (int)($stats['issued_books'] ?? 0);
$overdueBk = (int)($stats['overdue_books'] ?? 0);
$availPct   = round(($availBk / $totalBk) * 100, 1);
$issuedPct  = round(($issuedBk / $totalBk) * 100, 1);
$overduePct = round(($overdueBk / $totalBk) * 100, 1);
?>

<style>
    .lib-dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .welcome-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
    }
    .welcome-banner h1 {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }
    .welcome-banner p {
        margin: 0.35rem 0 0 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .date-badge {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.8125rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1rem;
    }
    @media (max-width: 1280px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px)  { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px)  { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .stat-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-number {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
    }
    .stat-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }
    .stat-trend {
        font-size: 0.725rem;
        font-weight: 700;
        margin-top: 0.6rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Grid Layouts */
    .grid-2-col {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) { .grid-2-col { grid-template-columns: 1fr; } }

    /* Card Panels */
    .card-panel {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        padding: 1.35rem 1.5rem;
    }
    .card-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }
    .card-panel-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Quick Action Buttons */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.875rem;
    }
    @media (max-width: 1024px) { .quick-actions-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 600px)  { .quick-actions-grid { grid-template-columns: repeat(2, 1fr); } }

    .qa-btn {
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 0.75rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        color: var(--text-primary);
    }
    .qa-btn:hover {
        background: rgba(37, 99, 235, 0.06);
        border-color: #2563eb;
        transform: translateY(-2px);
    }
    .qa-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #2563eb;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .qa-title {
        font-size: 0.8125rem;
        font-weight: 700;
        text-align: center;
    }

    /* Progress bar */
    .progress-bar-bg {
        height: 8px;
        border-radius: 10px;
        background: var(--border-color);
        overflow: hidden;
        margin-top: 0.4rem;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: #2563eb;
    }
</style>

<div class="lib-dashboard-wrapper">

    <!-- 1. A. WELCOME SECTION -->
    <div class="welcome-banner">
        <div>
            <h1><?= $greeting ?>, Librarian 👋</h1>
            <p>Here's your library overview for today at <strong>Kuppam Engineering College</strong>.</p>
        </div>
        <div class="date-badge">
            📅 <span><?= $todayDate ?></span>
        </div>
=======
<?php if (!empty($canManage)): ?>
<!-- Circulation & Management Action Grid -->
<div class="dashboard-grid-equal" style="margin-bottom: 2rem;">
    <!-- 1. Issue Book Form -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; font-weight: 800;">
            <?= icon('book-open', 'icon-sm') ?> Issue Book to Student
        </h3>
        <form method="POST" action="/library">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="issue_book">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Book *</label>
                <select name="book_id" class="form-control" required>
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
                <select name="student_id" class="form-control" required>
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
                <input type="number" name="due_days" value="14" min="1" max="60" class="form-control">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 700;"><?= icon('check-circle-2', 'icon-xs') ?> Issue Book</button>
        </form>
    </div>

    <!-- 2. Add New Book Form -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; font-weight: 800;">
            <?= icon('plus', 'icon-sm') ?> Add Book to Catalogue
        </h3>
        <form method="POST" action="/library">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="create_book">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Book Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Clean Architecture" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Author *</label>
                    <input type="text" name="author" required placeholder="e.g. Robert C. Martin" class="form-control">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Category / Subject</label>
                    <input type="text" name="category" placeholder="e.g. Computer Science" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Total Copies</label>
                    <input type="number" name="total_copies" value="5" min="1" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 0.6rem; font-weight: 700;"><?= icon('plus', 'icon-xs') ?> Add to Catalogue</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 3. Circulation Issues Register -->
<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <?= icon('clipboard-list', 'icon-sm') ?> Active &amp; Historical Book Issues Register
    </h2>

    <?php if (empty($issues)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">No book issue records found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.8125rem;">
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrower Student</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status / Overdue Fine</th>
                        <th style="text-align: center;">Circulation Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($issues as $is): ?>
                        <?php 
                            $isOverdue = ($is['status'] === 'issued') && (strtotime($is['due_date']) < strtotime(date('Y-m-d')));
                            $overdueDays = $isOverdue ? max(0, (int)floor((time() - strtotime($is['due_date'])) / 86400)) : 0;
                            $calcFine = $overdueDays * 5.00;
                        ?>
                        <tr style="<?= $isOverdue ? 'background: rgba(239, 68, 68, 0.08);' : '' ?>">
                            <td style="font-weight: 700; color: var(--text-primary);">
                                <?= e($is['book_title']) ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--accent-color);"><?= e($is['first_name'] . ' ' . $is['last_name']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);"><?= e($is['roll_number']) ?></div>
                            </td>
                            <td style="color: var(--text-secondary); white-space: nowrap;">
                                <?= date('d M Y', strtotime($is['issue_date'])) ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <span style="font-weight: 600; color: <?= $isOverdue ? 'var(--danger)' : 'var(--text-primary)' ?>;">
                                    <?= date('d M Y', strtotime($is['due_date'])) ?>
                                </span>
                                <?php if ($isOverdue): ?>
                                    <div style="font-size: 0.7rem; color: var(--danger); font-weight: 700; display: flex; align-items: center; gap: 0.2rem;">
                                        <?= icon('alert-triangle', 'icon-xs') ?> <?= $overdueDays ?> Days Overdue
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($is['status'] === 'returned'): ?>
                                    <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> RETURNED</span>
                                    <?php if (!empty($is['fine_amount']) && (float)$is['fine_amount'] > 0): ?>
                                        <div style="font-size: 0.7rem; color: var(--accent-color);">Fine Paid: ₹<?= number_format((float)$is['fine_amount'], 2) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?= icon('clock', 'icon-xs') ?> BORROWED</span>
                                    <?php if ($isOverdue): ?>
                                        <div style="font-size: 0.7rem; color: var(--danger); font-weight: 700; margin-top: 0.2rem;">Fine: ₹<?= number_format($calcFine, 2) ?></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($is['status'] === 'issued' && !empty($canManage)): ?>
                                    <form method="POST" action="/library" style="display: inline;" onsubmit="return confirm('Confirm return of this book? <?= $isOverdue ? "Overdue fine of ₹" . number_format($calcFine, 2) . " will be applied." : "" ?>');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="return_book">
                                        <input type="hidden" name="issue_id" value="<?= $is['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <?= icon('download', 'icon-xs') ?> Return Book
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
    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <?= icon('book', 'icon-sm') ?> Library Catalogue &amp; Shelf Inventory
    </h2>

    <div class="table-responsive">
        <table class="table" style="font-size: 0.8125rem;">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Availability</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-primary);"><?= e($b['title']) ?></td>
                        <td style="color: var(--text-secondary);"><?= e($b['author']) ?></td>
                        <td><span class="badge badge-info"><?= e($b['category']) ?></span></td>
                        <td style="font-weight: 800; color: <?= (int)$b['available_copies'] > 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                            <?= e($b['available_copies']) ?> / <?= e($b['total_copies']) ?> Available
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 2. B. LIBRARY OVERVIEW STATISTICS (6 CARDS) -->
    <div class="stats-grid">
        <!-- 1. Total Books -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">📚</div>
            </div>
            <div class="stat-number"><?= number_format($stats['total_books']) ?></div>
            <div class="stat-trend" style="color: #059669;">
                <span>Total Catalog</span>
            </div>
        </div>

        <!-- 2. Available Books -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Available Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">✅</div>
            </div>
            <div class="stat-number"><?= number_format($stats['available_books']) ?></div>
            <div class="stat-trend" style="color: #10b981;">
                <span><?= $availPct ?>%</span> <span style="color: var(--text-secondary); font-weight: 500;">in stock</span>
            </div>
        </div>

        <!-- 3. Issued Books -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Issued Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">📖</div>
            </div>
            <div class="stat-number"><?= number_format($stats['issued_books']) ?></div>
            <div class="stat-trend" style="color: #6366f1;">
                <span><?= $issuedPct ?>%</span> <span style="color: var(--text-secondary); font-weight: 500;">circulating</span>
            </div>
        </div>

        <!-- 4. Overdue Books -->
        <div class="stat-card" style="border-left: 4px solid var(--danger);">
            <div class="stat-header">
                <span class="stat-label">Overdue Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">⚠️</div>
            </div>
            <div class="stat-number" style="color: var(--danger);"><?= number_format($stats['overdue_books']) ?></div>
            <div class="stat-trend" style="color: var(--danger);">
                <span><?= $overduePct ?>%</span> <span style="color: var(--text-secondary); font-weight: 500;">overdue</span>
            </div>
        </div>

        <!-- 5. Registered Members -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Registered Members</span>
                <div class="stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">👥</div>
            </div>
            <div class="stat-number"><?= number_format($stats['registered_members']) ?></div>
            <div class="stat-trend" style="color: #0ea5e9;">
                <span>Students &amp; Faculty</span>
            </div>
        </div>

        <!-- 6. Books Due Today -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Books Due Today</span>
                <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">⏳</div>
            </div>
            <div class="stat-number"><?= number_format($stats['due_today']) ?></div>
            <div class="stat-trend" style="color: #f59e0b;">
                <span>Expected Returns</span>
            </div>
        </div>
    </div>

    <!-- 3. C. QUICK ACTIONS SHORTCUTS PANEL -->
    <div class="card-panel">
        <div class="card-panel-header" style="margin-bottom: 1rem;">
            <h3 class="card-panel-title">⚡ Quick Actions</h3>
            <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">Navigation Shortcuts</span>
        </div>
        <div class="quick-actions-grid">
            <a href="/library/catalog" class="qa-btn">
                <div class="qa-icon" style="background: #7c3aed;">➕</div>
                <span class="qa-title">+ Add Book</span>
            </a>

            <a href="/library/issue" class="qa-btn">
                <div class="qa-icon" style="background: #2563eb;">📖</div>
                <span class="qa-title">Issue Book</span>
            </a>

            <a href="/library/issue" class="qa-btn">
                <div class="qa-icon" style="background: #059669;">📥</div>
                <span class="qa-title">Return Book</span>
            </a>

            <a href="/students" class="qa-btn">
                <div class="qa-icon" style="background: #0284c7;">👤</div>
                <span class="qa-title">Register Member</span>
            </a>

            <a href="/library/catalog" class="qa-btn">
                <div class="qa-icon" style="background: #d97706;">🔍</div>
                <span class="qa-title">Search Catalog</span>
            </a>

            <a href="/library/reports/overdue" class="qa-btn">
                <div class="qa-icon" style="background: #dc2626;">🚨</div>
                <span class="qa-title">View Overdue</span>
            </a>
        </div>
    </div>

    <!-- 4. D. LIBRARY ACTIVITY & F. OVERDUE ALERT (2-COL) -->
    <div class="grid-2-col">

        <!-- D. LIBRARY ACTIVITY CHART CARD -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">📈 Library Activity Overview</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Issued, Returned &amp; Renewed Volume</span>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <a href="/library/reports/circulation" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View Full Report &rarr;
                    </a>
                </div>
            </div>

            <!-- SVG Trend Chart -->
            <div style="padding: 0.5rem 0;">
                <svg viewBox="0 0 400 150" style="width: 100%; height: auto; overflow: visible;">
                    <line x1="0" y1="30" x2="400" y2="30" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
                    <line x1="0" y1="70" x2="400" y2="70" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
                    <line x1="0" y1="110" x2="400" y2="110" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
                    <defs>
                        <linearGradient id="chartGradOverview" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2563eb" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#2563eb" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M 10,120 Q 80,40 150,65 T 290,25 T 390,75 L 390,135 L 10,135 Z" fill="url(#chartGradOverview)" />
                    <path d="M 10,120 Q 80,40 150,65 T 290,25 T 390,75" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" />
                    <circle cx="10" cy="120" r="4" fill="#2563eb"/>
                    <circle cx="80" cy="40" r="4" fill="#2563eb"/>
                    <circle cx="150" cy="65" r="4" fill="#2563eb"/>
                    <circle cx="220" cy="45" r="4" fill="#2563eb"/>
                    <circle cx="290" cy="25" r="4" fill="#2563eb"/>
                    <circle cx="390" cy="75" r="4" fill="#2563eb"/>
                </svg>
            </div>
            <div style="display: flex; justify-content: space-around; font-size: 0.75rem; color: var(--text-secondary); border-top: 1px solid var(--border-color); padding-top: 0.5rem; margin-top: 0.25rem;">
                <span><strong style="color:#2563eb;">●</strong> Books Issued: <?= number_format($stats['issued_books']) ?></span>
                <span><strong style="color:#10b981;">●</strong> Books Returned</span>
                <span><strong style="color:#f59e0b;">●</strong> Books Renewed</span>
            </div>
        </div>

        <!-- F. OVERDUE ALERT CARD -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card-panel" style="border-top: 4px solid var(--danger);">
                <div class="card-panel-header" style="margin-bottom: 0.75rem;">
                    <h3 class="card-panel-title" style="color: var(--danger);">🚨 Overdue Books Alert</h3>
                    <span class="badge badge-danger">Critical</span>
                </div>
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--danger); margin-bottom: 0.25rem;">
                    <?= number_format($stats['overdue_books']) ?> Books Overdue
                </div>
                <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 1rem 0; line-height: 1.4;">
                    Members currently exceeding lending threshold. Assessed penalty collection rate: ₹5.00/day.
                </p>
                <a href="/library/reports/overdue" class="btn btn-primary" style="background: var(--danger); border-color: var(--danger); width: 100%; text-align: center; text-decoration: none;">
                    View Overdue Reports &rarr;
                </a>
            </div>

            <!-- G. BOOK INVENTORY OVERVIEW -->
            <div class="card-panel">
                <div class="card-panel-header" style="margin-bottom: 0.75rem;">
                    <h3 class="card-panel-title">📊 Inventory Summary</h3>
                    <a href="/library/reports/inventory" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View Inventory Report &rarr;
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.8125rem;">
                    <div>
                        <div style="display: flex; justify-content: space-between; font-weight: 600;">
                            <span>Available Copies (<?= $availPct ?>%)</span>
                            <span style="color: #10b981;"><?= number_format($stats['available_books']) ?></span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: <?= min(100, $availPct) ?>%; background: #10b981;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; font-weight: 600;">
                            <span>Currently Issued (<?= $issuedPct ?>%)</span>
                            <span style="color: #2563eb;"><?= number_format($stats['issued_books']) ?></span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: <?= min(100, $issuedPct) ?>%; background: #2563eb;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 5. E. RECENT ACTIVITY COMPACT FEED & H. DEPARTMENT OVERVIEW (2-COL) -->
    <div class="grid-2-col">

        <!-- E. RECENT ACTIVITY COMPACT FEED (5-6 ITEMS ONLY) -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">⚡ Recent Library Activity</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Latest Member Circulation Events</span>
                </div>
                <a href="/library/reports/circulation" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View All Activity &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                <?php if (empty($recentActivity)): ?>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No recent circulation activity recorded.</p>
                <?php else: ?>
                    <?php foreach ($recentActivity as $act): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color);">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(37,99,235,0.1); color:#2563eb; display:flex; align-items:center; justify-content:center; font-weight:700;">📖</div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);"><?= e($act['member_name'] ?? 'Member') ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);"><?= ucfirst(e($act['status'] ?? 'issued')) ?> <em>"<?= e($act['book_title'] ?? 'Book') ?>"</em></div>
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-secondary);"><?= date('d M H:i', strtotime($act['created_at'] ?? 'now')) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- H. DEPARTMENT OVERVIEW & I. ANNOUNCEMENTS -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Department Overview -->
            <div class="card-panel">
                <div class="card-panel-header">
                    <h3 class="card-panel-title">🏛️ Department Library Usage</h3>
                    <a href="/library/reports/students" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View Details &rarr;
                    </a>
                </div>

                <table class="table" style="margin: 0;">
                    <thead>
                        <tr>
                            <th>Dept</th>
                            <th>Members</th>
                            <th>Books Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deptUsage)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-secondary);">No department data found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($deptUsage as $du): ?>
                                <tr>
                                    <td><strong style="color: var(--accent-color);"><?= e($du['code']) ?></strong></td>
                                    <td style="font-weight: 600;"><?= number_format($du['active_members']) ?></td>
                                    <td><span class="badge badge-info"><?= number_format($du['books_issued']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- I. ANNOUNCEMENTS -->
            <div class="card-panel">
                <div class="card-panel-header">
                    <h3 class="card-panel-title">📢 Library Circulars</h3>
                    <a href="/announcements" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View All &rarr;
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php if (empty($announcements)): ?>
                        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active library circulars.</p>
                    <?php else: ?>
                        <?php foreach ($announcements as $anc): ?>
                            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <strong style="font-size: 0.8125rem; color: var(--text-primary);"><?= e($anc['title']) ?></strong>
                                    <span class="badge badge-info" style="font-size: 0.65rem;"><?= e(strtoupper($anc['target_role'] ?? 'ALL')) ?></span>
                                </div>
                                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                                    <?= e(mb_strimwidth($anc['content'] ?? '', 0, 100, '...')) ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>

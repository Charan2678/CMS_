<?php
$taken     = (int)($summary['monthly_taken'] ?? 0);
$limit     = (int)($summary['monthly_limit'] ?? 4);
$remaining = (int)($summary['monthly_remaining'] ?? 4);
$isLimitReached = ($taken >= $limit);
?>

<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">📚 Library Catalog Repository</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Search books, view live copy availability, and reserve books for collection</p>
    </div>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
        <?php if (empty($canManage)): ?>
            <a href="/library/my-books" class="btn btn-primary" style="font-size: 0.8125rem;">📖 My Issued &amp; Reserved Books</a>
            <a href="/library/history" class="btn btn-secondary" style="font-size: 0.8125rem;">📅 Monthly Book History</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<?php if (empty($canManage)): ?>
    <!-- Monthly Library Limit Card for Student -->
    <div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem; border-left: 4px solid <?= $isLimitReached ? 'var(--danger)' : '#2563eb' ?>;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📚</span> Monthly Library Limit
                </h3>
                <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                    Students can take a maximum of <strong>4 books per month</strong>. Quota resets automatically at the start of each month.
                </p>
            </div>
            <div style="display: flex; gap: 1.5rem; align-items: center; background: var(--bg-main); padding: 0.6rem 1.25rem; border-radius: 10px; border: 1px solid var(--border-color);">
                <div style="text-align: center;">
                    <div style="font-size: 0.725rem; color: var(--text-secondary); font-weight: 600;">BOOKS TAKEN</div>
                    <div style="font-size: 1.35rem; font-weight: 800; color: <?= $isLimitReached ? 'var(--danger)' : '#2563eb' ?>;"><?= $taken ?> / <?= $limit ?></div>
                </div>
                <div style="width: 1px; height: 30px; background: var(--border-color);"></div>
                <div style="text-align: center;">
                    <div style="font-size: 0.725rem; color: var(--text-secondary); font-weight: 600;">REMAINING</div>
                    <div style="font-size: 1.35rem; font-weight: 800; color: <?= $remaining > 0 ? '#10b981' : 'var(--danger)' ?>;"><?= $remaining ?> Books</div>
                </div>
            </div>
        </div>
        <?php if ($isLimitReached): ?>
            <div style="margin-top: 0.875rem; padding: 0.6rem 0.875rem; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 6px; font-size: 0.8125rem; color: var(--danger); font-weight: 700;">
                ⚠️ Monthly Book Limit Reached (4 / 4). You cannot reserve another book during this month.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!empty($reservationInfo)): ?>
    <!-- Reservation Confirmation Banner -->
    <div class="card" style="margin-bottom: 1.5rem; border-left: 4px solid #10b981; background: rgba(16, 185, 129, 0.05); padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
            <strong style="font-size: 1rem; color: #059669;">🎉 Book Reservation Confirmed!</strong>
            <span class="badge badge-success">Status: Reserved</span>
        </div>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; font-size: 0.8125rem;">
            <div>
                <span style="color: var(--text-secondary); display: block;">Booking Date:</span>
                <strong><?= date('d M Y') ?></strong>
            </div>
            <div>
                <span style="color: var(--text-secondary); display: block;">Expected Collection:</span>
                <strong><?= date('d M Y', strtotime($reservationInfo['expected_date'] ?? '+14 days')) ?></strong>
            </div>
            <div>
                <span style="color: var(--text-secondary); display: block;">Reservation ID:</span>
                <strong style="font-family: monospace;">RES-<?= e($reservationInfo['issue_id'] ?? 1) ?></strong>
            </div>
            <div>
                <span style="color: var(--text-secondary); display: block;">Collection Desk:</span>
                <strong>KEC Central Library Counter</strong>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Search Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1rem;">
    <form method="GET" action="/library/catalog" style="display: flex; gap: 0.75rem; margin: 0;">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="🔍 Search library books by title, author, or category..." class="form-control" style="flex: 1; padding: 0.5rem 0.875rem; border: 1px solid var(--border-color); border-radius: 8px;">
        <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">Search Catalog</button>
        <?php if (!empty($search)): ?>
            <a href="/library/catalog" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div style="display: grid; grid-template-columns: <?= !empty($canManage) ? '2fr 1fr' : '1fr' ?>; gap: 1.5rem;">
    <!-- Catalog Table / Cards -->
    <div class="card">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
            <span>All Cataloged Books (<?= count($books) ?> Titles)</span>
            <span style="font-size: 0.75rem; color: var(--text-secondary);">Showing real-time inventory</span>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Book Title &amp; Details</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Available Copies</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($books)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                No books match your search query.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($books as $b): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem;"><?= e($b['title']) ?></div>
                                    <?php if (!empty($b['isbn'])): ?>
                                        <div style="font-size: 0.725rem; color: var(--text-secondary); font-family: monospace;">ISBN: <?= e($b['isbn']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--text-secondary); font-weight: 600;"><?= e($b['author'] ?? 'Unknown Author') ?></td>
                                <td><span class="badge badge-info"><?= e($b['category'] ?? 'General') ?></span></td>
                                <td>
                                    <span style="font-weight: 800; color: <?= (int)$b['available_copies'] > 0 ? '#10b981' : 'var(--danger)' ?>;">
                                        <?= (int)$b['available_copies'] ?> / <?= (int)($b['total_copies'] ?? 1) ?> Copies
                                    </span>
                                </td>
                                <td>
                                    <?php if ($isLimitReached): ?>
                                        <button disabled class="btn btn-sm btn-secondary" style="opacity: 0.65; cursor: not-allowed;" title="Monthly limit of 4 books reached">
                                            Limit Reached (4/4)
                                        </button>
                                    <?php elseif ((int)$b['available_copies'] > 0): ?>
                                        <form method="POST" action="/library/catalog" style="margin: 0;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_action" value="reserve_book">
                                            <input type="hidden" name="book_id" value="<?= $b['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-primary" style="font-weight: 700;">
                                                📖 Reserve Book
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button disabled class="btn btn-sm btn-secondary" style="opacity: 0.65; cursor: not-allowed;">
                                            Out of Stock
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($canManage)): ?>
        <!-- Create Book Form (Librarian Only) -->
        <div class="card">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                ➕ Add New Book to Catalog
            </div>
            <form method="POST" action="/library/catalog" style="padding: 1.25rem;">
                <?= csrf_field() ?>
                <input type="hidden" name="_action" value="add_book">

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Book Title *</label>
                    <input type="text" name="title" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Author</label>
                    <input type="text" name="author" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">ISBN</label>
                    <input type="text" name="isbn" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Category</label>
                    <select name="category" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                        <option value="Programming">Programming</option>
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Algorithms">Algorithms</option>
                        <option value="Database Management">Database Management</option>
                        <option value="Electronics">Electronics</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Total Copies</label>
                    <input type="number" name="total_copies" value="5" min="1" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add to Inventory</button>
            </form>
        </div>
    <?php endif; ?>
</div>

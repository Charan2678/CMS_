<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">📊 Inventory Stock &amp; Audit Report</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Physical inventory stock breakdown, available copies, and total title count</p>
    </div>
    <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
</div>

<div class="card" style="margin-bottom: 1.5rem;">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Stock Summary
    </div>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; padding: 1.25rem;">
        <div style="background: var(--bg-main); padding: 1rem; border-radius: 8px; text-align: center;">
            <div style="font-size: 0.8125rem; color: var(--text-secondary);">Total Inventory Copies</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);"><?= number_format($stats['total_books']) ?></div>
        </div>
        <div style="background: var(--bg-main); padding: 1rem; border-radius: 8px; text-align: center;">
            <div style="font-size: 0.8125rem; color: var(--text-secondary);">Available on Shelves</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #10b981;"><?= number_format($stats['available_books']) ?></div>
        </div>
        <div style="background: var(--bg-main); padding: 1rem; border-radius: 8px; text-align: center;">
            <div style="font-size: 0.8125rem; color: var(--text-secondary);">Currently Issued</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #2563eb;"><?= number_format($stats['issued_books']) ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Title-wise Stock Inventory (<?= count($books) ?> Titles)
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Total Copies</th>
                    <th>Available Copies</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-primary);"><?= e($b['title']) ?></td>
                        <td><?= e($b['author']) ?></td>
                        <td><span class="badge badge-info"><?= e($b['category']) ?></span></td>
                        <td style="font-weight: 600;"><?= (int)$b['total_copies'] ?></td>
                        <td style="font-weight: 700; color: <?= (int)$b['available_copies'] > 0 ? '#10b981' : 'var(--danger)' ?>;"><?= (int)$b['available_copies'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

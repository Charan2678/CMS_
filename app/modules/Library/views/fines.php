<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">💰 My Library Fees &amp; Overdue Fines</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">View late book return penalties, payment statuses, and settle outstanding library dues</p>
    </div>
    <a href="/library/my-books" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to My Books</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>

<!-- Fines Table -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Library Fine Breakdown &amp; Payment History
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Due Date</th>
                    <th>Late Days</th>
                    <th>Fine Amount</th>
                    <th>Payment Status</th>
                    <th>Action / Receipt</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($fines)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #10b981; font-weight: 600;">
                            ✨ No outstanding library fines! All dues are clear.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($fines as $fn): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($fn['book_title']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($fn['author']) ?></td>
                            <td><?= date('d M Y', strtotime($fn['due_date'])) ?></td>
                            <td style="font-weight: 700; color: var(--danger);"><?= (int)$fn['late_days'] ?> Days</td>
                            <td style="font-weight: 800; font-size: 1.05rem;">₹<?= number_format((float)$fn['fine_amount'], 2) ?></td>
                            <td>
                                <?php if (($fn['payment_status'] ?? '') === 'paid' || (int)($fn['fine_paid'] ?? 0) === 1): ?>
                                    <span class="badge badge-success">PAID</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (($fn['payment_status'] ?? '') === 'paid' || (int)($fn['fine_paid'] ?? 0) === 1): ?>
                                    <span style="font-family: monospace; font-size: 0.75rem; color: var(--accent-color); font-weight: 700;">
                                        <?= e($fn['transaction_id'] ?? 'TXN-984210') ?>
                                    </span>
                                <?php else: ?>
                                    <form method="POST" action="/library/fines" style="margin: 0;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="issue_id" value="<?= $fn['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-primary" style="font-weight: 700;">Pay Fine 💳</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

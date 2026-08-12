<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>💳</span> Fee Categories &amp; Master Setup
        </h1>
        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0.25rem 0 0 0;">
            Define fee component categories (Tuition, Exam, Library, Hostel, Transport, Special Fee).
        </p>
    </div>
    <div>
        <a href="/fee/payments" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); transition: all 0.2s ease;">
            <span>←</span> Back to Fee &amp; Financial Ledger
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Create Fee Category Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add Fee Category
        </h2>

        <form method="POST" action="/fee/categories">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="name">Category Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Tuition Fee">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="code">Category Short Code</label>
                <input type="text" id="code" name="code" class="form-control" placeholder="e.g. tuition_fee">
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                <input type="checkbox" id="is_refundable" name="is_refundable" value="1">
                <label for="is_refundable" style="font-size: 0.875rem; color: var(--text-secondary); cursor: pointer;">Refundable Caution Deposit</label>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Create Fee Category</button>
        </form>
    </div>

    <!-- Fee Categories List Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>🏷️</span> Fee Head Categories Directory
        </h2>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Code</th>
                        <th>Category Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No fee categories configured yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($cat['name']) ?></td>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($cat['code']) ?></td>
                                <td>
                                    <?php if ((int)$cat['is_refundable'] === 1): ?>
                                        <span class="badge badge-warning">Refundable Deposit</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Standard Non-Refundable Fee</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-success">Active</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="page-split">
    <!-- Create Fee Category Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('plus', 'icon-sm') ?> Add Fee Category
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

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <input type="checkbox" id="is_refundable" name="is_refundable" value="1" style="cursor: pointer;">
                <label for="is_refundable" style="font-size: 0.875rem; color: var(--text-primary); cursor: pointer; font-weight: 600;">Refundable Caution Deposit</label>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;"><?= icon('plus', 'icon-xs') ?> Create Fee Category</button>
        </form>
    </div>

    <!-- Fee Categories List Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('tag', 'icon-sm') ?> Fee Head Categories Directory
        </h2>

        <div class="table-responsive">
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
                                    <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> Active</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

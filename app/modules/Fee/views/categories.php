<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Form Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Add Fee Category</h2>
        </div>

        <form method="POST" action="/fee/categories">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Category Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Tuition Fee">
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Category Code</label>
                <input type="text" id="code" name="code" class="form-control" placeholder="e.g. tuition_fee">
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" id="is_refundable" name="is_refundable" value="1">
                <label for="is_refundable" style="font-size: 0.875rem; color: var(--text-secondary); cursor: pointer;">Refundable Deposit</label>
            </div>

            <button type="submit" class="btn-primary">Create Fee Category</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Fee Categories List</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Category Name</th>
                    <th style="padding: 0.75rem;">Code</th>
                    <th style="padding: 0.75rem;">Type</th>
                    <th style="padding: 0.75rem;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="4" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No fee categories configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 600;"><?= e($cat['name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($cat['code']) ?></td>
                            <td style="padding: 0.75rem;">
                                <?php if ((int)$cat['is_refundable'] === 1): ?>
                                    <span class="badge badge-warning">Refundable</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Standard Fee</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <span class="badge badge-success">Active</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

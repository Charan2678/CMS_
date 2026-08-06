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
            <h2 class="panel-title">Add Academic Year</h2>
        </div>

        <form method="POST" action="/master/academic-years">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Academic Year Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. 2026-2027">
            </div>

            <div class="form-group">
                <label class="form-label" for="start_date">Start Date *</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="end_date">End Date *</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
                <input type="checkbox" id="is_current" name="is_current" value="1">
                <label for="is_current" style="font-size: 0.875rem; color: var(--text-secondary); cursor: pointer;">Set as Active Academic Year</label>
            </div>

            <button type="submit" class="btn-primary">Create Academic Year</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Academic Years List</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Name</th>
                    <th style="padding: 0.75rem;">Duration</th>
                    <th style="padding: 0.75rem;">Status</th>
                    <th style="padding: 0.75rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($academicYears)): ?>
                    <tr>
                        <td colspan="4" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No academic years configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($academicYears as $ay): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 600;"><?= e($ay['name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($ay['start_date']) ?> to <?= e($ay['end_date']) ?></td>
                            <td style="padding: 0.75rem;">
                                <?php if ((int)$ay['is_current'] === 1): ?>
                                    <span class="badge badge-success">Active Year</span>
                                <?php else: ?>
                                    <span class="badge badge-info">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ((int)$ay['is_current'] !== 1): ?>
                                    <form method="POST" action="/master/academic-years" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="set_current">
                                        <input type="hidden" name="academic_year_id" value="<?= $ay['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: #a5b4fc; cursor: pointer; font-size: 0.8125rem; text-decoration: underline;">Set Active</button>
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

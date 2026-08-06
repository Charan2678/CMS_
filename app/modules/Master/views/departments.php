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
            <h2 class="panel-title">Add Department</h2>
        </div>

        <form method="POST" action="/master/departments">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Department Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Computer Science & Engineering">
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Department Code *</label>
                <input type="text" id="code" name="code" class="form-control" required placeholder="e.g. CSE">
            </div>

            <div class="form-group">
                <label class="form-label" for="established_year">Established Year</label>
                <input type="number" id="established_year" name="established_year" class="form-control" min="1900" max="2100" placeholder="e.g. 2010">
            </div>

            <button type="submit" class="btn-primary">Create Department</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Department List</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Code</th>
                    <th style="padding: 0.75rem;">Department Name</th>
                    <th style="padding: 0.75rem;">Established</th>
                    <th style="padding: 0.75rem;">HOD</th>
                    <th style="padding: 0.75rem;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($departments)): ?>
                    <tr>
                        <td colspan="5" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No departments created yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($departments as $dept): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($dept['code']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 500;"><?= e($dept['name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($dept['established_year'] ?? 'N/A') ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);">
                                <?= !empty($dept['hod_first_name']) ? e($dept['hod_first_name'] . ' ' . $dept['hod_last_name']) : 'Not Assigned' ?>
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

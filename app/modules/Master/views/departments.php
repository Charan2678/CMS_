<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; width: 100%;">
    <!-- Create Department Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add New Department
        </h2>

        <form method="POST" action="/master/departments">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="name">Department Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Computer Science & Engineering">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="code">Department Short Code *</label>
                <input type="text" id="code" name="code" class="form-control" required placeholder="e.g. CSE">
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" for="established_year">Established Year</label>
                <input type="number" id="established_year" name="established_year" class="form-control" min="1900" max="2100" placeholder="e.g. 2010">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Create Department</button>
        </form>
    </div>

    <!-- Departments Directory Table Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>🏢</span> Academic Departments Directory
        </h2>

        <div style="overflow-x: auto; width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Department Name</th>
                        <th>Est. Year</th>
                        <th>Head of Dept (HOD)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($departments)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No departments created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($departments as $dept): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($dept['code']) ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($dept['name']) ?></td>
                                <td style="color: var(--text-secondary);"><?= e($dept['established_year'] ?? 'N/A') ?></td>
                                <td style="color: var(--text-secondary);">
                                    <?= !empty($dept['hod_first_name']) ? e($dept['hod_first_name'] . ' ' . $dept['hod_last_name']) : '<span style="opacity: 0.6;">Unassigned</span>' ?>
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

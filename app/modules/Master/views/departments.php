<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<!-- Page Header & Title Banner -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.625rem;">
            <span>🏢</span> Academic Departments Directory
        </h1>
        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
            Manage college departments, short codes, established dates, and assigned Heads of Department (HOD)
        </div>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <span class="badge badge-info" style="font-size: 0.8125rem; padding: 0.5rem 1rem;">Total Departments: <?= count($departments) ?></span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 380px 1fr; gap: 1.75rem; width: 100%;">
    <!-- Form Card -->
    <div class="card" style="height: fit-content;">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
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

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label" for="established_year">Established Year</label>
                <input type="number" id="established_year" name="established_year" class="form-control" min="1900" max="2100" placeholder="e.g. 2001">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.875rem 1.5rem;">Create Department</button>
        </form>
    </div>

    <!-- Directory Card -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📑</span> Academic Departments Directory
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
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No departments created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($departments as $dept): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color); font-size: 0.9375rem;"><?= e($dept['code']) ?></td>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($dept['name']) ?></td>
                                <td style="color: var(--text-secondary); font-weight: 500;"><?= e($dept['established_year'] ?? 'N/A') ?></td>
                                <td style="color: var(--text-secondary); font-weight: 500;">
                                    <?= !empty($dept['hod_first_name']) ? '<strong style="color: var(--text-primary);">' . e($dept['hod_first_name'] . ' ' . $dept['hod_last_name']) . '</strong>' : '<span style="opacity: 0.6;">Unassigned</span>' ?>
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

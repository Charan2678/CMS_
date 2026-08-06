<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Faculty Directory</h2>
        <div>
            <a href="/faculty/designations" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); margin-right: 0.5rem;">Designations</a>
            <a href="/faculty/assign-subject" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1rem; background: var(--bg-surface); border: 1px solid var(--border-color); margin-right: 0.5rem;">Assign Subjects</a>
            <a href="/faculty/create" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1.25rem;">+ Onboard Faculty</a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="/faculty" style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
        <div>
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Name, Employee ID, Email..." value="<?= e($filters['search']) ?>">
        </div>

        <div>
            <label class="form-label">Department</label>
            <select name="department_id" class="form-control">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $filters['department_id'] == $d['id'] ? 'selected' : '' ?>><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Filter</button>
        </div>
    </form>

    <!-- Faculty Table -->
    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.75rem;">Emp ID</th>
                <th style="padding: 0.75rem;">Faculty Name</th>
                <th style="padding: 0.75rem;">Designation</th>
                <th style="padding: 0.75rem;">Department</th>
                <th style="padding: 0.75rem;">Qualification</th>
                <th style="padding: 0.75rem;">Status</th>
                <th style="padding: 0.75rem;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($facultyList)): ?>
                <tr>
                    <td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No faculty members found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($facultyList as $fac): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($fac['employee_id']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 600;"><?= e($fac['first_name'] . ' ' . $fac['last_name']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($fac['designation_name']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($fac['department_code']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($fac['qualification'] ?? 'N/A') ?></td>
                        <td style="padding: 0.75rem;">
                            <span class="badge badge-success">Active</span>
                        </td>
                        <td style="padding: 0.75rem;">
                            <a href="/faculty/<?= $fac['id'] ?>" style="color: #a5b4fc; text-decoration: none; font-weight: 500;">View Profile →</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>👨‍🏫</span> Faculty Members Directory
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Academic teaching staff, designations, and department workloads
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="/faculty/designations" class="btn-primary" style="text-decoration: none; padding: 0.5rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary);">Designations</a>
            <a href="/faculty/assign-subject" class="btn-primary" style="text-decoration: none; padding: 0.5rem 1rem; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary);">Assign Subjects</a>
            <a href="/faculty/create" class="btn-primary" style="text-decoration: none; padding: 0.5rem 1.25rem; font-weight: 700;">+ Onboard Faculty</a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="/faculty" class="filter-bar">
        <div>
            <label class="form-label">Search Query</label>
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
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Apply Filters</button>
        </div>
    </form>

    <!-- Faculty Table -->
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Emp ID</th>
                    <th>Faculty Name</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Qualification</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($facultyList)): ?>
                    <tr>
                        <td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No faculty members found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($facultyList as $fac): ?>
                        <tr>
                            <td style="font-weight: 800; color: var(--accent-color);"><?= e($fac['employee_id']) ?></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($fac['first_name'] . ' ' . $fac['last_name']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($fac['designation_name']) ?></td>
                            <td style="color: var(--text-secondary); font-weight: 600;"><?= e($fac['department_code']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($fac['qualification'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge badge-success">Active</span>
                            </td>
                            <td>
                                <a href="/faculty/<?= $fac['id'] ?>" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; border-radius: 6px; text-decoration: none; display: inline-block;">View Profile &rarr;</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

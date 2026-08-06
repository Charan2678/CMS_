<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Non-Faculty Staff Directory</h2>
        <a href="/staff/create" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1.25rem;">+ Onboard Staff</a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/staff" style="margin-bottom: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end;">
        <div>
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control" placeholder="Name, Staff ID, Email..." value="<?= e($filters['search']) ?>">
        </div>

        <div>
            <label class="form-label">Department Type</label>
            <select name="department_type" class="form-control">
                <option value="">All Department Types</option>
                <option value="accounts" <?= $filters['department_type'] === 'accounts' ? 'selected' : '' ?>>Accounts & Finance</option>
                <option value="library" <?= $filters['department_type'] === 'library' ? 'selected' : '' ?>>Library Services</option>
                <option value="hostel" <?= $filters['department_type'] === 'hostel' ? 'selected' : '' ?>>Hostel Administration</option>
                <option value="transport" <?= $filters['department_type'] === 'transport' ? 'selected' : '' ?>>Transport & Logistics</option>
                <option value="canteen" <?= $filters['department_type'] === 'canteen' ? 'selected' : '' ?>>Canteen Services</option>
                <option value="admin" <?= $filters['department_type'] === 'admin' ? 'selected' : '' ?>>General Administration</option>
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

    <!-- Staff Table -->
    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.75rem;">Staff ID</th>
                <th style="padding: 0.75rem;">Staff Name</th>
                <th style="padding: 0.75rem;">Department Domain</th>
                <th style="padding: 0.75rem;">Designation</th>
                <th style="padding: 0.75rem;">Mobile</th>
                <th style="padding: 0.75rem;">Status</th>
                <th style="padding: 0.75rem;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($staffList)): ?>
                <tr>
                    <td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No non-faculty staff records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($staffList as $st): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($st['employee_id']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 600;"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                        <td style="padding: 0.75rem; text-transform: uppercase; font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">
                            <?= e($st['department_type']) ?>
                        </td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($st['designation_name']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($st['mobile'] ?? 'N/A') ?></td>
                        <td style="padding: 0.75rem;">
                            <span class="badge badge-success">Active</span>
                        </td>
                        <td style="padding: 0.75rem;">
                            <a href="/staff/<?= $st['id'] ?>" style="color: #a5b4fc; text-decoration: none; font-weight: 500;">View Profile →</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('briefcase', 'icon-md') ?> Non-Faculty Staff Directory
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Administrative, Accounts, Library, Hostel, Transport, and Canteen staff management
            </div>
        </div>

        <a href="/staff/create" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.625rem 1.25rem; font-weight: 700;">
            <?= icon('user-plus', 'icon-xs') ?> Onboard Staff Member
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/staff" class="filter-bar">
        <div>
            <label class="form-label">Search Query</label>
            <input type="text" name="search" class="form-control" placeholder="Name, Staff ID, Email..." value="<?= e($filters['search']) ?>">
        </div>

        <div>
            <label class="form-label">Department Domain</label>
            <select name="department_type" class="form-control">
                <option value="">All Department Domains</option>
                <option value="accounts" <?= $filters['department_type'] === 'accounts' ? 'selected' : '' ?>>Accounts &amp; Finance</option>
                <option value="library" <?= $filters['department_type'] === 'library' ? 'selected' : '' ?>>Library Services</option>
                <option value="hostel" <?= $filters['department_type'] === 'hostel' ? 'selected' : '' ?>>Hostel Administration</option>
                <option value="transport" <?= $filters['department_type'] === 'transport' ? 'selected' : '' ?>>Transport &amp; Logistics</option>
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
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;"><?= icon('filter', 'icon-xs') ?> Apply Filters</button>
        </div>
    </form>

    <!-- Staff Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Staff Name</th>
                    <th>Department Domain</th>
                    <th>Designation</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($staffList)): ?>
                    <tr>
                        <td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No non-faculty staff records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($staffList as $st): ?>
                        <tr>
                            <td style="font-weight: 800; color: var(--accent-color);"><?= e($st['employee_id']) ?></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                            <td style="text-transform: uppercase; font-size: 0.75rem; color: var(--accent-color); font-weight: 700;">
                                <?= e($st['department_type']) ?>
                            </td>
                            <td style="color: var(--text-secondary);"><?= e($st['designation_name']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($st['mobile'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> Active</span>
                            </td>
                            <td>
                                <a href="/staff/<?= $st['id'] ?>" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 0.3rem;">View Profile <?= icon('arrow-right', 'icon-xs') ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

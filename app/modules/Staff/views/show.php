<div style="display: grid; grid-template-columns: 1fr 3fr; gap: 1.5rem;">
    <!-- Sidebar Profile Card -->
    <div class="panel" style="text-align: center;">
        <div style="width: 96px; height: 96px; border-radius: 50%; background: rgba(99, 102, 241, 0.2); color: #a5b4fc; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1rem auto; border: 2px solid var(--accent-color);">
            💼
        </div>
        <h2 style="font-size: 1.25rem; font-weight: 700;"><?= e($staff['first_name'] . ' ' . $staff['last_name']) ?></h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 0.5rem;"><?= e($staff['employee_id']) ?></p>
        <span class="badge badge-info" style="text-transform: uppercase; margin-bottom: 1rem;"><?= e($staff['department_type']) ?> DOMAIN</span>

        <div style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem; text-align: left; font-size: 0.8125rem;">
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Designation:</span>
                <strong><?= e($staff['designation_name']) ?></strong>
            </div>
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Joining Date:</span>
                <strong><?= e($staff['joining_date']) ?></strong>
            </div>
            <div style="margin-bottom: 0.75rem;">
                <span style="color: var(--text-secondary); display: block;">Portal Account:</span>
                <?php if (!empty($staff['user_account_id'])): ?>
                    <span class="badge badge-success">Username: <?= e($staff['username']) ?></span>
                <?php else: ?>
                    <span class="badge badge-warning">No Account</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Content Panel -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Non-Faculty Staff Contact Details</h3>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; font-size: 0.875rem;">
            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Department Domain</span>
                <strong style="text-transform: uppercase;"><?= e($staff['department_type']) ?></strong>
            </div>

            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Email Address</span>
                <strong><?= e($staff['email'] ?? 'N/A') ?></strong>
            </div>

            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Mobile Number</span>
                <strong><?= e($staff['mobile'] ?? 'N/A') ?></strong>
            </div>

            <div>
                <span style="color: var(--text-secondary); display: block; font-size: 0.75rem;">Status</span>
                <span class="badge badge-success" style="text-transform: capitalize;"><?= e($staff['status']) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title"><?= icon('wallet', 'icon-sm') ?> Accounts &amp; Staff Payroll Overview</h2>
        <a href="/fee/payments" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1.25rem; display: inline-flex; align-items: center; gap: 0.35rem;">
            <?= icon('credit-card', 'icon-xs') ?> Fee Ledger &amp; Revenue
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Staff Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Payroll Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($staffPayroll)): ?>
                    <tr>
                        <td colspan="6" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No non-faculty staff accounts found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($staffPayroll as $st): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--accent-color);"><?= e($st['employee_id']) ?></td>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                            <td style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);"><?= e($st['department_domain']) ?></td>
                            <td style="color: var(--text-secondary);"><?= e($st['designation']) ?></td>
                            <td><span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> ACTIVE</span></td>
                            <td>
                                <button class="btn-primary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; width: auto; display: inline-flex; align-items: center; gap: 0.3rem;">
                                    <?= icon('file-text', 'icon-xs') ?> Process Payslip
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

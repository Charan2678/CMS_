<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Accounts & Staff Payroll Overview</h2>
        <a href="/fee/payments" class="btn-primary" style="text-decoration: none; width: auto; padding: 0.5rem 1.25rem;">💳 Fee Ledger & Revenue</a>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.75rem;">Staff ID</th>
                <th style="padding: 0.75rem;">Staff Name</th>
                <th style="padding: 0.75rem;">Department</th>
                <th style="padding: 0.75rem;">Designation</th>
                <th style="padding: 0.75rem;">Status</th>
                <th style="padding: 0.75rem;">Payroll Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($staffPayroll)): ?>
                <tr>
                    <td colspan="6" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No non-faculty staff accounts found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($staffPayroll as $st): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($st['employee_id']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 600;"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                        <td style="padding: 0.75rem; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);"><?= e($st['department_domain']) ?></td>
                        <td style="padding: 0.75rem;"><?= e($st['designation']) ?></td>
                        <td style="padding: 0.75rem;"><span class="badge badge-success">ACTIVE</span></td>
                        <td style="padding: 0.75rem;">
                            <button class="btn-primary" style="padding: 0.375rem 0.75rem; font-size: 0.75rem; width: auto;">Process Payslip</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📜</span> System Activity & Security Audit Logs
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Immutable system audit trail tracking user actions, logins, and data transactions
            </div>
        </div>

        <span class="badge badge-warning" style="font-size: 0.75rem; text-transform: uppercase;">Write-Only Audit Ledger</span>
    </div>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Module</th>
                    <th>Action Executed</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No system activity logged yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="color: var(--text-secondary); font-size: 0.8125rem;"><?= date('d M Y H:i:s', strtotime($log['created_at'])) ?></td>
                            <td style="font-weight: 700; color: var(--accent-color);"><?= e($log['username'] ?? 'System') ?></td>
                            <td style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);"><?= e($log['module']) ?></td>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= e($log['action']) ?></td>
                            <td style="color: var(--text-secondary); font-family: monospace; font-size: 0.8125rem;"><?= e($log['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

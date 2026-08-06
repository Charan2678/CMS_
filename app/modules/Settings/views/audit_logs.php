<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Immutable System Activity Audit Log</h2>
        <span class="badge badge-warning">Write-Only Audit Ledger</span>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                <th style="padding: 0.75rem;">Timestamp</th>
                <th style="padding: 0.75rem;">User</th>
                <th style="padding: 0.75rem;">Module</th>
                <th style="padding: 0.75rem;">Action Executed</th>
                <th style="padding: 0.75rem;">IP Address</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="5" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No system activity logged yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($log['created_at']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 600; color: #a5b4fc;"><?= e($log['username'] ?? 'System') ?></td>
                        <td style="padding: 0.75rem; text-transform: uppercase; font-size: 0.75rem; font-weight: 700; color: var(--text-secondary);"><?= e($log['module']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 500;"><?= e($log['action']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary); font-family: monospace;"><?= e($log['ip_address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

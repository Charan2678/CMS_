<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">👥 Registered Library Members Directory</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Enrolled student and faculty cardholders eligible for library borrowing</p>
    </div>
    <a href="/library" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Library Overview</a>
</div>

<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Enrolled Student Members (<?= count($students) ?> Active)
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Member Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td style="font-weight: 700; font-family: monospace; color: var(--accent-color);"><?= e($s['roll_number']) ?></td>
                        <td style="font-weight: 600; color: var(--text-primary);"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></td>
                        <td style="color: var(--text-secondary);"><?= e($s['email']) ?></td>
                        <td style="color: var(--text-secondary);"><?= e($s['mobile']) ?></td>
                        <td><span class="badge badge-success">Active Cardholder</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

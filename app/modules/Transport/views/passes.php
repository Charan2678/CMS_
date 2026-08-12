<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('id-card', 'icon-sm') ?> Student Bus Pass Management
        </h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">
            Institutional transport credentials issued strictly upon verified fee payment.
        </p>
    </div>

    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="/transport" class="btn btn-secondary" style="font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
            <?= icon('arrow-left', 'icon-xs') ?> Transport Overview
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- KPI Stats Grid -->
<div class="grid-metrics" style="margin-bottom: 1.5rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Subscribed Passes</div>
            <div class="metric-value"><?= number_format($stats['total_passes'] ?? 0) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">All registered transport passes</div>
        </div>
        <div class="metric-icon icon-peach"><?= icon('id-card', 'icon-lg') ?></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Active Paid Passes</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($stats['active_passes'] ?? 0) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Fee cleared &amp; active credential</div>
        </div>
        <div class="metric-icon icon-green"><?= icon('check-circle-2', 'icon-lg') ?></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Payment Pending</div>
            <div class="metric-value" style="color: var(--orange-accent);"><?= number_format($stats['payment_pending'] ?? 0) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Locked until fee is confirmed</div>
        </div>
        <div class="metric-icon icon-peach"><?= icon('lock', 'icon-lg') ?></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Suspended / Expired</div>
            <div class="metric-value" style="color: var(--danger); font-size: 1.65rem;"><?= number_format(($stats['suspended'] ?? 0) + ($stats['expired'] ?? 0)) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Inactive credentials</div>
        </div>
        <div class="metric-icon icon-red"><?= icon('alert-octagon', 'icon-lg') ?></div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
    <form method="GET" action="/transport/passes" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 0.85rem; align-items: flex-end;">
        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.25rem; display: block;">Pass Status</label>
            <select name="status" class="form-control" style="font-size: 0.8125rem;">
                <option value="">-- All Statuses --</option>
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active (Paid)</option>
                <option value="payment_pending" <?= ($filters['status'] ?? '') === 'payment_pending' ? 'selected' : '' ?>>Payment Pending</option>
                <option value="suspended" <?= ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.25rem; display: block;">Filter by Route</label>
            <select name="route_id" class="form-control" style="font-size: 0.8125rem;">
                <option value="">-- All Routes --</option>
                <?php foreach ($routes as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= ($filters['route_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                        <?= e($r['route_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.25rem; display: block;">Search Student / Pass</label>
            <input type="text" name="search" value="<?= e($filters['search'] ?? '') ?>" placeholder="Name, Roll No, or Pass ID..." class="form-control" style="font-size: 0.8125rem;">
        </div>

        <div>
            <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 0.8125rem; padding: 0.55rem; font-weight: 700;">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Passes Ledger Table -->
<div class="card">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.15rem;">
        <h2 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.45rem;">
            <?= icon('list', 'icon-sm') ?> Registered Bus Passes (<?= count($passes) ?> Records)
        </h2>
    </div>

    <?php if (empty($passes)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 3rem 0; font-size: 0.875rem;">
            No student bus passes found for this filter selection.
        </p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.8125rem;">
                <thead>
                    <tr>
                        <th>Pass ID</th>
                        <th>Student Rider</th>
                        <th>Assigned Bus &amp; Route</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($passes as $p): ?>
                        <tr>
                            <td>
                                <code style="font-weight: 800; color: var(--text-primary); font-size: 0.8rem;"><?= e($p['pass_number']) ?></code>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.15rem;">
                                    Issued: <?= date('d M Y', strtotime($p['issue_date'])) ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);">
                                    <?= e($p['first_name'] . ' ' . $p['last_name']) ?>
                                </div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.1rem;">
                                    <?= e($p['roll_number']) ?> &bull; <?= e($p['course_code'] ?? 'B.Tech') ?> (Sem <?= e($p['semester_number'] ?? '1') ?>)
                                </div>
                            </td>
                            <td>
                                <strong style="color: var(--text-primary);"><?= e($p['bus_number'] ?? 'Bus') ?></strong>
                                <div style="font-size: 0.7rem; color: var(--orange-accent); font-weight: 600; margin-top: 0.1rem;">
                                    <?= e($p['route_name']) ?>
                                </div>
                            </td>
                            <td style="white-space: nowrap; color: var(--text-secondary);">
                                <?= date('d M Y', strtotime($p['valid_until'])) ?>
                            </td>
                            <td>
                                <?php if ($p['status'] === 'active'): ?>
                                    <span class="badge badge-green" style="font-weight: 800; font-size: 0.725rem;">
                                        <?= icon('check-circle-2', 'icon-xs') ?> ACTIVE (PAID)
                                    </span>
                                <?php elseif ($p['status'] === 'suspended'): ?>
                                    <span class="badge badge-red" style="font-weight: 800; font-size: 0.725rem;">
                                        <?= icon('alert-octagon', 'icon-xs') ?> SUSPENDED
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-peach" style="font-weight: 800; font-size: 0.725rem;">
                                        <?= icon('lock', 'icon-xs') ?> PAYMENT PENDING
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                                    <?php if ($p['status'] === 'active'): ?>
                                        <a href="/transport/pass/<?= $p['id'] ?>" target="_blank" class="btn-primary" style="text-decoration: none; padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <?= icon('printer', 'icon-xs') ?> View Pass
                                        </a>
                                        <form method="POST" action="/transport/pass/<?= $p['id'] ?>/suspend" style="display: inline;" onsubmit="return confirm('Suspend this active bus pass?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; font-weight: 700;">
                                                Suspend
                                            </button>
                                        </form>
                                    <?php elseif ($p['status'] === 'suspended'): ?>
                                        <form method="POST" action="/transport/pass/<?= $p['id'] ?>/reactivate" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                                                Reactivate
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="/fee/payments" class="btn btn-secondary" style="text-decoration: none; padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700; border-radius: 6px;">
                                            Verify Fee
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

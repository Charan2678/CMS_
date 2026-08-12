<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.4rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('bus', 'icon-md') ?> Institutional Transport &amp; Fleet Management
        </h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">
            Manage college bus fleet, routes, student allocations, and digital bus passes.
        </p>
    </div>

    <div style="display: flex; gap: 0.75rem; align-items: center;">
        <a href="/transport/passes" class="btn btn-primary" style="font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none; border-radius: 8px; box-shadow: var(--shadow-glow-primary);">
            <?= icon('id-card', 'icon-xs') ?> Manage All Bus Passes
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- Bus Passes & Fleet Overview KPI Grid -->
<div class="grid-metrics" style="margin-bottom: 2rem;">
    <div class="metric-card">
        <div>
            <div class="metric-label">Total Subscribed Riders</div>
            <div class="metric-value"><?= number_format($passStats['total_passes'] ?? 0) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Active transport subscriptions</div>
        </div>
        <div class="metric-icon icon-peach"><?= icon('users', 'icon-lg') ?></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Active (Paid) Bus Passes</div>
            <div class="metric-value" style="color: var(--success);"><?= number_format($passStats['active_passes'] ?? 0) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Fee cleared &amp; credential issued</div>
        </div>
        <div class="metric-icon icon-green"><?= icon('check-circle-2', 'icon-lg') ?></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Payment Pending Passes</div>
            <div class="metric-value" style="color: var(--orange-accent);"><?= number_format($passStats['payment_pending'] ?? 0) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Pass locked until fee paid</div>
        </div>
        <div class="metric-icon icon-peach"><?= icon('lock', 'icon-lg') ?></div>
    </div>

    <div class="metric-card">
        <div>
            <div class="metric-label">Active College Fleet</div>
            <div class="metric-value" style="font-size: 1.65rem; color: var(--text-primary);"><?= number_format($passStats['total_buses'] ?? 0) ?> Buses</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;"><?= count($routes) ?> Active Route Pathways</div>
        </div>
        <div class="metric-icon icon-info"><?= icon('bus', 'icon-lg') ?></div>
    </div>
</div>

<!-- Transport Operations & Subscriptions Grid -->
<div class="dashboard-grid-equal" style="margin-bottom: 2rem;">
    <!-- 1. Allocate Student to Route -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; font-weight: 800;">
            <?= icon('user-plus', 'icon-sm') ?> Subscribe Student to Bus Route
        </h3>
        <form method="POST" action="/transport">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="allocate_student">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Student *</label>
                <select name="student_id" class="form-control" required>
                    <option value="">-- Choose Student --</option>
                    <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>">
                            <?= e($st['roll_number']) ?> — <?= e($st['first_name'] . ' ' . $st['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Transport Route *</label>
                <select name="transport_route_id" class="form-control" required>
                    <option value="">-- Choose Bus Route --</option>
                    <?php foreach ($routes as $r): ?>
                        <option value="<?= $r['id'] ?>">
                            <?= e($r['route_name']) ?> (<?= e($r['vehicle_number'] ?? 'Bus') ?> &bull; Fare: ₹<?= number_format((float)$r['fare'], 2) ?>/yr)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 700;"><?= icon('check-circle-2', 'icon-xs') ?> Subscribe Student</button>
        </form>
    </div>

    <!-- 2. Add New Route -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem; font-weight: 800;">
            <?= icon('plus', 'icon-sm') ?> Add New Transport Route
        </h3>
        <form method="POST" action="/transport">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="create_route">

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Route Name *</label>
                    <input type="text" name="route_name" required placeholder="e.g. Route 3 - Palamaner to Campus" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Route Code</label>
                    <input type="text" name="route_code" placeholder="e.g. R-03" class="form-control">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Start Point</label>
                    <input type="text" name="start_point" placeholder="e.g. Palamaner Bus Station" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Annual Fare (₹)</label>
                    <input type="number" step="0.01" name="fare" value="18000.00" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 0.6rem; font-weight: 700;"><?= icon('plus', 'icon-xs') ?> Add Bus Route</button>
        </form>
    </div>
</div>

<!-- 3. Active Student Route Subscriptions & Bus Pass Register (ADMIN ONLY) -->
<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('clipboard-list', 'icon-sm') ?> Active Student Transport Subscriptions &amp; Passes
        </h2>
        <a href="/transport/passes" class="btn btn-secondary" style="font-size: 0.8125rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
            <?= icon('id-card', 'icon-xs') ?> Detailed Pass Ledger
        </a>
    </div>

    <?php if (empty($allocations)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">No active student bus route subscriptions.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table" style="font-size: 0.8125rem;">
                <thead>
                    <tr>
                        <th>Student Rider</th>
                        <th>Subscribed Route</th>
                        <th>Assigned Bus</th>
                        <th>Bus Pass Status</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocations as $ta): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($ta['first_name'] . ' ' . $ta['last_name']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.3rem; margin-top: 0.15rem;">
                                    <?= e($ta['roll_number']) ?> &bull; <?= icon('phone', 'icon-xs') ?> <?= e($ta['mobile'] ?? 'N/A') ?>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--accent-color);"><?= e($ta['route_name']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--success); font-weight: 700;">₹<?= number_format((float)$ta['fare'], 2) ?>/yr</div>
                            </td>
                            <td>
                                <strong style="color: var(--text-primary);"><?= e($ta['bus_number'] ?? 'Assigned Bus') ?></strong>
                            </td>
                            <td>
                                <?php if (($ta['pass_status'] ?? '') === 'active'): ?>
                                    <span class="badge badge-green" style="font-weight: 800;">
                                        <?= icon('check-circle-2', 'icon-xs') ?> ACTIVE PASS
                                    </span>
                                <?php elseif (($ta['pass_status'] ?? '') === 'suspended'): ?>
                                    <span class="badge badge-red" style="font-weight: 800;">
                                        <?= icon('alert-octagon', 'icon-xs') ?> SUSPENDED
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-peach" style="font-weight: 800;">
                                        <?= icon('lock', 'icon-xs') ?> PAYMENT PENDING
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                                    <?php if (!empty($ta['pass_id']) && $ta['pass_status'] === 'active'): ?>
                                        <a href="/transport/pass/<?= $ta['pass_id'] ?>" target="_blank" class="btn btn-sm btn-primary" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700; text-decoration: none;">
                                            <?= icon('printer', 'icon-xs') ?> View Pass
                                        </a>
                                    <?php endif; ?>

                                    <form method="POST" action="/transport" style="display: inline;" onsubmit="return confirm('Cancel this student bus subscription?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="cancel_allocation">
                                        <input type="hidden" name="allocation_id" value="<?= $ta['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.3rem 0.55rem; font-size: 0.75rem; font-weight: 700;">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- 4. Transport Routes & Fleet Directory -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <?= icon('map-pin', 'icon-sm') ?> College Bus Routes &amp; Fares Directory
    </h2>

    <div class="table-responsive">
        <table class="table" style="font-size: 0.8125rem;">
            <thead>
                <tr>
                    <th>Route Name</th>
                    <th>Bus / Vehicle</th>
                    <th>Route Pathway</th>
                    <th>Active Riders</th>
                    <th style="text-align: right;">Annual Bus Fee</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $r): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--accent-color);"><?= e($r['route_name']) ?></td>
                        <td>
                            <strong style="color: var(--text-primary);"><?= e($r['vehicle_number'] ?? 'Bus') ?></strong>
                            <?php if (!empty($r['driver_name'])): ?>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Driver: <?= e($r['driver_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 0.75rem;"><?= e($r['start_point']) ?> &rarr; <?= e($r['end_point']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 700; color: var(--text-primary);"><?= e($r['active_riders'] ?? 0) ?> Students</td>
                        <td style="text-align: right; font-weight: 800; color: var(--success); font-size: 0.9375rem;">
                            ₹<?= number_format((float)$r['fare'], 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

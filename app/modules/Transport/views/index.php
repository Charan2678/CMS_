<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($isStudentOrParent)): ?>
<!-- Student Bus Subscription & Payment Banner -->
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid var(--accent-color); background: linear-gradient(135deg, rgba(2, 132, 199, 0.05) 0%, rgba(37, 99, 235, 0.05) 100%);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                <span style="font-size: 1.35rem;">🚌</span>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">
                    <?= !empty($mySubscription) ? 'My College Bus Subscription' : 'Bus Transport Route & Fee Payment' ?>
                </h3>
                <span class="badge" style="background: #0284c7; color: #fff; font-size: 0.7rem; font-weight: 700;">Official Canara Bank QR</span>
            </div>
            <p style="margin: 0; font-size: 0.8125rem; color: var(--text-secondary);">
                <?php if (!empty($mySubscription)): ?>
                    Active Route: <strong><?= e($mySubscription['route_name']) ?></strong> (Pickup: <?= e($mySubscription['pickup_point'] ?? 'Campus') ?>). Scan and pay your annual bus transport fare directly.
                <?php else: ?>
                    Subscribe to college bus transportation &amp; pay annual route fare via verified Canara Bank QR code (<code>106508598000310@cnrb</code>).
                <?php endif; ?>
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="/fee/pay/transport" class="btn btn-primary" style="text-decoration: none; font-weight: 800; padding: 0.65rem 1.35rem; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);">
                <span>📱</span> Pay Bus Fee (Scan QR)
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($canManage)): ?>
<!-- Transport Operations & Subscriptions Grid -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- 1. Allocate Student to Route -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
            <span>🚌</span> Subscribe Student to Bus Route
        </h3>
        <form method="POST" action="/transport">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="allocate_student">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Select Student *</label>
                <select name="student_id" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
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
                <select name="transport_route_id" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                    <option value="">-- Choose Bus Route --</option>
                    <?php foreach ($routes as $r): ?>
                        <option value="<?= $r['id'] ?>">
                            <?= e($r['route_name']) ?> (Fare: ₹<?= number_format((float)$r['fare'], 2) ?>/yr)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 700;">Subscribe Student</button>
        </form>
    </div>

    <!-- 2. Add New Route -->
    <div class="card" style="border-top: 4px solid #8b5cf6;">
        <h3 style="margin-top: 0; font-size: 1.1rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
            <span>➕</span> Add New Transport Route
        </h3>
        <form method="POST" action="/transport">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="create_route">

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Route Name *</label>
                    <input type="text" name="route_name" required placeholder="e.g. Route 3 - Palamaner to Campus" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Route Code</label>
                    <input type="text" name="route_code" placeholder="e.g. R-03" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Start Point</label>
                    <input type="text" name="start_point" placeholder="e.g. Palamaner Bus Station" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Annual Fare (₹)</label>
                    <input type="number" step="0.01" name="fare" value="18000.00" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
            </div>

            <button type="submit" class="btn btn-secondary" style="width: 100%; padding: 0.6rem; font-weight: 700; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid #8b5cf6;">Add Bus Route</button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 3. Active Student Route Subscriptions Register -->
<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>📋</span> Active Student Bus Subscriptions
    </h2>

    <?php if (empty($allocations)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">No active student bus route subscriptions.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                        <th style="padding: 0.65rem 0.75rem;">Student Rider</th>
                        <th style="padding: 0.65rem 0.75rem;">Subscribed Route</th>
                        <th style="padding: 0.65rem 0.75rem;">Subscribed Date</th>
                        <th style="padding: 0.65rem 0.75rem;">Status</th>
                        <th style="padding: 0.65rem 0.75rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allocations as $ta): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($ta['first_name'] . ' ' . $ta['last_name']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);"><?= e($ta['roll_number']) ?> &bull; 📞 <?= e($ta['mobile'] ?? 'N/A') ?></div>
                            </td>
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 600; color: var(--accent-color);"><?= e($ta['route_name']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--success); font-weight: 700;">₹<?= number_format((float)$ta['fare'], 2) ?>/yr</div>
                            </td>
                            <td style="padding: 0.75rem; color: var(--text-secondary); white-space: nowrap;">
                                <?= date('d M Y', strtotime($ta['allocated_date'])) ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ($ta['status'] === 'active'): ?>
                                    <span class="badge badge-success">ACTIVE</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">CANCELLED</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php if ($ta['status'] === 'active' && !empty($canManage)): ?>
                                    <form method="POST" action="/transport" style="display: inline;" onsubmit="return confirm('Cancel this student bus subscription?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_action" value="cancel_allocation">
                                        <input type="hidden" name="allocation_id" value="<?= $ta['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700;">
                                            Cancel
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: var(--text-secondary);">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- 4. Transport Routes Directory -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>🗺️</span> College Bus Routes &amp; Fares Directory
    </h2>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                    <th style="padding: 0.65rem 0.75rem;">Route Name</th>
                    <th style="padding: 0.65rem 0.75rem;">Code</th>
                    <th style="padding: 0.65rem 0.75rem;">Route Pathway</th>
                    <th style="padding: 0.65rem 0.75rem;">Active Riders</th>
                    <th style="padding: 0.65rem 0.75rem; text-align: right;">Annual Bus Fee</th>
                    <th style="padding: 0.65rem 0.75rem; text-align: center;">Bus Fee Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $r): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-weight: 700; color: var(--accent-color);"><?= e($r['route_name']) ?></td>
                        <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($r['route_code'] ?? ($r['vehicle_number'] ?? 'Bus Route')) ?></td>
                        <td style="padding: 0.75rem;"><?= e($r['start_point']) ?> &rarr; <?= e($r['end_point']) ?></td>
                        <td style="padding: 0.75rem; font-weight: 700; color: var(--text-primary);"><?= e($r['active_riders'] ?? 0) ?> Students</td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 800; color: var(--success); font-size: 0.9375rem;">
                            ₹<?= number_format((float)$r['fare'], 2) ?>
                        </td>
                        <td style="padding: 0.75rem; text-align: center;">
                            <a href="/fee/pay/transport" class="btn btn-sm btn-primary" style="text-decoration: none; padding: 0.35rem 0.85rem; font-size: 0.75rem; font-weight: 700; border-radius: 6px;">
                                💳 Pay (Scan QR)
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: <?= !empty($canManage) ? '1fr 2fr' : '1fr' ?>; gap: 1.5rem;">
    <?php if (!empty($canManage)): ?>
    <!-- Form Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Add Transport Route</h2>
        </div>

        <form method="POST" action="/transport">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="route_name">Route Name *</label>
                <input type="text" id="route_name" name="route_name" class="form-control" required placeholder="e.g. Route 1 - Kuppam Town to Campus">
            </div>

            <div class="form-group">
                <label class="form-label" for="route_code">Route Code</label>
                <input type="text" id="route_code" name="route_code" class="form-control" placeholder="e.g. R01">
            </div>

            <div class="form-group">
                <label class="form-label" for="start_point">Starting Point</label>
                <input type="text" id="start_point" name="start_point" class="form-control" placeholder="e.g. Kuppam Bus Stand">
            </div>

            <div class="form-group">
                <label class="form-label" for="end_point">Destination Point</label>
                <input type="text" id="end_point" name="end_point" class="form-control" placeholder="e.g. College Campus Gate 1">
            </div>

            <div class="form-group">
                <label class="form-label" for="fare">Annual Transport Fare (₹)</label>
                <input type="number" step="0.01" id="fare" name="fare" class="form-control" placeholder="15000.00">
            </div>

            <button type="submit" class="btn-primary">Add Transport Route</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Transport Routes Directory</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Route Name</th>
                    <th style="padding: 0.75rem;">Code</th>
                    <th style="padding: 0.75rem;">Start → Destination</th>
                    <th style="padding: 0.75rem;">Annual Fare</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($routes)): ?>
                    <tr>
                        <td colspan="4" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No transport routes configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($routes as $r): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 600; color: #a5b4fc;"><?= e($r['route_name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($r['route_code']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($r['start_point']) ?> → <?= e($r['end_point']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #86efac;">₹<?= number_format((float)$r['fare'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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
            <h2 class="panel-title">Add Hostel Block</h2>
        </div>

        <form method="POST" action="/hostel">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Block Name / Title *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Block A - Boys Hostel">
            </div>

            <div class="form-group">
                <label class="form-label" for="gender_type">Hostel Type *</label>
                <select id="gender_type" name="gender_type" class="form-control" required>
                    <option value="boys">Boys Hostel</option>
                    <option value="girls">Girls Hostel</option>
                    <option value="coed">Co-Ed Hostel</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="warden_name">Warden Name</label>
                <input type="text" id="warden_name" name="warden_name" class="form-control" placeholder="e.g. Dr. Rajesh Kumar">
            </div>

            <div class="form-group">
                <label class="form-label" for="warden_phone">Warden Phone</label>
                <input type="text" id="warden_phone" name="warden_phone" class="form-control" placeholder="e.g. +91 98765 43210">
            </div>

            <button type="submit" class="btn-primary">Add Hostel Block</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Hostel Blocks Directory</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Block Name</th>
                    <th style="padding: 0.75rem;">Type</th>
                    <th style="padding: 0.75rem;">Warden</th>
                    <th style="padding: 0.75rem;">Contact</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($blocks)): ?>
                    <tr>
                        <td colspan="4" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No hostel blocks configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($blocks as $b): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 600; color: #a5b4fc;"><?= e($b['name']) ?></td>
                            <td style="padding: 0.75rem; text-transform: uppercase;"><span class="badge badge-info"><?= e($b['gender_type']) ?></span></td>
                            <td style="padding: 0.75rem;"><?= e($b['warden_name'] ?? 'N/A') ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($b['warden_phone'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

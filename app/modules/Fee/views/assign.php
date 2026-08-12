<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('credit-card', 'icon-md') ?> Mass Assign Fees to Enrolled Students
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Batch generate student fee ledgers for an entire section or academic class
            </div>
        </div>
        <span class="badge badge-info" style="font-size: 0.75rem;">Batch Ledger Generator</span>
    </div>

    <form method="POST" action="/fee/assign">
        <?= csrf_field() ?>

        <div class="dashboard-grid-equal" style="margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="fee_structure_id">Select Configured Fee Schedule *</label>
                <select id="fee_structure_id" name="fee_structure_id" class="form-control" required>
                    <option value="">-- Select Fee Structure --</option>
                    <?php foreach ($structures as $fs): ?>
                        <option value="<?= $fs['id'] ?>">
                            <?= e($fs['academic_year_name']) ?> — <?= e($fs['course_code']) ?> Sem <?= e($fs['semester_number']) ?> (<?= e($fs['category_name']) ?>: ₹<?= number_format((float)$fs['amount'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="section_id">Select Target Section / Batch *</label>
                <select id="section_id" name="section_id" class="form-control" required>
                    <option value="">-- Select Section --</option>
                    <?php foreach ($sections as $sec): ?>
                        <?php 
                            $secName = $sec['name'] ?? '';
                            $cleanSec = (strpos(strtolower($secName), 'section') !== false) ? $secName : 'Section ' . $secName;
                        ?>
                        <option value="<?= $sec['id'] ?>">
                            <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> — <?= e($cleanSec) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="background: rgba(2, 132, 199, 0.08); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; box-shadow: var(--shadow-xs);">
            <strong style="color: var(--accent-color); font-size: 0.9375rem; display: flex; align-items: center; gap: 0.4rem;">
                <?= icon('zap', 'icon-xs') ?> Automated Batch Generation:
            </strong>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0.35rem 0 0 0; line-height: 1.45;">
                Executing this action will create individual fee ledgers for all active enrolled students in the selected section based on the configured fee structure.
            </p>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem; font-weight: 700;">
                <?= icon('check-circle-2', 'icon-xs') ?> Assign Fee to All Section Students
            </button>
        </div>
    </form>
</div>

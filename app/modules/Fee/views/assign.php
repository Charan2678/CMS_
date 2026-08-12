<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>💳</span> Mass Assign Fees to Enrolled Students
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Batch generate student fee ledgers for an entire section or academic class
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
            <a href="/fee/payments" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); transition: all 0.2s ease;">
                <span>←</span> Back to Fee &amp; Financial Ledger
            </a>
            <span class="badge badge-info" style="font-size: 0.75rem;">Batch Ledger Generator</span>
        </div>
    </div>

    <form method="POST" action="/fee/assign">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
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

        <div style="background: rgba(2, 132, 199, 0.1); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <strong style="color: var(--accent-color); font-size: 0.9375rem;">⚡ Automated Batch Generation:</strong>
            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0.25rem 0 0 0; line-height: 1.4;">
                Executing this action will create individual fee ledgers for all active enrolled students in the selected section based on the configured fee structure.
            </p>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 3rem; font-weight: 700;">Assign Fee to All Section Students</button>
        </div>
    </form>
</div>

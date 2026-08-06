<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel" style="max-width: 640px; margin: 0 auto;">
    <div class="panel-header">
        <h2 class="panel-title">Mass Assign Fee to Section Students</h2>
    </div>

    <form method="POST" action="/fee/assign">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="fee_structure_id">Select Configured Fee Structure *</label>
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
                    <option value="<?= $sec['id'] ?>">
                        <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> — Section <?= e($sec['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.75rem 2rem;">Assign Fee to All Section Students</button>
        </div>
    </form>
</div>

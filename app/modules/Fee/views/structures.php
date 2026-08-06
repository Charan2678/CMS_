<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Form Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Add Fee Structure</h2>
        </div>

        <form method="POST" action="/fee/structures">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="academic_year_id">Academic Year *</label>
                <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                    <option value="">-- Select Academic Year --</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= (int)$ay['is_current'] === 1 ? 'selected' : '' ?>>
                            <?= e($ay['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="course_id">Course *</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?> (<?= e($c['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="semester_id">Semester *</label>
                <select id="semester_id" name="semester_id" class="form-control" required>
                    <option value="">-- Select Semester --</option>
                    <?php foreach ($semesters as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= e($s['display']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="fee_category_id">Fee Category *</label>
                <select id="fee_category_id" name="fee_category_id" class="form-control" required>
                    <option value="">-- Select Fee Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="amount">Fee Amount (₹) *</label>
                <input type="number" step="0.01" id="amount" name="amount" class="form-control" required placeholder="e.g. 45000.00">
            </div>

            <div class="form-group">
                <label class="form-label" for="due_date">Due Date *</label>
                <input type="date" id="due_date" name="due_date" class="form-control" required>
            </div>

            <button type="submit" class="btn-primary">Save Fee Structure</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Configured Fee Structures</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Year</th>
                    <th style="padding: 0.75rem;">Course / Sem</th>
                    <th style="padding: 0.75rem;">Category</th>
                    <th style="padding: 0.75rem;">Amount</th>
                    <th style="padding: 0.75rem;">Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($structures)): ?>
                    <tr>
                        <td colspan="5" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No fee structures configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($structures as $fs): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($fs['academic_year_name']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 600; color: #a5b4fc;"><?= e($fs['course_code']) ?> (Sem <?= e($fs['semester_number']) ?>)</td>
                            <td style="padding: 0.75rem; font-weight: 500;"><?= e($fs['category_name']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #86efac;">₹<?= number_format((float)$fs['amount'], 2) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($fs['due_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

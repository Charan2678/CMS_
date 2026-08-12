<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="page-split">
    <!-- Create Fee Structure Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('plus', 'icon-sm') ?> Define Fee Structure
        </h2>

        <form method="POST" action="/fee/structures">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
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

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="course_id">Course *</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?> (<?= e($c['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="semester_id">Semester *</label>
                <select id="semester_id" name="semester_id" class="form-control" required>
                    <option value="">-- Select Semester --</option>
                    <?php foreach ($semesters as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= e($s['display']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="fee_category_id">Fee Category *</label>
                <select id="fee_category_id" name="fee_category_id" class="form-control" required>
                    <option value="">-- Select Fee Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" for="amount">Fee Amount (₹) *</label>
                    <input type="number" step="0.01" id="amount" name="amount" class="form-control" required placeholder="e.g. 45000.00">
                </div>

                <div class="form-group">
                    <label class="form-label" for="due_date">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;"><?= icon('save', 'icon-xs') ?> Save Fee Structure</button>
        </form>
    </div>

    <!-- Fee Structures List Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('settings', 'icon-sm') ?> Configured Fee Schedules Directory
        </h2>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Academic Year</th>
                        <th>Course / Sem</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($structures)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No fee structures configured yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($structures as $fs): ?>
                            <tr>
                                <td style="color: var(--text-secondary);"><?= e($fs['academic_year_name']) ?></td>
                                <td style="font-weight: 700; color: var(--accent-color);"><?= e($fs['course_code']) ?> (Sem <?= e($fs['semester_number']) ?>)</td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($fs['category_name']) ?></td>
                                <td style="font-weight: 800; color: var(--success);">₹<?= number_format((float)$fs['amount'], 2) ?></td>
                                <td style="color: var(--text-secondary); font-size: 0.8125rem;"><?= date('d M Y', strtotime($fs['due_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

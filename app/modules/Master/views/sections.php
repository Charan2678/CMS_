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
            <h2 class="panel-title">Add Section</h2>
        </div>

        <form method="POST" action="/master/sections">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="academic_year_id">Academic Year *</label>
                <select id="academic_year_id" name="academic_year_id" class="form-control" required>
                    <option value="">-- Select Academic Year --</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= (int)$ay['is_current'] === 1 ? 'selected' : '' ?>>
                            <?= e($ay['name']) ?> <?= (int)$ay['is_current'] === 1 ? '(Active)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="semester_id">Course & Semester *</label>
                <select id="semester_id" name="semester_id" class="form-control" required>
                    <option value="">-- Select Semester --</option>
                    <?php foreach ($semesters as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= e($s['display']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Section Name / Batch *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. A or B">
            </div>

            <div class="form-group">
                <label class="form-label" for="max_strength">Max Student Capacity</label>
                <input type="number" id="max_strength" name="max_strength" class="form-control" value="60" min="10" max="200">
            </div>

            <button type="submit" class="btn-primary">Create Section</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Sections List</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Academic Year</th>
                    <th style="padding: 0.75rem;">Course</th>
                    <th style="padding: 0.75rem;">Semester</th>
                    <th style="padding: 0.75rem;">Section</th>
                    <th style="padding: 0.75rem;">Capacity</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sections)): ?>
                    <tr>
                        <td colspan="5" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No sections configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sections as $sec): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($sec['academic_year_name']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 600; color: #a5b4fc;"><?= e($sec['course_code']) ?></td>
                            <td style="padding: 0.75rem;">Sem <?= e($sec['semester_number']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 700;">Section <?= e($sec['name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($sec['max_strength']) ?> Max</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

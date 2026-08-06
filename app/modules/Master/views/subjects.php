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
            <h2 class="panel-title">Add Subject</h2>
        </div>

        <form method="POST" action="/master/subjects">
            <?= csrf_field() ?>

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
                <label class="form-label" for="name">Subject Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Data Structures & Algorithms">
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Subject Code *</label>
                <input type="text" id="code" name="code" class="form-control" required placeholder="e.g. CS201">
            </div>

            <div class="form-group">
                <label class="form-label" for="type">Subject Type *</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="theory">Theory</option>
                    <option value="practical">Practical / Lab</option>
                    <option value="elective">Elective</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label" for="credits">Credits</label>
                    <input type="number" step="0.5" id="credits" name="credits" class="form-control" value="3.0">
                </div>

                <div class="form-group">
                    <label class="form-label" for="max_internal_marks">Max Internal</label>
                    <input type="number" id="max_internal_marks" name="max_internal_marks" class="form-control" value="25">
                </div>

                <div class="form-group">
                    <label class="form-label" for="max_external_marks">Max External</label>
                    <input type="number" id="max_external_marks" name="max_external_marks" class="form-control" value="75">
                </div>

                <div class="form-group">
                    <label class="form-label" for="pass_external_marks">Pass External</label>
                    <input type="number" id="pass_external_marks" name="pass_external_marks" class="form-control" value="30">
                </div>
            </div>

            <button type="submit" class="btn-primary">Create Subject</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Subjects List</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Code</th>
                    <th style="padding: 0.75rem;">Subject Name</th>
                    <th style="padding: 0.75rem;">Course / Sem</th>
                    <th style="padding: 0.75rem;">Type</th>
                    <th style="padding: 0.75rem;">Credits</th>
                    <th style="padding: 0.75rem;">Max Marks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subjects)): ?>
                    <tr>
                        <td colspan="6" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No subjects configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subjects as $sub): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($sub['code']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 500;"><?= e($sub['name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($sub['course_code']) ?> (Sem <?= e($sub['semester_number']) ?>)</td>
                            <td style="padding: 0.75rem;">
                                <span class="badge badge-info" style="text-transform: capitalize;"><?= e($sub['type']) ?></span>
                            </td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($sub['credits']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($sub['max_internal_marks']) ?> Int / <?= e($sub['max_external_marks']) ?> Ext</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

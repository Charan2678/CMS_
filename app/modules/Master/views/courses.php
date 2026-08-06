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
            <h2 class="panel-title">Add Course</h2>
        </div>

        <form method="POST" action="/master/courses">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="department_id">Department *</label>
                <select id="department_id" name="department_id" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="name">Course Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. B.Tech Computer Science">
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Course Code *</label>
                <input type="text" id="code" name="code" class="form-control" required placeholder="e.g. BTECH-CSE">
            </div>

            <div class="form-group">
                <label class="form-label" for="degree_type">Degree Type *</label>
                <select id="degree_type" name="degree_type" class="form-control" required>
                    <option value="ug">Undergraduate (UG)</option>
                    <option value="pg">Postgraduate (PG)</option>
                    <option value="diploma">Diploma</option>
                    <option value="certificate">Certificate</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="duration_years">Duration (Years) *</label>
                <input type="number" id="duration_years" name="duration_years" class="form-control" min="1" max="6" value="4" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="total_semesters">Total Semesters *</label>
                <input type="number" id="total_semesters" name="total_semesters" class="form-control" min="1" max="12" value="8" required>
            </div>

            <button type="submit" class="btn-primary">Create Course & Auto-Generate Semesters</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Courses List</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Code</th>
                    <th style="padding: 0.75rem;">Course Name</th>
                    <th style="padding: 0.75rem;">Dept</th>
                    <th style="padding: 0.75rem;">Degree</th>
                    <th style="padding: 0.75rem;">Duration</th>
                    <th style="padding: 0.75rem;">Semesters</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="6" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No courses created yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courses as $c): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($c['code']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 500;"><?= e($c['name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($c['department_code']) ?></td>
                            <td style="padding: 0.75rem; text-transform: uppercase; font-size: 0.75rem;"><?= e($c['degree_type']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($c['duration_years']) ?> Yrs</td>
                            <td style="padding: 0.75rem;">
                                <span class="badge badge-info"><?= e($c['total_semesters']) ?> Semesters</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

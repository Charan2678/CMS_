<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Create Course Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>🎓</span> Add New Course
        </h2>

        <form method="POST" action="/master/courses">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="department_id">Department *</label>
                <select id="department_id" name="department_id" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= e($d['name']) ?> (<?= e($d['code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="name">Course Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. B.Tech Computer Science">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="code">Course Code *</label>
                <input type="text" id="code" name="code" class="form-control" required placeholder="e.g. BTECH-CSE">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="degree_type">Degree Type *</label>
                <select id="degree_type" name="degree_type" class="form-control" required>
                    <option value="ug">Undergraduate (UG)</option>
                    <option value="pg">Postgraduate (PG)</option>
                    <option value="diploma">Diploma</option>
                    <option value="certificate">Certificate</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" for="duration_years">Duration (Years) *</label>
                    <input type="number" id="duration_years" name="duration_years" class="form-control" min="1" max="6" value="4" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="total_semesters">Total Semesters *</label>
                    <input type="number" id="total_semesters" name="total_semesters" class="form-control" min="1" max="12" value="8" required>
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Create Course & Semesters</button>
        </form>
    </div>

    <!-- Courses List Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📚</span> Degree Programs & Courses Directory
        </h2>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Dept</th>
                        <th>Degree</th>
                        <th>Duration</th>
                        <th>Semesters</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No courses created yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($c['code']) ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($c['name']) ?></td>
                                <td style="color: var(--text-secondary);"><?= e($c['department_code']) ?></td>
                                <td style="text-transform: uppercase; font-size: 0.75rem; font-weight: 700;"><?= e($c['degree_type']) ?></td>
                                <td style="color: var(--text-secondary);"><?= e($c['duration_years']) ?> Yrs</td>
                                <td>
                                    <span class="badge badge-info"><?= e($c['total_semesters']) ?> Semesters</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

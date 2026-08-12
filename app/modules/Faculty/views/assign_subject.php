<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>📚</span> Subject &amp; Section Faculty Assignment
        </h1>
        <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 0.25rem 0 0 0;">
            Assign course subjects and sections to faculty members for the active academic session.
        </p>
    </div>
    <div>
        <a href="/faculty" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); transition: all 0.2s ease;">
            <span>←</span> Back to Faculty Directory
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel" style="max-width: 680px; margin: 0 auto;">
    <div class="panel-header">
        <h2 class="panel-title">Subject & Section Faculty Assignment</h2>
    </div>

    <form method="POST" action="/faculty/assign-subject">
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
            <label class="form-label" for="faculty_id">Faculty Member *</label>
            <select id="faculty_id" name="faculty_id" class="form-control" required>
                <option value="">-- Select Faculty --</option>
                <?php foreach ($facultyList as $fac): ?>
                    <option value="<?= $fac['id'] ?>">
                        <?= e($fac['first_name'] . ' ' . $fac['last_name']) ?> (<?= e($fac['employee_id']) ?> - <?= e($fac['department_code']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="subject_id">Subject *</label>
            <select id="subject_id" name="subject_id" class="form-control" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?= $sub['id'] ?>">
                        <?= e($sub['code']) ?> — <?= e($sub['name']) ?> (<?= e($sub['course_code']) ?> Sem <?= e($sub['semester_number']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="section_id">Section / Batch *</label>
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
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.75rem 2rem;">Allocate Subject to Faculty</button>
        </div>
    </form>
</div>

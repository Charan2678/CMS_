<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Mark Daily Attendance</h2>
    </div>

    <!-- Selection Bar -->
    <form method="GET" action="/attendance" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 1.5rem;">
        <div>
            <label class="form-label">Academic Year *</label>
            <select name="academic_year_id" class="form-control" required>
                <?php foreach ($academicYears as $ay): ?>
                    <option value="<?= $ay['id'] ?>" <?= (int)$ay['id'] === $academicYearId || ((int)$ay['is_current'] === 1 && $academicYearId === 0) ? 'selected' : '' ?>>
                        <?= e($ay['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Section / Batch *</label>
            <select name="section_id" class="form-control" required>
                <option value="">-- Select Section --</option>
                <?php foreach ($sections as $sec): ?>
                    <option value="<?= $sec['id'] ?>" <?= $sectionId == $sec['id'] ? 'selected' : '' ?>>
                        <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> (Sec <?= e($sec['name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Subject *</label>
            <select name="subject_id" class="form-control" required>
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?= $sub['id'] ?>" <?= $subjectId == $sub['id'] ? 'selected' : '' ?>>
                        <?= e($sub['code']) ?> — <?= e($sub['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Date *</label>
            <input type="date" name="date" class="form-control" value="<?= e($date) ?>" required>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Load Student Roster</button>
        </div>
    </form>
</div>

<?php if ($sectionId > 0 && $subjectId > 0): ?>
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Student Attendance Roster</h2>
            <span class="badge badge-info">Date: <?= e($date) ?></span>
        </div>

        <?php if (empty($students)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">No active students enrolled in this section.</p>
        <?php else: ?>
            <form method="POST" action="/attendance">
                <?= csrf_field() ?>
                <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                <input type="hidden" name="subject_id" value="<?= $subjectId ?>">
                <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                <input type="hidden" name="date" value="<?= e($date) ?>">

                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.75rem;">Roll No</th>
                            <th style="padding: 0.75rem;">Student Name</th>
                            <th style="padding: 0.75rem;">Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $st): ?>
                            <?php $currStatus = $existing[$st['id']] ?? 'present'; ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($st['roll_number']) ?></td>
                                <td style="padding: 0.75rem; font-weight: 600;"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; gap: 1rem; align-items: center;">
                                        <label style="color: #86efac; font-weight: 500; cursor: pointer;">
                                            <input type="radio" name="attendance[<?= $st['id'] ?>]" value="present" <?= $currStatus === 'present' ? 'checked' : '' ?>> Present
                                        </label>
                                        <label style="color: #fca5a5; font-weight: 500; cursor: pointer;">
                                            <input type="radio" name="attendance[<?= $st['id'] ?>]" value="absent" <?= $currStatus === 'absent' ? 'checked' : '' ?>> Absent
                                        </label>
                                        <label style="color: #fcd34d; font-weight: 500; cursor: pointer;">
                                            <input type="radio" name="attendance[<?= $st['id'] ?>]" value="late" <?= $currStatus === 'late' ? 'checked' : '' ?>> Late
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 1.5rem; text-align: right;">
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.75rem 2.5rem;">Submit Attendance</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

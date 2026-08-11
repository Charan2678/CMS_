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
                <span>✅</span> Daily Class Attendance Roster
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Select academic placement and load enrolled student roster to log attendance
            </div>
        </div>
    </div>

    <!-- Selection Bar — Full Width Grid -->
    <form method="GET" action="/attendance" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end; background: var(--bg-main); padding: 1.25rem; border-radius: 10px; border: 1px solid var(--border-color); width: 100%;">
        <div>
            <label class="form-label">Academic Session *</label>
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
                    <?php 
                        $secName = $sec['name'] ?? '';
                        $cleanSec = (strpos(strtolower($secName), 'section') !== false) ? $secName : 'Section ' . $secName;
                    ?>
                    <option value="<?= $sec['id'] ?>" <?= $sectionId == $sec['id'] ? 'selected' : '' ?>>
                        <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> (<?= e($cleanSec) ?>)
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
    <div class="card" style="width: 100%;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📋</span> Enrolled Student Attendance Roster
            </h2>
            <span class="badge badge-info" style="font-weight: 700;">Date: <?= date('d M Y', strtotime($date)) ?></span>
        </div>

        <?php if (empty($students)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0; padding: 1rem 0;">No active students enrolled in this section.</p>
        <?php else: ?>
            <form method="POST" action="/attendance">
                <?= csrf_field() ?>
                <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                <input type="hidden" name="subject_id" value="<?= $subjectId ?>">
                <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                <input type="hidden" name="date" value="<?= e($date) ?>">

                <div style="overflow-x: auto; width: 100%;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Roll Number</th>
                                <th>Student Name</th>
                                <th style="text-align: right;">Attendance Marking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $st): ?>
                                <?php $currStatus = $existing[$st['id']] ?? 'present'; ?>
                                <tr>
                                    <td style="font-weight: 800; color: var(--accent-color);"><?= e($st['roll_number']) ?></td>
                                    <td style="font-weight: 700; color: var(--text-primary);"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                                    <td style="text-align: right;">
                                        <div style="display: inline-flex; gap: 1.25rem; align-items: center; background: var(--bg-main); padding: 0.35rem 0.875rem; border-radius: 20px; border: 1px solid var(--border-color);">
                                            <label style="color: var(--success); font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                                <input type="radio" name="attendance[<?= $st['id'] ?>]" value="present" <?= $currStatus === 'present' ? 'checked' : '' ?>> Present
                                            </label>
                                            <label style="color: var(--danger); font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                                <input type="radio" name="attendance[<?= $st['id'] ?>]" value="absent" <?= $currStatus === 'absent' ? 'checked' : '' ?>> Absent
                                            </label>
                                            <label style="color: var(--warning); font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                                <input type="radio" name="attendance[<?= $st['id'] ?>]" value="late" <?= $currStatus === 'late' ? 'checked' : '' ?>> Late
                                            </label>
                                            <label style="color: var(--accent-color); font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.25rem;">
                                                <input type="radio" name="attendance[<?= $st['id'] ?>]" value="on_leave" <?= $currStatus === 'on_leave' ? 'checked' : '' ?>> On Leave
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1.5rem; text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 3rem; font-weight: 700;">Submit Attendance</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

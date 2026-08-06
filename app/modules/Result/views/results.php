<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Semester Results & Marks Calculation Engine</h2>
    </div>

    <!-- Selection Bar -->
    <form method="GET" action="/results" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 1.5rem;">
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
            <label class="form-label">Course & Semester *</label>
            <select name="semester_id" class="form-control" required>
                <option value="">-- Select Semester --</option>
                <?php foreach ($semesters as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $semesterId == $s['id'] ? 'selected' : '' ?>><?= e($s['display']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Section (For External Marks Entry)</label>
            <select name="section_id" class="form-control">
                <option value="">-- All Sections --</option>
                <?php foreach ($sections as $sec): ?>
                    <option value="<?= $sec['id'] ?>" <?= $sectionId == $sec['id'] ? 'selected' : '' ?>>
                        <?= e($sec['course_code']) ?> Sem <?= e($sec['semester_number']) ?> (Sec <?= e($sec['name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Load Results / Marks</button>
        </div>
    </form>
</div>

<!-- Section 1: External Marks Entry & Calculation Engine -->
<?php if ($sectionId > 0 && !empty($students) && !empty($subjects)): ?>
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Enter External Exam Marks & Run Calculation Engine</h3>
        </div>

        <form method="POST" action="/results">
            <?= csrf_field() ?>
            <input type="hidden" name="semester_id" value="<?= $semesterId ?>">
            <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
            <input type="hidden" name="section_id" value="<?= $sectionId ?>">

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.75rem;">Roll No</th>
                            <th style="padding: 0.75rem;">Student Name</th>
                            <?php foreach ($subjects as $sub): ?>
                                <th style="padding: 0.75rem; text-align: center;"><?= e($sub['code']) ?> Marks</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $st): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($st['roll_number']) ?></td>
                                <td style="padding: 0.75rem; font-weight: 600;"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                                <?php foreach ($subjects as $sub): ?>
                                    <td style="padding: 0.75rem; text-align: center;">
                                        <input type="number" step="0.5" name="external_marks[<?= $st['id'] ?>][<?= $sub['id'] ?>]" class="form-control" style="width: 90px; text-align: center; display: inline-block;" placeholder="0-100">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem; text-align: right;">
                <button type="submit" class="btn-primary" style="width: auto; padding: 0.75rem 2.5rem;">Calculate & Generate Semester Results</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Section 2: Calculated Semester Results Ledger -->
<?php if ($semesterId > 0 && $academicYearId > 0): ?>
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Consolidated Semester Results Ledger</h3>
            <?php if (!empty($results)): ?>
                <form method="POST" action="/results" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="publish">
                    <input type="hidden" name="semester_id" value="<?= $semesterId ?>">
                    <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.5rem 1.25rem; font-size: 0.8125rem;">📢 Publish Results to Student Portal</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (empty($results)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">No calculated results found for this semester. Enter external marks above to run calculation.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                        <th style="padding: 0.75rem;">Rank</th>
                        <th style="padding: 0.75rem;">Roll No</th>
                        <th style="padding: 0.75rem;">Student Name</th>
                        <th style="padding: 0.75rem;">Total Marks</th>
                        <th style="padding: 0.75rem;">Percentage</th>
                        <th style="padding: 0.75rem;">Grade</th>
                        <th style="padding: 0.75rem;">Result Status</th>
                        <th style="padding: 0.75rem;">Publish Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($results as $res): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;">#<?= $rank++ ?></td>
                            <td style="padding: 0.75rem; font-weight: 600;"><?= e($res['roll_number']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($res['first_name'] . ' ' . $res['last_name']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($res['total_marks']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 700;"><?= e($res['percentage']) ?>%</td>
                            <td style="padding: 0.75rem;"><span class="badge badge-info"><?= e($res['grade']) ?></span></td>
                            <td style="padding: 0.75rem;">
                                <?php if ($res['result'] === 'pass'): ?>
                                    <span class="badge badge-success">PASS</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">FAIL</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ((int)$res['published'] === 1): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

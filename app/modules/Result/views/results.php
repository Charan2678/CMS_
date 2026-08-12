<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card" style="width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('bar-chart-3', 'icon-md') ?> Semester Examination Results Calculation Engine
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Compute semester SGPA, percentage, and publish gradecards to student portals
            </div>
        </div>
    </div>

    <!-- Selection Bar — Full Width Grid -->
    <form method="GET" action="/results" class="filter-bar">
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
            <label class="form-label">Course &amp; Semester *</label>
            <select name="semester_id" class="form-control" required>
                <option value="">-- Select Semester --</option>
                <?php foreach ($semesters as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $semesterId == $s['id'] ? 'selected' : '' ?>><?= e($s['display']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="form-label">Section (For Marks Entry)</label>
            <select name="section_id" class="form-control">
                <option value="">-- All Sections --</option>
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
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;"><?= icon('search', 'icon-xs') ?> Load Results Roster</button>
        </div>
    </form>
</div>

<!-- Section 1: External Marks Entry & Calculation Engine -->
<?php if ($sectionId > 0 && !empty($students) && !empty($subjects)): ?>
    <div class="card" style="width: 100%;">
        <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('file-signature', 'icon-sm') ?> External Semester Exam Marks Entry
        </h3>

        <form method="POST" action="/results">
            <?= csrf_field() ?>
            <input type="hidden" name="semester_id" value="<?= $semesterId ?>">
            <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
            <input type="hidden" name="section_id" value="<?= $sectionId ?>">

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <?php foreach ($subjects as $sub): ?>
                                <th style="text-align: center;"><?= e($sub['code']) ?> (100)</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $st): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($st['roll_number']) ?></td>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                                <?php foreach ($subjects as $sub): ?>
                                    <td style="text-align: center;">
                                        <input type="number" step="0.5" name="external_marks[<?= $st['id'] ?>][<?= $sub['id'] ?>]" class="form-control" style="width: 100px; text-align: center; display: inline-block; font-weight: 700;" placeholder="0-100">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1.5rem; text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem; font-weight: 700;"><?= icon('calculator', 'icon-sm') ?> Calculate &amp; Generate Semester Results</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Section 2: Calculated Semester Results Ledger -->
<?php if ($semesterId > 0 && $academicYearId > 0): ?>
    <div class="card" style="width: 100%;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('file-spreadsheet', 'icon-sm') ?> Consolidated Semester Results Ledger
            </h3>
            <?php if (!empty($results)): ?>
                <form method="POST" action="/results" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="publish">
                    <input type="hidden" name="semester_id" value="<?= $semesterId ?>">
                    <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.625rem 1.25rem; font-size: 0.8125rem; font-weight: 700;"><?= icon('send', 'icon-xs') ?> Publish Results to Student Portal</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (empty($results)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0; padding: 1rem 0;">No calculated results found for this semester. Enter external marks above to run calculation engine.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Roll Number</th>
                            <th>Student Name</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>Result</th>
                            <th>Portal Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; foreach ($results as $res): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);">#<?= $rank++ ?></td>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($res['roll_number']) ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($res['first_name'] . ' ' . $res['last_name']) ?></td>
                                <td style="font-weight: 700;"><?= e($res['total_marks']) ?></td>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($res['percentage']) ?>%</td>
                                <td><span class="badge badge-info"><?= e($res['grade']) ?></span></td>
                                <td>
                                    <?php if ($res['result'] === 'pass'): ?>
                                        <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> PASS</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><?= icon('x-circle', 'icon-xs') ?> FAIL</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ((int)$res['published'] === 1): ?>
                                        <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> Published</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><?= icon('clock', 'icon-xs') ?> Draft</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

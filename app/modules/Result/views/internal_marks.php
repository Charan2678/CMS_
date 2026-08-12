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
                <?= icon('file-signature', 'icon-md') ?> Internal Assessment (CIA) Marks Entry
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Enter mid-term, assignment, or continuous internal assessment marks
            </div>
        </div>
    </div>

    <!-- Selector Bar — Full Width Grid -->
    <form method="GET" action="/marks/internal" class="filter-bar">
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
            <label class="form-label">Exam Type *</label>
            <select name="exam_type" class="form-control" required>
                <option value="cia1" <?= $examType === 'cia1' ? 'selected' : '' ?>>CIA Exam 1</option>
                <option value="cia2" <?= $examType === 'cia2' ? 'selected' : '' ?>>CIA Exam 2</option>
                <option value="cia3" <?= $examType === 'cia3' ? 'selected' : '' ?>>CIA Exam 3</option>
                <option value="assignment" <?= $examType === 'assignment' ? 'selected' : '' ?>>Assignment</option>
                <option value="practical" <?= $examType === 'practical' ? 'selected' : '' ?>>Practical Lab</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;"><?= icon('search', 'icon-xs') ?> Load Marks Roster</button>
        </div>
    </form>
</div>

<?php if ($sectionId > 0 && $subjectId > 0): ?>
    <div class="card" style="width: 100%;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('clipboard-list', 'icon-sm') ?> Enter Student Marks &amp; Grade Log
            </h2>
            <span class="badge badge-info" style="font-weight: 700; text-transform: uppercase;">Exam: <?= strtoupper(e($examType)) ?></span>
        </div>

        <?php if (empty($students)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0; padding: 1rem 0;">No active students enrolled in this section.</p>
        <?php else: ?>
            <form method="POST" action="/marks/internal">
                <?= csrf_field() ?>
                <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                <input type="hidden" name="subject_id" value="<?= $subjectId ?>">
                <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                <input type="hidden" name="exam_type" value="<?= e($examType) ?>">

                <div style="margin-bottom: 1.25rem; width: 220px;">
                    <label class="form-label">Maximum Assessment Marks</label>
                    <input type="number" name="max_marks" class="form-control" value="25" min="5" max="100">
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Roll Number</th>
                                <th>Student Name</th>
                                <th style="text-align: right;">Marks Obtained</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $st): ?>
                                <tr>
                                    <td style="font-weight: 800; color: var(--accent-color);"><?= e($st['roll_number']) ?></td>
                                    <td style="font-weight: 700; color: var(--text-primary);"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                                    <td style="text-align: right;">
                                        <input type="number" step="0.5" name="marks[<?= $st['id'] ?>]" class="form-control" style="width: 140px; display: inline-block; text-align: right; font-weight: 700;" placeholder="0.0" min="0" max="100">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1.5rem; text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.875rem 2.5rem; font-weight: 700;"><?= icon('save', 'icon-sm') ?> Save Internal Marks</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

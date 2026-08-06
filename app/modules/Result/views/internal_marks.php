<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Internal Marks Entry</h2>
    </div>

    <!-- Selector Bar -->
    <form method="GET" action="/marks/internal" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 1.5rem;">
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
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Load Roster</button>
        </div>
    </form>
</div>

<?php if ($sectionId > 0 && $subjectId > 0): ?>
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Enter Internal Marks</h2>
            <span class="badge badge-info">Exam: <?= strtoupper(e($examType)) ?></span>
        </div>

        <?php if (empty($students)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">No active students enrolled in this section.</p>
        <?php else: ?>
            <form method="POST" action="/marks/internal">
                <?= csrf_field() ?>
                <input type="hidden" name="section_id" value="<?= $sectionId ?>">
                <input type="hidden" name="subject_id" value="<?= $subjectId ?>">
                <input type="hidden" name="academic_year_id" value="<?= $academicYearId ?>">
                <input type="hidden" name="exam_type" value="<?= e($examType) ?>">

                <div style="margin-bottom: 1rem; width: 200px;">
                    <label class="form-label">Max Marks</label>
                    <input type="number" name="max_marks" class="form-control" value="25" min="5" max="100">
                </div>

                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                            <th style="padding: 0.75rem;">Roll No</th>
                            <th style="padding: 0.75rem;">Student Name</th>
                            <th style="padding: 0.75rem;">Marks Obtained</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $st): ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($st['roll_number']) ?></td>
                                <td style="padding: 0.75rem; font-weight: 600;"><?= e($st['first_name'] . ' ' . $st['last_name']) ?></td>
                                <td style="padding: 0.75rem;">
                                    <input type="number" step="0.5" name="marks[<?= $st['id'] ?>]" class="form-control" style="width: 120px;" placeholder="0.0" min="0" max="100">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 1.5rem; text-align: right;">
                    <button type="submit" class="btn-primary" style="width: auto; padding: 0.75rem 2.5rem;">Save Internal Marks</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

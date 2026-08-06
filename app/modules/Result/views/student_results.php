<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin: 0;">My Semester Results & Marksheets</h1>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
            Official academic performance report cards for all current and previous semesters.
        </p>
    </div>
    <div>
        <button onclick="window.print()" class="btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem;">
            <span>🖨️</span> Print / Download Marksheets
        </button>
    </div>
</div>

<?php if (empty($semesters)): ?>
    <div class="card" style="padding: 2rem; text-align: center;">
        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🏆</div>
        <h2 style="font-size: 1.25rem; color: var(--text-primary); margin-top: 0;">No Semester Results Published Yet</h2>
        <p style="color: var(--text-secondary); font-size: 0.875rem; max-width: 500px; margin: 0.5rem auto 0;">
            Your semester examination results are either currently being evaluated or pending official publishing by the examination cell.
        </p>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <?php foreach ($semesters as $sem): ?>
            <div class="card">
                <!-- Semester Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--accent-color); letter-spacing: 0.05em;"><?= e($sem['course_name']) ?></span>
                        <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin: 0.25rem 0 0;"><?= e($sem['semester_name']) ?> — <?= e($sem['academic_year_name']) ?></h2>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <div style="text-align: right;">
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Total Marks / Grade</div>
                            <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);"><?= number_format($sem['total_marks'], 1) ?> <span style="font-size: 0.875rem; color: var(--accent-color);">(<?= e($sem['grade']) ?>)</span></div>
                        </div>

                        <div>
                            <?php if ($sem['result'] === 'pass'): ?>
                                <span class="badge badge-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                    PASSED
                                </span>
                            <?php else: ?>
                                <span class="badge badge-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                    FAILED / BACKLOG
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Subject Marks Table -->
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Title</th>
                                <th style="text-align: center;">Credits</th>
                                <th style="text-align: center;">Internal CIA Marks</th>
                                <th style="text-align: center;">External Exam Marks</th>
                                <th style="text-align: right;">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sem['subjects'])): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-secondary);">Detailed subject marks breakdown pending.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sem['subjects'] as $sub): ?>
                                    <tr>
                                        <td style="font-weight: 600; color: var(--accent-color);"><?= e($sub['subject_code']) ?></td>
                                        <td style="color: var(--text-primary); font-weight: 500;"><?= e($sub['subject_name']) ?></td>
                                        <td style="text-align: center; color: var(--text-secondary);"><?= number_format((float)$sub['credits'], 1) ?></td>
                                        <td style="text-align: center; color: var(--text-primary);"><?= $sub['internal_marks'] !== null ? number_format((float)$sub['internal_marks'], 1) : 'N/A' ?></td>
                                        <td style="text-align: center; color: var(--text-primary);"><?= number_format((float)$sub['external_marks'], 1) ?> / <?= number_format((float)$sub['external_max'], 0) ?></td>
                                        <td style="text-align: right;">
                                            <span style="font-weight: 700; color: <?= $sub['grade'] === 'F' ? 'var(--danger)' : 'var(--success)' ?>; font-size: 0.9375rem;">
                                                <?= e($sub['grade']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

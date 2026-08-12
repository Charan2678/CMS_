<div class="dashboard-grid-equal" style="width: 100%;">
    <!-- Department Breakdown -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('building-2', 'icon-sm') ?> Student Enrollment by Department
        </h3>

        <div class="table-responsive" style="width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Department Code</th>
                        <th>Department Name</th>
                        <th style="text-align: right;">Enrolled Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($departmentEnrollments)): ?>
                        <tr>
                            <td colspan="3" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No department data available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($departmentEnrollments as $d): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($d['department_code']) ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($d['department_name']) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--success);"><?= number_format((int)$d['student_count']) ?> Students</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Course Breakdown -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('graduation-cap', 'icon-sm') ?> Student Enrollment by Degree Program
        </h3>

        <div class="table-responsive" style="width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th style="text-align: right;">Enrolled Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courseEnrollments)): ?>
                        <tr>
                            <td colspan="3" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No course data available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courseEnrollments as $c): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($c['course_code']) ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($c['course_name']) ?></td>
                                <td style="text-align: right; font-weight: 800; color: var(--success);"><?= number_format((int)$c['student_count']) ?> Students</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

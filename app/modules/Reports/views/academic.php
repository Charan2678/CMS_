<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <!-- Department Breakdown -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Enrollment by Department</h3>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Department Code</th>
                    <th style="padding: 0.75rem;">Department Name</th>
                    <th style="padding: 0.75rem; text-align: right;">Enrolled Students</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($departmentEnrollments)): ?>
                    <tr>
                        <td colspan="3" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No department data available.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($departmentEnrollments as $d): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($d['department_code']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($d['department_name']) ?></td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #86efac;"><?= number_format((int)$d['student_count']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Course Breakdown -->
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Enrollment by Degree Course</h3>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Course Code</th>
                    <th style="padding: 0.75rem;">Course Name</th>
                    <th style="padding: 0.75rem; text-align: right;">Enrolled Students</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courseEnrollments)): ?>
                    <tr>
                        <td colspan="3" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No course data available.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courseEnrollments as $c): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($c['course_code']) ?></td>
                            <td style="padding: 0.75rem;"><?= e($c['course_name']) ?></td>
                            <td style="padding: 0.75rem; text-align: right; font-weight: 700; color: #86efac;"><?= number_format((int)$c['student_count']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

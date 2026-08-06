<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Attendance Shortage & Audit Report</h2>
        <span class="badge badge-warning">Shortage Threshold: &lt; 75%</span>
    </div>

    <!-- Section Filter -->
    <form method="GET" action="/reports/attendance" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1.5rem; max-width: 540px;">
        <div>
            <label class="form-label">Select Section to Audit *</label>
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
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0;">Run Shortage Audit</button>
        </div>
    </form>
</div>

<?php if ($sectionId > 0): ?>
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Students Deficient in Attendance (&lt; 75%)</h3>
        </div>

        <?php if (empty($shortageList)): ?>
            <p style="color: #86efac; font-size: 0.875rem; font-weight: 600;">✅ Excellent! No students in this section currently fall below the 75% attendance threshold.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                        <th style="padding: 0.75rem;">Roll No</th>
                        <th style="padding: 0.75rem;">Student Name</th>
                        <th style="padding: 0.75rem;">Attended / Total Classes</th>
                        <th style="padding: 0.75rem;">Attendance Percentage</th>
                        <th style="padding: 0.75rem;">Action Required</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shortageList as $s): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;"><?= e($s['roll_number']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 600;"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($s['present_classes']) ?> / <?= e($s['total_classes']) ?> Classes</td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #fca5a5;"><?= e($s['attendance_pct']) ?>%</td>
                            <td style="padding: 0.75rem;">
                                <span class="badge badge-danger">Condonation Warning Issued</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

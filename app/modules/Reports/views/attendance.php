<div class="card" style="margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
                <?= icon('calendar-check', 'icon-md') ?> Attendance Shortage &amp; Audit Report
            </h2>
            <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Audit student presence and identify students below mandatory attendance criteria
            </div>
        </div>
        <span class="badge badge-warning"><?= icon('alert-triangle', 'icon-xs') ?> Shortage Threshold: &lt; 75%</span>
    </div>

    <!-- Section Filter -->
    <form method="GET" action="/reports/attendance" class="filter-bar" style="max-width: 540px;">
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
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 0; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;">
                <?= icon('filter', 'icon-xs') ?> Run Shortage Audit
            </button>
        </div>
    </form>
</div>

<?php if ($sectionId > 0): ?>
    <div class="card">
        <div style="margin-bottom: 1.25rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                <?= icon('alert-circle', 'icon-sm') ?> Students Deficient in Attendance (&lt; 75%)
            </h3>
        </div>

        <?php if (empty($shortageList)): ?>
            <p style="color: var(--success); font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem; padding: 1rem 0;">
                <?= icon('check-circle-2', 'icon-sm') ?> Excellent! No students in this section currently fall below the 75% attendance threshold.
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Attended / Total Classes</th>
                            <th>Attendance Percentage</th>
                            <th>Action Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shortageList as $s): ?>
                            <tr>
                                <td style="font-weight: 800; color: var(--accent-color);"><?= e($s['roll_number']) ?></td>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                <td style="color: var(--text-secondary);"><?= e($s['present_classes']) ?> / <?= e($s['total_classes']) ?> Classes</td>
                                <td style="font-weight: 800; color: var(--danger);"><?= e($s['attendance_pct']) ?>%</td>
                                <td>
                                    <span class="badge badge-danger"><?= icon('alert-triangle', 'icon-xs') ?> Condonation Warning</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="page-title" style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.015em;">
            <?= icon('calendar-check', 'icon-md') ?> My Attendance Dashboard
        </h1>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
            Real-time tracking of your attendance standing, subject-wise statistics, and daily class records.
        </p>
    </div>
    <div>
        <?php 
            $pct = $summary['percentage'] ?? 100;
            $badgeColor = $pct >= 75 ? 'var(--success)' : 'var(--danger)';
            $badgeBg = $pct >= 75 ? 'rgba(16, 185, 129, 0.12)' : 'rgba(239, 68, 68, 0.12)';
            $standingText = $pct >= 75 ? 'Good Standing (Eligible for Exams)' : 'Shortage Alert (< 75% Requirement)';
            $standingIcon = $pct >= 75 ? 'check-circle-2' : 'alert-triangle';
        ?>
        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: <?= $badgeBg ?>; border: 1px solid <?= $badgeColor ?>; border-radius: 8px; color: <?= $badgeColor ?>; font-weight: 700; font-size: 0.8125rem; box-shadow: var(--shadow-xs);">
            <?= icon($standingIcon, 'icon-xs') ?> <?= $standingText ?>
        </span>
    </div>
</div>

<!-- Overall Summary Stat Cards -->
<div class="grid-metrics">
    <div class="metric-card">
        <div class="metric-icon">
            <?= icon('bar-chart-3', 'icon-lg') ?>
        </div>
        <div>
            <div class="metric-label">Overall Percentage</div>
            <div class="metric-value" style="color: <?= $badgeColor ?>;"><?= number_format($summary['percentage'], 1) ?>%</div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon" style="background: rgba(16, 185, 129, 0.12); color: var(--success); border-color: rgba(16, 185, 129, 0.25);">
            <?= icon('check-circle-2', 'icon-lg') ?>
        </div>
        <div>
            <div class="metric-label">Classes Attended</div>
            <div class="metric-value"><?= $summary['total_present'] ?> / <?= $summary['total_conducted'] ?></div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon" style="background: rgba(239, 68, 68, 0.12); color: var(--danger); border-color: rgba(239, 68, 68, 0.25);">
            <?= icon('x-circle', 'icon-lg') ?>
        </div>
        <div>
            <div class="metric-label">Classes Missed</div>
            <div class="metric-value" style="color: var(--danger);"><?= $summary['total_absent'] ?></div>
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-icon" style="background: rgba(245, 158, 11, 0.12); color: var(--warning); border-color: rgba(245, 158, 11, 0.25);">
            <?= icon('clock', 'icon-lg') ?>
        </div>
        <div>
            <div class="metric-label">Late Arrivals</div>
            <div class="metric-value" style="color: var(--warning);"><?= $summary['total_late'] ?></div>
        </div>
    </div>
</div>

<!-- Subject-Wise Breakdown Table -->
<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <?= icon('book-open', 'icon-sm') ?> Subject-Wise Attendance Breakdown
    </h2>

    <?php if (empty($subjects)): ?>
        <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">No attendance recorded for your subjects yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Title</th>
                        <th>Type</th>
                        <th style="text-align: center;">Conducted</th>
                        <th style="text-align: center;">Attended</th>
                        <th style="text-align: center;">Percentage</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $s): ?>
                        <?php 
                            $spct = $s['percentage'];
                            $pColor = $spct >= 75 ? 'var(--success)' : ($spct >= 65 ? 'var(--warning)' : 'var(--danger)');
                        ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--accent-color);"><?= e($s['subject_code']) ?></td>
                            <td style="color: var(--text-primary); font-weight: 600;"><?= e($s['subject_name']) ?></td>
                            <td style="color: var(--text-secondary); text-transform: capitalize;"><?= e($s['subject_type']) ?></td>
                            <td style="text-align: center; color: var(--text-primary); font-weight: 600;"><?= $s['conducted'] ?></td>
                            <td style="text-align: center; color: var(--text-primary); font-weight: 600;"><?= $s['present'] + $s['late'] ?></td>
                            <td style="text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                    <div style="width: 80px; height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden;">
                                        <div style="width: <?= min(100, $spct) ?>%; height: 100%; background: <?= $pColor ?>;"></div>
                                    </div>
                                    <span style="font-weight: 700; color: <?= $pColor ?>; font-size: 0.875rem;"><?= number_format($spct, 1) ?>%</span>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($spct >= 75): ?>
                                    <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> Safe</span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><?= icon('alert-triangle', 'icon-xs') ?> Shortage Risk</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Daily Attendance Log Section (Automatically displayed) -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('calendar-range', 'icon-sm') ?> Daily Class Attendance History
        </h2>

        <!-- Optional Filter Form -->
        <form method="GET" action="/attendance" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <select name="month" class="form-control" style="width: auto; padding: 0.4rem 0.75rem; font-size: 0.8125rem;">
                <option value="">All Months</option>
                <?php 
                    $currM = date('Y-m');
                    for ($i=0; $i<6; $i++) {
                        $mVal = date('Y-m', strtotime("-$i months"));
                        $mLabel = date('F Y', strtotime("-$i months"));
                        $sel = ($selectedMonth === $mVal) ? 'selected' : '';
                        echo "<option value=\"{$mVal}\" {$sel}>{$mLabel}</option>";
                    }
                ?>
            </select>

            <select name="subject_id" class="form-control" style="width: auto; padding: 0.4rem 0.75rem; font-size: 0.8125rem;">
                <option value="0">All Subjects</option>
                <?php foreach ($subjects as $sub): ?>
                    <option value="<?= $sub['subject_id'] ?>" <?= $selectedSubject == $sub['subject_id'] ? 'selected' : '' ?>>
                        <?= e($sub['subject_code']) ?> — <?= e($sub['subject_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-primary" style="padding: 0.4rem 0.875rem; font-size: 0.8125rem;"><?= icon('filter', 'icon-xs') ?> Filter</button>
            <?php if (!empty($selectedMonth) || $selectedSubject > 0): ?>
                <a href="/attendance" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8125rem; margin-left: 0.25rem;">Clear Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($dailyLog)): ?>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No attendance records found for the selected filter.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Academic Year</th>
                        <th>Section</th>
                        <th style="text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailyLog as $log): ?>
                        <tr>
                            <td style="color: var(--text-primary); font-weight: 600;"><?= date('D, d M Y', strtotime($log['date'])) ?></td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary);"><?= e($log['subject_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--accent-color);"><?= e($log['subject_code']) ?></div>
                            </td>
                            <td style="color: var(--text-secondary);"><?= e($log['academic_year'] ?? 'N/A') ?></td>
                            <td style="color: var(--text-secondary);"><?= e($log['section_name'] ?? 'N/A') ?></td>
                            <td style="text-align: right;">
                                <?php if ($log['status'] === 'present'): ?>
                                    <span class="badge badge-success"><?= icon('check-circle-2', 'icon-xs') ?> Present</span>
                                <?php elseif ($log['status'] === 'absent'): ?>
                                    <span class="badge badge-danger"><?= icon('x-circle', 'icon-xs') ?> Absent</span>
                                <?php elseif ($log['status'] === 'late'): ?>
                                    <span class="badge badge-warning"><?= icon('clock', 'icon-xs') ?> Late</span>
                                <?php elseif ($log['status'] === 'on_leave'): ?>
                                    <span class="badge badge-info" style="background: rgba(147, 51, 234, 0.12); color: #a855f7; border: 1px solid rgba(147, 51, 234, 0.3);"><?= icon('file-signature', 'icon-xs') ?> On Leave</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><?= ucfirst($log['status']) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

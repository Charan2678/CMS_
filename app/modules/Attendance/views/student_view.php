<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <div>
        <h1 class="page-title" style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); margin: 0;">My Attendance Dashboard</h1>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem;">
            Real-time tracking of your attendance standing, subject-wise statistics, and daily class records.
        </p>
    </div>
    <div>
        <?php 
            $pct = $summary['percentage'] ?? 100;
            $badgeColor = $pct >= 75 ? 'var(--success)' : 'var(--danger)';
            $badgeBg = $pct >= 75 ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)';
            $standingText = $pct >= 75 ? 'Good Standing (Eligible for Exams)' : 'Shortage Alert (< 75% Requirement)';
        ?>
        <span style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: <?= $badgeBg ?>; border: 1px solid <?= $badgeColor ?>; border-radius: 8px; color: <?= $badgeColor ?>; font-weight: 600; font-size: 0.875rem;">
            <?= $standingText ?>
        </span>
    </div>
</div>

<!-- Overall Summary Stat Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="metric-card">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(2, 132, 199, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--accent-color);">
            📊
        </div>
        <div>
            <div class="metric-label">Overall Percentage</div>
            <div class="metric-value" style="color: <?= $badgeColor ?>;"><?= number_format($summary['percentage'], 1) ?>%</div>
        </div>
    </div>

    <div class="metric-card">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--success);">
            ✅
        </div>
        <div>
            <div class="metric-label">Classes Attended</div>
            <div class="metric-value"><?= $summary['total_present'] ?> / <?= $summary['total_conducted'] ?></div>
        </div>
    </div>

    <div class="metric-card">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(239, 68, 68, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--danger);">
            ❌
        </div>
        <div>
            <div class="metric-label">Classes Missed</div>
            <div class="metric-value" style="color: var(--danger);"><?= $summary['total_absent'] ?></div>
        </div>
    </div>

    <div class="metric-card">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--warning);">
            ⏰
        </div>
        <div>
            <div class="metric-label">Late Arrivals</div>
            <div class="metric-value" style="color: var(--warning);"><?= $summary['total_late'] ?></div>
        </div>
    </div>
</div>

<!-- Subject-Wise Breakdown Table -->
<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>📚</span> Subject-Wise Attendance Breakdown
    </h2>

    <?php if (empty($subjects)): ?>
        <p style="color: var(--text-secondary); margin: 0; font-size: 0.875rem;">No attendance recorded for your subjects yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
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
                            <td style="font-weight: 600; color: var(--accent-color);"><?= e($s['subject_code']) ?></td>
                            <td style="color: var(--text-primary); font-weight: 500;"><?= e($s['subject_name']) ?></td>
                            <td style="color: var(--text-secondary); text-transform: capitalize;"><?= e($s['subject_type']) ?></td>
                            <td style="text-align: center; color: var(--text-primary);"><?= $s['conducted'] ?></td>
                            <td style="text-align: center; color: var(--text-primary);"><?= $s['present'] + $s['late'] ?></td>
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
                                    <span class="badge badge-success">Safe</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Shortage Risk</span>
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
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span>🗓️</span> Daily Class Attendance History
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

            <button type="submit" class="btn-primary" style="padding: 0.4rem 0.875rem; font-size: 0.8125rem;">Filter</button>
            <?php if (!empty($selectedMonth) || $selectedSubject > 0): ?>
                <a href="/attendance" style="color: var(--text-secondary); text-decoration: none; font-size: 0.8125rem; margin-left: 0.25rem;">Clear Filter</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($dailyLog)): ?>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No attendance records found for the selected filter.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
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
                            <td style="color: var(--text-primary); font-weight: 500;"><?= date('D, d M Y', strtotime($log['date'])) ?></td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-primary);"><?= e($log['subject_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--accent-color);"><?= e($log['subject_code']) ?></div>
                            </td>
                            <td style="color: var(--text-secondary);"><?= e($log['academic_year'] ?? 'N/A') ?></td>
                            <td style="color: var(--text-secondary);"><?= e($log['section_name'] ?? 'N/A') ?></td>
                            <td style="text-align: right;">
                                <?php if ($log['status'] === 'present'): ?>
                                    <span class="badge badge-success">Present</span>
                                <?php elseif ($log['status'] === 'absent'): ?>
                                    <span class="badge badge-danger">Absent</span>
                                <?php elseif ($log['status'] === 'late'): ?>
                                    <span class="badge badge-warning">Late</span>
                                <?php elseif ($log['status'] === 'on_leave'): ?>
                                    <span class="badge badge-info" style="background: rgba(147, 51, 234, 0.15); color: #a855f7; border: 1px solid rgba(147, 51, 234, 0.3);">On Leave</span>
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

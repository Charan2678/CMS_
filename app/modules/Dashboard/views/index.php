<?php
$role = auth_role();
$sData = $studentData ?? [];
$att = $sData['attendance'] ?? ['percentage' => 0, 'total_conducted' => 0, 'total_present' => 0, 'total_absent' => 0];
$fee = $sData['fee'] ?? ['total_payable' => 0, 'total_paid' => 0, 'balance_due' => 0];
$ancList = $announcements ?? [];
$canteenOrders = $sData['canteen'] ?? [];

$attPercentage = (float) ($att['percentage'] ?? 0);
$isShortage = $attPercentage < 75.0;
$feeBalance = (float) ($fee['balance_due'] ?? 0);
?>

<?php if ($role === 'parent'): ?>
    <?php $ward = $sData['ward_info'] ?? null; ?>
    <div style="background: var(--bg-peach); border: 1px solid var(--orange-border); border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: var(--shadow-sm);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--orange-gradient); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: var(--shadow-glow-primary);">
                <?= icon('users', 'icon-lg') ?>
            </div>
            <div>
                <div style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.725rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--orange-accent); font-weight: 800;">Parent &amp; Guardian Portal</div>
                <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; margin: 0.15rem 0 0 0; font-size: 1.2rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.015em;">
                    Monitoring Ward: <?= e($ward ? trim($ward['first_name'] . ' ' . $ward['last_name']) : ($_SESSION['ward_name'] ?? 'Enrolled Student')) ?>
                </h2>
                <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                    Roll Number: <strong style="color: var(--text-primary);"><?= e($ward['roll_number'] ?? ($_SESSION['ward_roll_number'] ?? 'N/A')) ?></strong> &bull; 
                    Course: <strong style="color: var(--text-primary);"><?= e($ward['course_name'] ?? 'B.Tech CSE') ?> (Sem <?= e($ward['semester_number'] ?? '1') ?><?= !empty($ward['section_name']) ? ' - ' . e($ward['section_name']) : '' ?>)</strong>
                </div>
            </div>
        </div>
        <div>
            <span class="badge badge-green" style="font-size: 0.785rem; padding: 0.4rem 0.8rem;"><?= icon('graduation-cap', 'icon-xs') ?> Active Student Standing</span>
        </div>
    </div>
<?php endif; ?>

<!-- Bento Metric Summary Cards Grid (White Diamond & Light Orange) -->
<div class="grid-metrics">
    <?php if (in_array($role, ['super_admin', 'admin', 'hod', 'staff', 'accounts_staff'])): ?>
        <!-- Card 1: Active Enrolled Students (Light Orange) -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Active Enrolled Students</div>
                <div class="metric-value"><?= number_format($studentCount ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Across all semesters</div>
            </div>
            <div class="metric-icon icon-peach"><?= icon('graduation-cap', 'icon-lg') ?></div>
        </div>

        <!-- Card 2: Faculty & Staff (Success Green) -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Active Faculty &amp; Staff</div>
                <div class="metric-value"><?= number_format(($facultyCount ?? 0) + ($staffCount ?? 0)) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;"><?= number_format($facultyCount ?? 0) ?> Teaching &bull; <?= number_format($staffCount ?? 0) ?> Non-Teaching</div>
            </div>
            <div class="metric-icon icon-green"><?= icon('school', 'icon-lg') ?></div>
        </div>

        <!-- Card 3: Academic Departments (Info Blue) -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Departments &amp; Courses</div>
                <div class="metric-value"><?= number_format($deptCount ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;"><?= number_format($courseCount ?? 0) ?> Degree Programs</div>
            </div>
            <div class="metric-icon icon-info"><?= icon('building', 'icon-lg') ?></div>
        </div>

        <!-- Card 4: Fee Collections YTD (Success Accent) -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Fee Collections YTD</div>
                <div class="metric-value" style="color: var(--success); font-size: 1.65rem;">₹<?= number_format($totalFeeCollected ?? 0, 2) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Total Fee Revenue</div>
            </div>
            <div class="metric-icon icon-peach"><?= icon('wallet', 'icon-lg') ?></div>
        </div>

    <?php elseif (in_array($role, ['student', 'parent'])): ?>
        <!-- Real-Time Attendance Card -->
        <div class="metric-card">
            <div>
                <div class="metric-label"><?= $role === 'parent' ? "Ward's Attendance" : 'Overall Attendance' ?></div>
                <div class="metric-value" style="color: <?= $isShortage ? 'var(--danger)' : 'var(--success)' ?>;">
                    <?= number_format($attPercentage, 1) ?>%
                </div>
                <div style="margin-top: 0.35rem;">
                    <?php if ($isShortage): ?>
                        <span class="badge badge-red"><?= icon('alert-triangle', 'icon-xs') ?> Shortage Alert (&lt;75%)</span>
                    <?php else: ?>
                        <span class="badge badge-green"><?= icon('check-circle-2', 'icon-xs') ?> Safe Standing (&ge;75%)</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="metric-icon <?= $isShortage ? 'icon-red' : 'icon-green' ?>">
                <?= $isShortage ? icon('alert-octagon', 'icon-lg') : icon('shield-check', 'icon-lg') ?>
            </div>
        </div>

        <!-- Real-Time Fee Clearance Card -->
        <div class="metric-card">
            <div>
                <div class="metric-label"><?= $role === 'parent' ? "Ward's Fee Dues" : 'Fee Dues &amp; Clearance' ?></div>
                <div class="metric-value" style="color: <?= $feeBalance > 0 ? 'var(--orange-accent)' : 'var(--success)' ?>;">
                    <?= $feeBalance > 0 ? '₹' . number_format($feeBalance, 2) . ' Due' : 'Paid (No Dues)' ?>
                </div>
                <div style="margin-top: 0.35rem;">
                    <?php if ($feeBalance > 0): ?>
                        <span class="badge badge-peach"><?= icon('credit-card', 'icon-xs') ?> Dues Pending</span>
                    <?php else: ?>
                        <span class="badge badge-green"><?= icon('check-circle-2', 'icon-xs') ?> Fully Cleared</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="metric-icon icon-peach"><?= icon('credit-card', 'icon-lg') ?></div>
        </div>

        <!-- Active Semester Card -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Academic Placement</div>
                <div class="metric-value" style="font-size: 1.35rem;">
                    <?= e($sData['ward_info']['course_name'] ?? 'B.Tech CSE') ?>
                </div>
                <div style="margin-top: 0.35rem;">
                    <span class="badge badge-info"><?= icon('graduation-cap', 'icon-xs') ?> Semester <?= e($sData['ward_info']['semester_number'] ?? '1') ?></span>
                </div>
            </div>
            <div class="metric-icon icon-info"><?= icon('award', 'icon-lg') ?></div>
        </div>

        <!-- College Notices / Active Status -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Campus Announcements</div>
                <div class="metric-value" style="font-size: 1.35rem; color: var(--text-primary);">
                    <?= count($ancList) ?> Active
                </div>
                <div style="margin-top: 0.35rem;">
                    <a href="/announcements" style="font-size: 0.75rem; color: var(--orange-accent); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">View Circulars <?= icon('arrow-right', 'icon-xs') ?></a>
                </div>
            </div>
            <div class="metric-icon icon-peach"><?= icon('megaphone', 'icon-lg') ?></div>
        </div>
    <?php endif; ?>
</div>

<?php if (in_array($role, ['student', 'parent'])): ?>
<!-- Student & Parent Dashboard Main Grid -->
<div class="dashboard-grid-2">
    <!-- Active Announcements Board -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.15rem;">
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('megaphone', 'icon-sm') ?> Campus Live Announcements &amp; Circulars
            </h2>
            <a href="/announcements" style="font-size: 0.8125rem; color: var(--orange-accent); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">View All <?= icon('arrow-right', 'icon-xs') ?></a>
        </div>

        <?php if (empty($ancList)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active campus announcements currently.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                <?php foreach (array_slice($ancList, 0, 3) as $anc): ?>
                    <div style="background: var(--bg-main); padding: 0.95rem 1.15rem; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                            <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?= e($anc['title']) ?></h3>
                            <span class="badge badge-info" style="text-transform: uppercase;">
                                <?= e($anc['target_role'] ?? 'Everyone') ?>
                            </span>
                        </div>
                        <p style="color: var(--text-secondary); font-size: 0.8125rem; line-height: 1.45; margin: 0 0 0.5rem 0;">
                            <?= e(mb_strimwidth($anc['content'], 0, 140, '...')) ?>
                        </p>
                        <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; justify-content: space-between; align-items: center;">
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><?= icon('user', 'icon-xs') ?> Published by <?= e($anc['publisher_name'] ?? 'System Admin') ?></span>
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><?= icon('calendar', 'icon-xs') ?> <?= date('d M Y', strtotime($anc['publish_at'] ?? $anc['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Action Self-Service Portal -->
    <div class="card">
        <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= icon('zap', 'icon-sm') ?> Student Self-Service Portal
        </h2>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="/transport/pass" style="background: var(--bg-peach); border: 1px solid var(--orange-border); padding: 0.85rem 1rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 700; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                <span style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-primary);"><?= icon('id-card', 'icon-sm', ['style' => 'color: var(--orange-accent);']) ?> 🚌 My Bus Pass</span>
                <span class="badge badge-peach">Digital Pass</span>
            </a>

            <a href="/attendance" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                <span style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-primary);"><?= icon('calendar-check', 'icon-sm', ['style' => 'color: var(--success);']) ?> My Attendance Log</span>
                <span class="badge badge-green"><?= number_format($attPercentage, 1) ?>%</span>
            </a>

            <a href="/results" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                <span style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-primary);"><?= icon('file-spreadsheet', 'icon-sm', ['style' => 'color: var(--orange-accent);']) ?> My Semester Results</span>
                <span class="badge badge-peach">Grade Cards</span>
            </a>

            <a href="/fee/payments" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                <span style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-primary);"><?= icon('receipt', 'icon-sm', ['style' => 'color: var(--orange-accent);']) ?> My Fee Receipts</span>
                <span class="badge badge-peach"><?= $feeBalance > 0 ? 'Dues' : 'Paid' ?></span>
            </a>

            <a href="/timetable" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                <span style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-primary);"><?= icon('clock', 'icon-sm', ['style' => 'color: var(--info);']) ?> Class Timetable</span>
                <span class="badge badge-info">Mon-Sat</span>
            </a>

            <a href="/canteen" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.85rem 1rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                <span style="display: flex; align-items: center; gap: 0.6rem; color: var(--text-primary);"><?= icon('utensils', 'icon-sm', ['style' => 'color: var(--orange-accent);']) ?> Canteen Food Order</span>
                <span class="badge badge-peach">Order Online</span>
            </a>
        </div>
    </div>
</div>

<?php else: ?>

<!-- Executive Admin & Staff Command Dashboard Grid -->
<div class="dashboard-grid-2">
    <!-- Left Column: Operations & Department Stats -->
    <div style="display: flex; flex-direction: column; gap: 1.35rem;">
        <!-- Quick Action Shortcuts -->
        <div class="card">
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('zap', 'icon-sm') ?> Operational Shortcuts &amp; Quick Actions
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 0.85rem;">
                <?php if (in_array($role, ['super_admin', 'admin', 'hod'])): ?>
                    <a href="/students/admission" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.9rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                        <?= icon('user-plus', 'icon-md', ['style' => 'color: var(--orange-accent);']) ?> Admit Student
                    </a>
                    <a href="/attendance" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.9rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                        <?= icon('calendar-check', 'icon-md', ['style' => 'color: var(--success);']) ?> Attendance Roster
                    </a>
                    <a href="/fee/payments" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.9rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                        <?= icon('credit-card', 'icon-md', ['style' => 'color: var(--orange-accent);']) ?> Collect Fees
                    </a>
                    <a href="/results" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.9rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                        <?= icon('award', 'icon-md', ['style' => 'color: var(--info);']) ?> Results Engine
                    </a>
                    <a href="/announcements" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.9rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                        <?= icon('megaphone', 'icon-md', ['style' => 'color: var(--orange-accent);']) ?> Announcements
                    </a>
                    <a href="/audit-logs" style="background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.9rem; border-radius: 12px; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem; transition: all 0.18s ease; box-shadow: var(--shadow-xs);">
                        <?= icon('history', 'icon-md', ['style' => 'color: var(--danger);']) ?> Audit Logs
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Departmental Student Distribution Card -->
        <div class="card">
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('building-2', 'icon-sm') ?> Departmental Student Strength
            </h2>

            <?php if (empty($deptStats)): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">No department statistics available.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Department Code</th>
                                <th>Department Name</th>
                                <th style="text-align: right;">Enrolled Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deptStats as $ds): ?>
                                <tr>
                                    <td style="font-weight: 700; color: var(--orange-accent);"><?= e($ds['code']) ?></td>
                                    <td style="font-weight: 600; color: var(--text-primary);"><?= e($ds['name']) ?></td>
                                    <td style="text-align: right; font-weight: 700; color: var(--text-primary);"><?= number_format((int)$ds['student_count']) ?> Students</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Column: Announcements Feed & Audit Log Stream -->
    <div style="display: flex; flex-direction: column; gap: 1.35rem;">
        <!-- Live Announcements Board -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.15rem;">
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <?= icon('megaphone', 'icon-sm') ?> Campus Live Announcements
                </h2>
                <a href="/announcements" style="font-size: 0.8125rem; color: var(--orange-accent); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">View All <?= icon('arrow-right', 'icon-xs') ?></a>
            </div>

            <?php if (empty($ancList)): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active campus announcements currently.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <?php foreach (array_slice($ancList, 0, 3) as $anc): ?>
                        <div style="background: var(--bg-main); padding: 0.85rem 1rem; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: var(--shadow-xs);">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                                <h3 style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?= e($anc['title']) ?></h3>
                                <span class="badge badge-info" style="font-size: 0.6875rem; text-transform: uppercase;">
                                    <?= e($anc['target_role'] ?? 'Everyone') ?>
                                </span>
                            </div>
                            <p style="color: var(--text-secondary); font-size: 0.785rem; line-height: 1.4; margin: 0;">
                                <?= e(mb_strimwidth($anc['content'], 0, 100, '...')) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Audit Log Activity Stream -->
        <?php if (!empty($auditLogs)): ?>
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.15rem;">
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <?= icon('history', 'icon-sm') ?> Recent System Activity
                </h2>
                <a href="/audit-logs" style="font-size: 0.8125rem; color: var(--orange-accent); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">Logs <?= icon('arrow-right', 'icon-xs') ?></a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                <?php foreach ($auditLogs as $log): ?>
                    <div style="background: var(--bg-main); padding: 0.75rem 0.875rem; border-radius: 10px; border: 1px solid var(--border-color); font-size: 0.75rem; box-shadow: var(--shadow-xs);">
                        <div style="display: flex; justify-content: space-between; font-weight: 700; color: var(--text-primary);">
                            <span><?= e($log['username'] ?? 'System') ?></span>
                            <span style="color: var(--text-muted); font-weight: 400;"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                        </div>
                        <div style="color: var(--text-secondary); margin-top: 0.15rem;"><?= e($log['action']) ?> &bull; <?= e($log['entity_type'] ?? '') ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

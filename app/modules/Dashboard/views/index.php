<?php
$role = auth_role();
$sData = $studentData ?? [];
$att = $sData['attendance'] ?? ['percentage' => 0, 'total_conducted' => 0, 'total_present' => 0, 'total_absent' => 0];
$fee = $sData['fee'] ?? ['total_payable' => 0, 'total_paid' => 0, 'balance_due' => 0];
$announcements = $sData['announcements'] ?? [];
$timetable = $sData['timetable'] ?? [];
$allResults = $sData['all_results'] ?? [];
$canteenOrders = $sData['canteen'] ?? [];

$attPercentage = (float) ($att['percentage'] ?? 0);
$isShortage = $attPercentage < 75.0;
$feeBalance = (float) ($fee['balance_due'] ?? 0);
?>

<!-- Metric Summary Cards -->
<div class="grid-metrics" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
    <?php if (in_array($role, ['super_admin', 'admin', 'hod'])): ?>
        <div class="metric-card">
            <div>
                <div class="metric-label">Active Students</div>
                <div class="metric-value"><?= number_format($studentCount ?? 0) ?></div>
            </div>
            <div class="metric-icon">👨‍🎓</div>
        </div>

        <div class="metric-card">
            <div>
                <div class="metric-label">Active Faculty</div>
                <div class="metric-value"><?= number_format($facultyCount ?? 0) ?></div>
            </div>
            <div class="metric-icon">👨‍🏫</div>
        </div>

        <div class="metric-card">
            <div>
                <div class="metric-label">Departments</div>
                <div class="metric-value"><?= number_format($deptCount ?? 0) ?></div>
            </div>
            <div class="metric-icon">🏢</div>
        </div>

        <div class="metric-card">
            <div>
                <div class="metric-label">Active Courses</div>
                <div class="metric-value"><?= number_format($courseCount ?? 0) ?></div>
            </div>
            <div class="metric-icon">🎓</div>
        </div>

    <?php elseif ($role === 'student'): ?>
        <!-- Real-Time Attendance Card with Red Shortage / Green Safe Indicator -->
        <div class="metric-card" style="border-left: 4px solid <?= $isShortage ? 'var(--danger)' : 'var(--success)' ?> !important;">
            <div>
                <div class="metric-label">Overall Attendance</div>
                <div class="metric-value" style="color: <?= $isShortage ? 'var(--danger)' : 'var(--success)' ?>;">
                    <?= number_format($attPercentage, 1) ?>%
                </div>
                <div style="margin-top: 0.35rem;">
                    <?php if ($isShortage): ?>
                        <span class="badge badge-danger">
                            ⚠️ Shortage Alert (&lt;75%)
                        </span>
                    <?php else: ?>
                        <span class="badge badge-success">
                            ✅ Safe Standing (&ge;75%)
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;"><?= $isShortage ? '🚨' : '🛡️' ?></div>
        </div>

        <!-- Real-Time Fee Clearance / Dues Card -->
        <div class="metric-card" style="border-left: 4px solid <?= $feeBalance > 0 ? 'var(--warning)' : 'var(--success)' ?> !important;">
            <div>
                <div class="metric-label">Fee Dues & Clearance</div>
                <div class="metric-value" style="color: <?= $feeBalance > 0 ? 'var(--warning)' : 'var(--success)' ?>;">
                    <?= $feeBalance > 0 ? '₹' . number_format($feeBalance, 2) . ' Due' : 'Paid (No Dues)' ?>
                </div>
                <div style="margin-top: 0.35rem;">
                    <?php if ($feeBalance > 0): ?>
                        <span class="badge badge-warning">
                            💳 Dues Pending
                        </span>
                    <?php else: ?>
                        <span class="badge badge-success">
                            ✨ Fully Cleared
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">💳</div>
        </div>

        <!-- Active Semester & CGPA Card -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Academic Standing</div>
                <div class="metric-value" style="font-size: 1.5rem;">
                    Sem 5 <span style="font-size: 0.9375rem; color: var(--text-secondary);">(CGPA 8.45)</span>
                </div>
                <div style="margin-top: 0.35rem;">
                    <span class="badge badge-info">
                        🎓 B.Tech CSE
                    </span>
                </div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🏆</div>
        </div>

        <!-- Active Canteen Token Status -->
        <div class="metric-card">
            <div>
                <div class="metric-label">Canteen Active Token</div>
                <?php if (!empty($canteenOrders)): ?>
                    <?php $latestOrder = $canteenOrders[0]; ?>
                    <div class="metric-value" style="font-size: 1rem; font-family: monospace; color: var(--accent-color);">
                        <?= e($latestOrder['order_number']) ?>
                    </div>
                    <div style="margin-top: 0.35rem;">
                        <span class="badge badge-info" style="text-transform: uppercase;">
                            🍳 <?= e($latestOrder['order_status']) ?> (<?= e($latestOrder['item_name']) ?>)
                        </span>
                    </div>
                <?php else: ?>
                    <div class="metric-value" style="font-size: 1rem; color: var(--text-secondary);">No Active Order</div>
                    <div style="margin-top: 0.35rem;">
                        <a href="/canteen" style="font-size: 0.75rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">Order Canteen Food &rarr;</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">☕</div>
        </div>
    <?php endif; ?>
</div>

<?php if ($role === 'student'): ?>
<!-- Real-Time Student Dashboard Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Active Announcements Board -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📢</span> Campus Live Announcements & Circulars
            </h2>
            <a href="/announcements" style="font-size: 0.8125rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">View All &rarr;</a>
        </div>

        <?php if (empty($announcements)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active campus announcements currently.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach (array_slice($announcements, 0, 3) as $anc): ?>
                    <div style="background: var(--bg-main); padding: 1rem 1.25rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                            <h3 style="font-size: 0.9375rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?= e($anc['title']) ?></h3>
                            <span class="badge badge-info" style="text-transform: uppercase;">
                                <?= e($anc['target_role'] ?? 'Everyone') ?>
                            </span>
                        </div>
                        <p style="color: var(--text-secondary); font-size: 0.8125rem; line-height: 1.4; margin: 0 0 0.5rem 0;">
                            <?= e(mb_strimwidth($anc['content'], 0, 140, '...')) ?>
                        </p>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); display: flex; justify-content: space-between;">
                            <span>📢 Published by <?= e($anc['publisher_name'] ?? 'System Admin') ?></span>
                            <span>📅 <?= date('d M Y', strtotime($anc['publish_at'] ?? $anc['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Action Self-Service Portal -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>⚡</span> Student Self-Service Portal
        </h2>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="/attendance" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.875rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>✅</span> My Attendance Log</span>
                <span class="badge badge-success"><?= number_format($attPercentage, 1) ?>%</span>
            </a>

            <a href="/results" style="background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(2, 132, 199, 0.25); padding: 0.875rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>🏆</span> My Semester Results</span>
                <span class="badge badge-info">Grade Cards</span>
            </a>

            <a href="/fee/payments" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); padding: 0.875rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>💳</span> My Fee Receipts</span>
                <span class="badge badge-warning"><?= $feeBalance > 0 ? 'Dues' : 'Paid' ?></span>
            </a>

            <a href="/timetable" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25); padding: 0.875rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>🗓️</span> Class Timetable</span>
                <span class="badge badge-info">Mon-Sat</span>
            </a>

            <a href="/canteen" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.25); padding: 0.875rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>☕</span> Canteen Food Order</span>
                <span class="badge badge-info">Order Online</span>
            </a>
        </div>
    </div>
</div>
<?php else: ?>

<!-- Quick Action Shortcuts for Non-Students -->
<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">
            <?php if ($role === 'faculty'): ?>
                Faculty Portal Shortcuts
            <?php elseif ($role === 'staff'): ?>
                Staff Operations Portal
            <?php else: ?>
                Admin Quick Actions & Portal Shortcuts
            <?php endif; ?>
        </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
        <?php if (in_array($role, ['super_admin', 'admin', 'hod'])): ?>
            <a href="/students/admission" style="background: rgba(2, 132, 199, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">➕</span> Admit Student
            </a>
            <a href="/attendance" style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">✅</span> Mark Attendance
            </a>
            <a href="/fee/payments" style="background: rgba(245, 158, 11, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">💳</span> Collect Fees
            </a>
            <a href="/results" style="background: rgba(168, 85, 247, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">🏆</span> Results Engine
            </a>
            <a href="/announcements" style="background: rgba(59, 130, 246, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">📢</span> Announcements
            </a>

        <?php elseif ($role === 'faculty'): ?>
            <a href="/attendance" style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">✅</span> Mark Attendance
            </a>
            <a href="/marks/internal" style="background: rgba(2, 132, 199, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">📝</span> Enter Internal Marks
            </a>
            <a href="/timetable" style="background: rgba(168, 85, 247, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">🗓️</span> Class Timetable
            </a>
            <a href="/announcements" style="background: rgba(59, 130, 246, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">📢</span> View Announcements
            </a>

        <?php elseif ($role === 'staff'): ?>
            <a href="/fee/payments" style="background: rgba(245, 158, 11, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">💳</span> Fee Payments
            </a>
            <a href="/accounts" style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">💵</span> Accounts & Payroll
            </a>
            <a href="/library" style="background: rgba(2, 132, 199, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">📖</span> Library Catalog
            </a>
            <a href="/hostel" style="background: rgba(168, 85, 247, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">🏢</span> Hostel Management
            </a>
            <a href="/transport" style="background: rgba(59, 130, 246, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="font-size: 1.25rem;">🚌</span> Transport Routes
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

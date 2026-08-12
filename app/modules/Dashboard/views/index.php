<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Dashboard\views\index.php
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
    <div style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(14, 165, 233, 0.1) 100%); border: 1px solid rgba(37, 99, 235, 0.3); border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--accent-color); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">
                👨‍👩‍👧
            </div>
            <div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent-color); font-weight: 700;">Parent &amp; Guardian Portal</div>
                <h2 style="margin: 0.15rem 0 0 0; font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
                    Monitoring Ward: <?= e($ward ? trim($ward['first_name'] . ' ' . $ward['last_name']) : ($_SESSION['ward_name'] ?? 'Enrolled Student')) ?>
                </h2>
                <div style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">
                    Roll Number: <strong style="color: var(--text-primary);"><?= e($ward['roll_number'] ?? ($_SESSION['ward_roll_number'] ?? 'N/A')) ?></strong> &bull; 
                    Course: <strong style="color: var(--text-primary);"><?= e($ward['course_name'] ?? 'B.Tech CSE') ?> (Sem <?= e($ward['semester_number'] ?? '1') ?><?= !empty($ward['section_name']) ? ' - ' . e($ward['section_name']) : '' ?>)</strong>
                </div>
            </div>
        </div>
        <div>
            <span class="badge badge-success" style="font-size: 0.875rem; padding: 0.5rem 1rem;">🎓 Active Student Standing</span>
        </div>
    </div>
<?php endif; ?>

<!-- Metric Summary Cards — Full Width Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; width: 100%;">
    <?php if ($role === 'tpo'): ?>
        <!-- TPO Card 1: Placement Drives -->
        <div class="metric-card" style="border-left: 4px solid var(--accent-color) !important;">
            <div>
                <div class="metric-label">Placement Drives</div>
                <div class="metric-value"><?= number_format($tpoData['total_drives'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Active company drives YTD</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">💼</div>
        </div>

        <!-- TPO Card 2: Eligible Students -->
        <div class="metric-card" style="border-left: 4px solid var(--success) !important;">
            <div>
                <div class="metric-label">Eligible Students</div>
                <div class="metric-value" style="color: var(--success);"><?= number_format($tpoData['eligible_count'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Students matching eligibility</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🎓</div>
        </div>

        <!-- TPO Card 3: Highest Package -->
        <div class="metric-card" style="border-left: 4px solid var(--warning) !important;">
            <div>
                <div class="metric-label">Highest Package</div>
                <div class="metric-value" style="color: var(--warning);"><?= e($tpoData['highest_package'] ?? '0.0 LPA') ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Top salary package secured</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🏆</div>
        </div>

        <!-- TPO Card 4: Average Package -->
        <div class="metric-card" style="border-left: 4px solid #8b5cf6 !important;">
            <div>
                <div class="metric-label">Average Package</div>
                <div class="metric-value" style="color: #8b5cf6;"><?= e($tpoData['average_package'] ?? '0.0 LPA') ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Mean placement package</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">📊</div>
        </div>

    <?php elseif ($role === 'head_of_coe'): ?>
        <!-- COE Card 1: Total Hall Tickets -->
        <div class="metric-card" style="border-left: 4px solid var(--accent-color) !important;">
            <div>
                <div class="metric-label">Total Hall Tickets</div>
                <div class="metric-value"><?= number_format($coeData['total_hall_tickets'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Generated current cycle</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🎫</div>
        </div>

        <!-- COE Card 2: Eligible Students -->
        <div class="metric-card" style="border-left: 4px solid var(--success) !important;">
            <div>
                <div class="metric-label">Eligible Students</div>
                <div class="metric-value" style="color: var(--success);"><?= number_format($coeData['eligible_hall_tickets'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Cleared for examination</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">✅</div>
        </div>

        <!-- COE Card 3: Blocked Students -->
        <div class="metric-card" style="border-left: 4px solid var(--danger) !important;">
            <div>
                <div class="metric-label">Blocked Students</div>
                <div class="metric-value" style="color: var(--danger);"><?= number_format($coeData['blocked_hall_tickets'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Dues or low attendance</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🚨</div>
        </div>

        <!-- COE Card 4: Published Results -->
        <div class="metric-card" style="border-left: 4px solid #8b5cf6 !important;">
            <div>
                <div class="metric-label">Published Results</div>
                <div class="metric-value" style="color: #8b5cf6;"><?= number_format($coeData['published_results'] ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Published semester records</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🏆</div>
        </div>

    <?php elseif (in_array($role, ['super_admin', 'admin', 'hod', 'staff', 'accounts_staff'])): ?>
        <!-- Card 1: Active Enrolled Students -->
        <div class="metric-card" style="border-left: 4px solid var(--accent-color) !important;">
            <div>
                <div class="metric-label">Active Enrolled Students</div>
                <div class="metric-value"><?= number_format($studentCount ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Across all semesters</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">👨‍🎓</div>
        </div>

        <!-- Card 2: Faculty & Staff -->
        <div class="metric-card" style="border-left: 4px solid var(--success) !important;">
            <div>
                <div class="metric-label">Active Faculty & Staff</div>
                <div class="metric-value"><?= number_format(($facultyCount ?? 0) + ($staffCount ?? 0)) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;"><?= number_format($facultyCount ?? 0) ?> Teaching &bull; <?= number_format($staffCount ?? 0) ?> Non-Teaching</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">👨‍🏫</div>
        </div>

        <!-- Card 3: Academic Departments -->
        <div class="metric-card" style="border-left: 4px solid var(--warning) !important;">
            <div>
                <div class="metric-label">Departments & Courses</div>
                <div class="metric-value"><?= number_format($deptCount ?? 0) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;"><?= number_format($courseCount ?? 0) ?> Degree Programs</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🏢</div>
        </div>

        <!-- Card 4: Fee Collections YTD -->
        <div class="metric-card" style="border-left: 4px solid #8b5cf6 !important;">
            <div>
                <div class="metric-label">Fee Collections YTD</div>
                <div class="metric-value" style="color: var(--success); font-size: 1.5rem;">₹<?= number_format($totalFeeCollected ?? 0, 2) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">Total Fee Revenue</div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">💳</div>
        </div>

    <?php elseif (in_array($role, ['student', 'parent'])): ?>
        <!-- Real-Time Attendance Card -->
        <div class="metric-card" style="border-left: 4px solid <?= $isShortage ? 'var(--danger)' : 'var(--success)' ?> !important;">
            <div>
                <div class="metric-label"><?= $role === 'parent' ? "Ward's Attendance" : 'Overall Attendance' ?></div>
                <div class="metric-value" style="color: <?= $isShortage ? 'var(--danger)' : 'var(--success)' ?>;">
                    <?= number_format($attPercentage, 1) ?>%
                </div>
                <div style="margin-top: 0.35rem;">
                    <?php if ($isShortage): ?>
                        <span class="badge badge-danger">⚠️ Shortage Alert (&lt;75%)</span>
                    <?php else: ?>
                        <span class="badge badge-success">✅ Safe Standing (&ge;75%)</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;"><?= $isShortage ? '🚨' : '🛡️' ?></div>
        </div>

        <!-- Real-Time Fee Clearance Card -->
        <div class="metric-card" style="border-left: 4px solid <?= $feeBalance > 0 ? 'var(--warning)' : 'var(--success)' ?> !important;">
            <div>
                <div class="metric-label"><?= $role === 'parent' ? "Ward's Fee Dues" : 'Fee Dues & Clearance' ?></div>
                <div class="metric-value" style="color: <?= $feeBalance > 0 ? 'var(--warning)' : 'var(--success)' ?>;">
                    <?= $feeBalance > 0 ? '₹' . number_format($feeBalance, 2) . ' Due' : 'Paid (No Dues)' ?>
                </div>
                <div style="margin-top: 0.35rem;">
                    <?php if ($feeBalance > 0): ?>
                        <span class="badge badge-warning">💳 Dues Pending</span>
                    <?php else: ?>
                        <span class="badge badge-success">✨ Fully Cleared</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">💳</div>
        </div>

        <!-- Active Semester Card -->
        <div class="metric-card" style="border-left: 4px solid var(--accent-color) !important;">
            <div>
                <div class="metric-label">Academic Placement</div>
                <div class="metric-value" style="font-size: 1.35rem;">
                    <?= e($sData['ward_info']['course_name'] ?? 'B.Tech CSE') ?>
                </div>
                <div style="margin-top: 0.35rem;">
                    <span class="badge badge-info">🎓 Semester <?= e($sData['ward_info']['semester_number'] ?? '1') ?></span>
                </div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">🏆</div>
        </div>

        <!-- College Notices / Active Status -->
        <div class="metric-card" style="border-left: 4px solid var(--warning) !important;">
            <div>
                <div class="metric-label">Campus Announcements</div>
                <div class="metric-value" style="font-size: 1.35rem; color: var(--warning);">
                    <?= count($ancList) ?> Active
                </div>
                <div style="margin-top: 0.35rem;">
                    <a href="/announcements" style="font-size: 0.75rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">View Circulars &rarr;</a>
                </div>
            </div>
            <div class="metric-icon" style="font-size: 2rem;">📢</div>
        </div>
    <?php endif; ?>
</div>

<?php if (in_array($role, ['student', 'parent'])): ?>
<!-- Student & Parent Dashboard Main Grid -->
<?php
$cSummary = $sData['canteen_summary'] ?? ['available_items' => 8, 'active_orders' => 1, 'todays_orders' => 2, 'pending_orders' => 1, 'total_spending' => 265.00];
$lSummary = $sData['library_summary'] ?? ['books_issued' => 2, 'books_reserved' => 1, 'due_soon' => 1, 'overdue_books' => 0, 'total_fine' => 0.00];
?>

<!-- Student & Parent Canteen, Library & Hostel Summary Row (3-Col) -->
<?php
$cSummary = $sData['canteen_summary'] ?? ['available_items' => 8, 'active_orders' => 1, 'todays_orders' => 2, 'pending_orders' => 1, 'total_spending' => 265.00];
$lSummary = $sData['library_summary'] ?? ['books_issued' => 2, 'books_reserved' => 1, 'due_soon' => 1, 'overdue_books' => 0, 'total_fine' => 0.00];
$hSummary = $sData['hostel_summary'] ?? null;
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- 1. STUDENT CANTEEN SUMMARY -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>🍱</span> Canteen &amp; Meals
            </h3>
            <a href="/canteen/menu" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">View &rarr;</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center;">
            <div style="background: var(--bg-main); padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.68rem; color: var(--text-secondary);">Available</div>
                <div style="font-size: 1.15rem; font-weight: 800; color: #10b981;"><?= (int)$cSummary['available_items'] ?></div>
            </div>
            <div style="background: var(--bg-main); padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.68rem; color: var(--text-secondary);">Active</div>
                <div style="font-size: 1.15rem; font-weight: 800; color: #2563eb;"><?= (int)$cSummary['active_orders'] ?></div>
            </div>
            <div style="background: var(--bg-main); padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.68rem; color: var(--text-secondary);">Spending</div>
                <div style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary);">₹<?= number_format((float)$cSummary['total_spending'], 0) ?></div>
            </div>
        </div>
    </div>

    <!-- 2. STUDENT LIBRARY SUMMARY -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📚</span> Library Lending
            </h3>
            <a href="/library/catalog" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">View &rarr;</a>
        </div>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center;">
            <div style="background: var(--bg-main); padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.68rem; color: var(--text-secondary);">Issued</div>
                <div style="font-size: 1.15rem; font-weight: 800; color: #2563eb;"><?= (int)($lSummary['books_issued'] ?? 0) ?></div>
            </div>
            <div style="background: var(--bg-main); padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.68rem; color: var(--text-secondary);">Reserved</div>
                <div style="font-size: 1.15rem; font-weight: 800; color: #f59e0b;"><?= (int)($lSummary['books_reserved'] ?? 0) ?></div>
            </div>
            <div style="background: var(--bg-main); padding: 0.5rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div style="font-size: 0.68rem; color: var(--text-secondary);">Quota Left</div>
                <div style="font-size: 1.15rem; font-weight: 800; color: #10b981;"><?= (int)($lSummary['monthly_remaining'] ?? 4) ?></div>
            </div>
        </div>
    </div>

    <!-- 3. HOSTEL SUMMARY CARD -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>🏠</span> Hostel Residence
            </h3>
            <a href="<?= $role === 'parent' ? '/hostel/details' : '/hostel/booking' ?>" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">View &rarr;</a>
        </div>
        
        <?php if (!$hSummary): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-main); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                <div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">Hostel Status</div>
                    <div style="font-weight: 800; color: var(--text-primary); font-size: 0.875rem;">Not Booked</div>
                </div>
                <a href="/hostel/booking" class="btn btn-sm btn-primary" style="font-size: 0.75rem; font-weight: 700;">
                    Book Hostel →
                </a>
            </div>
        <?php elseif ($hSummary['booking_status'] === 'payment_pending'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(245, 158, 11, 0.08); padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(245, 158, 11, 0.3);">
                <div>
                    <div style="font-size: 0.75rem; color: #b45309; font-weight: 700;">Payment Pending</div>
                    <div style="font-size: 0.8125rem; font-weight: 800; color: var(--text-primary);">Room <?= e($hSummary['room_number']) ?> &bull; Bed <?= e($hSummary['bed_number']) ?></div>
                </div>
                <a href="/hostel/pay" class="btn btn-sm btn-warning" style="font-size: 0.75rem; font-weight: 800;">
                    Pay Fee →
                </a>
            </div>
        <?php elseif ($hSummary['booking_status'] === 'payment_verification_pending'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(2, 132, 199, 0.08); padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(2, 132, 199, 0.3);">
                <div>
                    <div style="font-size: 0.75rem; color: #0284c7; font-weight: 700;">Verification Pending</div>
                    <div style="font-size: 0.8125rem; font-weight: 800; color: var(--text-primary);">Room <?= e($hSummary['room_number']) ?> &bull; Bed <?= e($hSummary['bed_number']) ?></div>
                </div>
                <span class="badge badge-info" style="font-size: 0.7rem;">Warden Review</span>
            </div>
        <?php elseif ($hSummary['booking_status'] === 'confirmed'): ?>
            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(16, 185, 129, 0.08); padding: 0.75rem; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.3);">
                <div>
                    <div style="font-size: 0.75rem; color: var(--success); font-weight: 800; text-transform: uppercase;">CONFIRMED</div>
                    <div style="font-size: 0.875rem; font-weight: 800; color: var(--text-primary);"><?= e($hSummary['block_name']) ?></div>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">Room <?= e($hSummary['room_number']) ?> (Bed <?= e($hSummary['bed_number']) ?>) &bull; <strong style="color: var(--success);">PAID</strong></div>
                </div>
                <a href="<?= $role === 'parent' ? '/hostel/details' : '/hostel/booking' ?>" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem; font-weight: 700;">
                    Details →
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>




<!-- 18. STUDENT DASHBOARD QUICK ACTIONS -->
<div class="dashboard-grid-2">
    <!-- Active Announcements Board -->
    <div class="card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>📢</span> Campus Live Announcements &amp; Circulars
            </h2>
            <a href="/announcements" style="font-size: 0.8125rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">View All &rarr;</a>
        </div>

        <?php if (empty($ancList)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active campus announcements currently.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach (array_slice($ancList, 0, 3) as $anc): ?>
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

        <div style="display: flex; flex-direction: column; gap: 0.65rem;">
            <a href="/canteen/menu" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>🍴</span> Canteen Menu</span>
                <span class="badge badge-success">Order Food</span>
            </a>

            <a href="/canteen/orders" style="background: rgba(37, 99, 235, 0.08); border: 1px solid rgba(37, 99, 235, 0.25); padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>📦</span> My Canteen Orders</span>
                <span class="badge badge-info">Track Live Order</span>
            </a>

            <a href="/library/catalog" style="background: rgba(147, 51, 234, 0.08); border: 1px solid rgba(147, 51, 234, 0.25); padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>📚</span> Library Catalog</span>
                <span class="badge badge-secondary" style="background:#8b5cf6; color:#fff;">Reserve Books</span>
            </a>

            <a href="/library/my-books" style="background: rgba(2, 132, 199, 0.08); border: 1px solid rgba(2, 132, 199, 0.25); padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>📖</span> My Issued Books</span>
                <span class="badge badge-info">Loans Log</span>
            </a>

            <a href="/library/history" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>📅</span> Monthly Book History</span>
                <span class="badge badge-warning">Quota &amp; History</span>
            </a>

            <a href="/transport/routes" style="background: rgba(37, 99, 235, 0.08); border: 1px solid rgba(37, 99, 235, 0.25); padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>🚌</span> Transport &amp; Bus Routes</span>
                <span class="badge badge-info">Bus Fleet &amp; Routes</span>
            </a>

            <a href="/transport/history" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); padding: 0.75rem 1rem; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: space-between; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.5rem;"><span>💳</span> My Transport Payments</span>
                <span class="badge badge-success">Receipts &amp; Audit</span>
            </a>

        </div>
    </div>
</div>



<?php else: ?>

<!-- Executive Admin & Staff Command Dashboard Grid -->
<div class="dashboard-grid-2">
    <!-- Left Column: Operations & Department Stats -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Quick Action Shortcuts -->
        <div class="card">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚡</span> Operational Shortcuts & Quick Actions
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                <?php if (in_array($role, ['super_admin', 'admin', 'hod'])): ?>
                    <a href="/students/admission" style="background: rgba(2, 132, 199, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                        <span style="font-size: 1.25rem;">➕</span> Admit Student
                    </a>
                    <a href="/attendance" style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                        <span style="font-size: 1.25rem;">✅</span> Attendance Roster
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
                    <a href="/audit-logs" style="background: rgba(236, 72, 153, 0.1); border: 1px solid var(--border-color); padding: 1rem; border-radius: 0.5rem; text-decoration: none; display: flex; align-items: center; gap: 0.75rem; color: var(--text-primary); font-weight: 600; font-size: 0.875rem;">
                        <span style="font-size: 1.25rem;">📜</span> Audit Logs
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Departmental Student Distribution Card -->
        <div class="card">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>🏢</span> Departmental Student Strength
            </h2>

            <?php if (empty($deptStats)): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">No department statistics available.</p>
            <?php else: ?>
                <div style="overflow-x: auto;">
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
                                    <td style="font-weight: 700; color: var(--accent-color);"><?= e($ds['code']) ?></td>
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

        <!-- Live Announcements Board -->
        <div class="card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📢</span> Campus Live Announcements & Circulars
                </h2>
                <a href="/announcements" style="font-size: 0.8125rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">View All &rarr;</a>
            </div>

            <?php if (empty($ancList)): ?>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active campus announcements currently.</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach (array_slice($ancList, 0, 3) as $anc): ?>
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

<?php endif; ?>

            <!-- Recent Audit Log Activity Stream -->
            <?php if (!empty($auditLogs)): ?>
            <div class="card">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📜</span> Recent System Activity
                    </h2>
                    <a href="/audit-logs" style="font-size: 0.8125rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">Logs &rarr;</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($auditLogs as $log): ?>
                        <div style="background: var(--bg-main); padding: 0.75rem 0.875rem; border-radius: 6px; border: 1px solid var(--border-color); font-size: 0.75rem;">
                            <div style="display: flex; justify-content: space-between; font-weight: 700; color: var(--text-primary);">
                                <span><?= e($log['username'] ?? 'System') ?></span>
                                <span style="color: var(--text-secondary); font-weight: 400;"><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                            </div>
                            <div style="color: var(--text-secondary); margin-top: 0.15rem;"><?= e($log['action']) ?> &bull; <?= e($log['entity_type'] ?? '') ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
<?php endif; ?>

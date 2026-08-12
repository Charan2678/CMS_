<?php
/**
 * Modern Overview Warden Dashboard — Kuppam Engineering College
 * 
 * Strict Architecture: Overview Dashboard ONLY.
 * High-level statistics, occupancy summaries, and quick navigation shortcuts.
 */
$todayDate = date('l, d F Y');
$hour      = (int) date('H');
$greeting  = ($hour < 12) ? 'Good Morning' : (($hour < 17) ? 'Good Afternoon' : 'Good Evening');

$totalSt   = (int)($stats['total_students'] ?? 1250);
$totalRm   = (int)($stats['total_rooms'] ?? 450);
$occRm     = (int)($stats['occupied_rooms'] ?? 390);
$availBeds = (int)($stats['available_beds'] ?? 120);
$pendOut   = (int)($stats['pending_outpasses'] ?? 28);
$studOut   = (int)($stats['students_out'] ?? 64);
$totalCap  = (int)($stats['total_capacity'] ?? 1500);

$occRate   = ($totalCap > 0) ? round(($totalSt / $totalCap) * 100, 1) : 85.3;
?>

<style>
    .warden-dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .welcome-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
    }
    .welcome-banner h1 {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.2;
    }
    .welcome-banner p {
        margin: 0.35rem 0 0 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .date-badge {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.8125rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1rem;
    }
    @media (max-width: 1280px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px)  { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px)  { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .stat-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-number {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.1;
    }
    .stat-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }
    .stat-trend {
        font-size: 0.725rem;
        font-weight: 700;
        margin-top: 0.6rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Grid Layouts */
    .grid-2-col {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) { .grid-2-col { grid-template-columns: 1fr; } }

    /* Card Panels */
    .card-panel {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        padding: 1.35rem 1.5rem;
    }
    .card-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
    }
    .card-panel-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Quick Action Buttons */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.875rem;
    }
    @media (max-width: 1024px) { .quick-actions-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 600px)  { .quick-actions-grid { grid-template-columns: repeat(2, 1fr); } }

    .qa-btn {
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 0.75rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        color: var(--text-primary);
    }
    .qa-btn:hover {
        background: rgba(37, 99, 235, 0.06);
        border-color: #2563eb;
        transform: translateY(-2px);
    }
    .qa-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #2563eb;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .qa-title {
        font-size: 0.8125rem;
        font-weight: 700;
        text-align: center;
    }

    .progress-bar-bg {
        height: 8px;
        border-radius: 10px;
        background: var(--border-color);
        overflow: hidden;
        margin-top: 0.4rem;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: #2563eb;
    }
</style>

<div class="warden-dashboard-wrapper">

    <!-- 1. WELCOME SECTION -->
    <div class="welcome-banner">
        <div>
            <h1><?= $greeting ?>, Warden 👋</h1>
            <p>Here's your hostel overview for today at <strong>Kuppam Engineering College</strong>.</p>
        </div>
        <div class="date-badge">
            📅 <span><?= $todayDate ?></span>
        </div>
    </div>

    <!-- 2. HOSTEL STATISTICS (6 CARDS) -->
    <div class="stats-grid">
        <!-- 1. Total Students -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Students</span>
                <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">🎓</div>
            </div>
            <div class="stat-number"><?= number_format($totalSt) ?></div>
            <div class="stat-trend" style="color: #059669;">
                <span>+4.5%</span> <span style="color: var(--text-secondary); font-weight: 500;">this semester</span>
            </div>
        </div>

        <!-- 2. Total Rooms -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Rooms</span>
                <div class="stat-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">🏢</div>
            </div>
            <div class="stat-number"><?= number_format($totalRm) ?></div>
            <div class="stat-trend" style="color: #6366f1;">
                <span>Across 3 Blocks</span>
            </div>
        </div>

        <!-- 3. Occupied Rooms -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Occupied Rooms</span>
                <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">🛌</div>
            </div>
            <div class="stat-number"><?= number_format($occRm) ?></div>
            <div class="stat-trend" style="color: #10b981;">
                <span><?= round(($occRm/$totalRm)*100) ?>%</span> <span style="color: var(--text-secondary); font-weight: 500;">occupancy</span>
            </div>
        </div>

        <!-- 4. Available Beds -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Available Beds</span>
                <div class="stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">🔑</div>
            </div>
            <div class="stat-number"><?= number_format($availBeds) ?></div>
            <div class="stat-trend" style="color: #0ea5e9;">
                <span>Ready for allocation</span>
            </div>
        </div>

        <!-- 5. Pending Outpasses -->
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <div class="stat-header">
                <span class="stat-label">Pending Outpasses</span>
                <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">📝</div>
            </div>
            <div class="stat-number" style="color: #d97706;"><?= number_format($pendOut) ?></div>
            <div class="stat-trend" style="color: #d97706;">
                <span>Requires Approval</span>
            </div>
        </div>

        <!-- 6. Students Out -->
        <div class="stat-card" style="border-left: 4px solid var(--accent-color);">
            <div class="stat-header">
                <span class="stat-label">Students Out</span>
                <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">🚪</div>
            </div>
            <div class="stat-number"><?= number_format($studOut) ?></div>
            <div class="stat-trend" style="color: var(--accent-color);">
                <span>Currently Outside</span>
            </div>
        </div>
    </div>

    <!-- 3. QUICK ACTIONS SHORTCUTS -->
    <div class="card-panel">
        <div class="card-panel-header" style="margin-bottom: 1rem;">
            <h3 class="card-panel-title">⚡ Quick Actions</h3>
            <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">Warden Desk Shortcuts</span>
        </div>
        <div class="quick-actions-grid">
            <a href="/hostel/management" class="qa-btn">
                <div class="qa-icon" style="background: #2563eb;">🛌</div>
                <span class="qa-title">Allocate Student</span>
            </a>

            <a href="/hostel/management" class="qa-btn">
                <div class="qa-icon" style="background: #7c3aed;">🔑</div>
                <span class="qa-title">Add Hostel Room</span>
            </a>

            <a href="/hostel/management" class="qa-btn">
                <div class="qa-icon" style="background: #0284c7;">🏢</div>
                <span class="qa-title">Manage Hostel</span>
            </a>

            <a href="/leave/outpasses" class="qa-btn">
                <div class="qa-icon" style="background: #f59e0b;">📝</div>
                <span class="qa-title">Gate Outpass</span>
            </a>

            <a href="/leave/outpasses" class="qa-btn">
                <div class="qa-icon" style="background: #059669;">🚪</div>
                <span class="qa-title">Check-in Student</span>
            </a>

            <a href="/announcements" class="qa-btn">
                <div class="qa-icon" style="background: #6366f1;">📢</div>
                <span class="qa-title">View Announcements</span>
            </a>
        </div>
    </div>

    <!-- 4. OCCUPANCY & OUTPASS OVERVIEWS (2-COL) -->
    <div class="grid-2-col">

        <!-- 8. HOSTEL OCCUPANCY OVERVIEW -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">📊 Hostel Occupancy Overview</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Real-time capacity distribution</span>
                </div>
                <a href="/hostel/management" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View Hostel Management &rarr;
                </a>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; padding: 0.5rem 0;">
                <div style="flex: 1; min-width: 200px;">
                    <div style="font-size: 2.2rem; font-weight: 800; color: var(--accent-color); font-family: monospace;">
                        <?= $occRate ?>%
                    </div>
                    <div style="font-size: 0.8125rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase;">
                        Total Occupancy Rate
                    </div>

                    <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.8125rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total Capacity:</span>
                            <strong><?= number_format($totalCap) ?> Beds</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Occupied Beds:</span>
                            <strong style="color: #2563eb;"><?= number_format($totalSt) ?> Beds</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Available Beds:</span>
                            <strong style="color: #10b981;"><?= number_format($availBeds) ?> Beds</strong>
                        </div>
                    </div>
                </div>

                <!-- Visual Bar Indicator -->
                <div style="flex: 1; min-width: 220px; background: var(--bg-main); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.8125rem; font-weight: 700; margin-bottom: 0.5rem;">Capacity Allocation</div>
                    <div class="progress-bar-bg" style="height: 12px;">
                        <div class="progress-bar-fill" style="width: <?= min(100, $occRate) ?>%; background: #2563eb;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">
                        <span>0 Beds</span>
                        <span><?= number_format($totalCap) ?> Max</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 11. GATE OUTPASS OVERVIEW -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🚪 Gate Outpass &amp; Check-in</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Today's Movement Status</span>
                </div>
                <a href="/leave/outpasses" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    Manage Outpasses &rarr;
                </a>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem;">
                <div style="background: var(--bg-main); border: 1px solid var(--border-color); padding: 1rem; border-radius: 10px; text-align: center;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #f59e0b; text-transform: uppercase;">Pending</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;"><?= $pendOut ?></div>
                </div>

                <div style="background: var(--bg-main); border: 1px solid var(--border-color); padding: 1rem; border-radius: 10px; text-align: center;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #10b981; text-transform: uppercase;">Approved Today</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;">42</div>
                </div>

                <div style="background: var(--bg-main); border: 1px solid var(--border-color); padding: 1rem; border-radius: 10px; text-align: center;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #2563eb; text-transform: uppercase;">Currently Out</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;"><?= $studOut ?></div>
                </div>

                <div style="background: var(--bg-main); border: 1px solid var(--border-color); padding: 1rem; border-radius: 10px; text-align: center;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #6366f1; text-transform: uppercase;">Checked In Today</div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;">38</div>
                </div>
            </div>
        </div>

    </div>

    <!-- 5. BLOCK SUMMARY & ALLOCATION SUMMARY (2-COL) -->
    <div class="grid-2-col">

        <!-- 9. HOSTEL BLOCK SUMMARY -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🏢 Hostel Block Summary</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Block-wise Capacity Breakdown</span>
                </div>
                <a href="/hostel/management" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View All Blocks &rarr;
                </a>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="margin: 0;">
                    <thead>
                        <tr>
                            <th>Block Name</th>
                            <th>Type</th>
                            <th>Warden</th>
                            <th>Total Rooms</th>
                            <th>Occupied</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blockSummary as $blk): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($blk['name']) ?></td>
                                <td><span class="badge badge-info"><?= e($blk['type']) ?></span></td>
                                <td style="font-size: 0.8125rem; color: var(--text-secondary);"><?= e($blk['warden_name']) ?></td>
                                <td style="font-weight: 600;"><?= (int)$blk['room_count'] ?> Rooms</td>
                                <td style="font-weight: 700; color: #2563eb;"><?= (int)$blk['occupied_beds'] ?> Beds</td>
                                <td style="font-weight: 700; color: #10b981;"><?= (int)$blk['available_beds'] ?> Beds</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 10. STUDENT ALLOCATION SUMMARY -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🛌 Student Room Allocation</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Current Resident Roster</span>
                </div>
                <a href="/hostel/management" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    Manage Allocations &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <span>Total Allocated Residents</span>
                    <strong style="font-size: 1.1rem; color: #2563eb;"><?= number_format($totalSt) ?></strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <span>Unallocated Hostel Applicants</span>
                    <strong style="font-size: 1.1rem; color: var(--danger);">35</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <span>Available Vacant Beds</span>
                    <strong style="font-size: 1.1rem; color: #10b981;"><?= number_format($availBeds) ?></strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <span>Recently Allocated (This Week)</span>
                    <strong style="font-size: 1.1rem; color: #6366f1;">18</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- 6. RECENT ACTIVITY, IMPORTANT ALERTS, & ANNOUNCEMENTS (3-COL) -->
    <div class="grid-2-col">

        <!-- 12. RECENT HOSTEL ACTIVITY (5-6 ITEMS ONLY) -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">⚡ Recent Hostel Activity</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Live movement &amp; allocation feed</span>
                </div>
                <a href="/leave/outpasses" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View All Activity &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(37,99,235,0.1); color:#2563eb; display:flex; align-items:center; justify-content:center; font-weight:700;">🛌</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">Rahul Kumar</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Room allocated: Boys Hostel A — Room 204</div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">10 mins ago</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-weight:700;">📝</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">Priya Sharma</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Outpass approved for weekend visit</div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Today at 10:30 AM</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(99,102,241,0.1); color:#6366f1; display:flex; align-items:center; justify-content:center; font-weight:700;">🚪</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">Arun Kumar</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Checked in at main security gate</div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Today at 9:45 AM</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(245,158,11,0.1); color:#f59e0b; display:flex; align-items:center; justify-content:center; font-weight:700;">🔑</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">Kiran Reddy</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Room changed: Girls Hostel B</div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">1 hour ago</span>
                </div>
            </div>
        </div>

        <!-- 13. IMPORTANT ALERTS & 14. ANNOUNCEMENTS -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            <!-- 13. PENDING / IMPORTANT ALERTS -->
            <div class="card-panel" style="border-top: 4px solid var(--danger);">
                <div class="card-panel-header">
                    <h3 class="card-panel-title" style="color: var(--danger);">⚠️ Important Alerts</h3>
                    <span class="badge badge-danger">Attention</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.8125rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(239, 68, 68, 0.06); padding: 0.6rem 0.75rem; border-radius: 8px;">
                        <span>⚠ <strong>28 outpass requests</strong> waiting for approval.</span>
                        <a href="/leave/outpasses" class="btn btn-sm btn-outline-danger" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Review</a>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(245, 158, 11, 0.06); padding: 0.6rem 0.75rem; border-radius: 8px;">
                        <span>⚠ <strong>35 students</strong> not yet allocated to rooms.</span>
                        <a href="/hostel/management" class="btn btn-sm btn-outline-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Allocate</a>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(245, 158, 11, 0.06); padding: 0.6rem 0.75rem; border-radius: 8px;">
                        <span>⚠ <strong>12 rooms</strong> require maintenance inspection.</span>
                        <a href="/hostel/management" class="btn btn-sm btn-outline-primary" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">View</a>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(239, 68, 68, 0.06); padding: 0.6rem 0.75rem; border-radius: 8px;">
                        <span>⚠ <strong>8 students</strong> exceeded approved outpass return time.</span>
                        <a href="/leave/outpasses" class="btn btn-sm btn-outline-danger" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Check</a>
                    </div>
                </div>
            </div>

            <!-- 14. ANNOUNCEMENTS -->
            <div class="card-panel">
                <div class="card-panel-header">
                    <h3 class="card-panel-title">📢 Hostel Announcements</h3>
                    <a href="/announcements" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View All &rarr;
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 0.8125rem; color: var(--text-primary);">Hostel Timings Extended During Exams</strong>
                            <span class="badge badge-info" style="font-size: 0.65rem;">Today</span>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                            Night study hall open till 11:30 PM for end-term examinations.
                        </p>
                    </div>

                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 0.8125rem; color: var(--text-primary);">Mess Committee Monthly Meeting</strong>
                            <span class="badge badge-secondary" style="font-size: 0.65rem;">Yesterday</span>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                            Student representatives meeting at 5:00 PM in Block A conference room.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

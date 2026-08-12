<?php
/**
 * Modern Overview Transport Manager Dashboard — Kuppam Engineering College
 * 
 * Strict Architecture: Overview Dashboard ONLY.
 * High-level statistics, accounts/payment summary, route metrics, and navigation shortcuts.
 * NO full management tables or creation forms on this page.
 */
$todayDate = date('l, d F Y');
$hour      = (int) date('H');
$greeting  = ($hour < 12) ? 'Good Morning' : (($hour < 17) ? 'Good Afternoon' : 'Good Evening');

$totRoutes   = (int)($stats['total_routes'] ?? 24);
$totStudents = (int)($stats['total_students'] ?? 1250);
$totBuses    = (int)($stats['total_buses'] ?? 32);
$actRoutes   = (int)($stats['active_routes'] ?? 22);
$pendFees    = (float)($stats['pending_fees'] ?? 485000.00);
$collFees    = (float)($stats['fees_collected'] ?? 1840000.00);

$paidCount   = (int)($paymentSum['paid_students'] ?? 980);
$unpaidCount = (int)($paymentSum['unpaid_students'] ?? 210);
$partCount   = (int)($paymentSum['partially_paid'] ?? 60);
$totFeeAmt   = (float)($paymentSum['total_fee'] ?? 2325000.00);
$collPct     = (float)($paymentSum['collection_percentage'] ?? 79.1);
?>

<style>
    .transport-dashboard-wrapper {
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

    .stat-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }
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
        height: 100%;
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
        font-size: 1.55rem;
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
        grid-template-columns: 1fr 1fr;
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
        grid-template-columns: repeat(5, 1fr);
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
        height: 10px;
        border-radius: 10px;
        background: var(--border-color);
        overflow: hidden;
        margin-top: 0.5rem;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        background: #2563eb;
    }
</style>

<div class="transport-dashboard-wrapper">

    <!-- 3. WELCOME SECTION -->
    <div class="welcome-banner">
        <div>
            <h1><?= $greeting ?>, Transport Manager 👋</h1>
            <p>Here's your transport overview for today at <strong>Kuppam Engineering College</strong>.</p>
        </div>
        <div class="date-badge">
            📅 <span><?= $todayDate ?></span>
        </div>
    </div>

    <!-- 4. TRANSPORT STATISTICS (6 COMPACT CARDS) -->
    <div class="stats-grid">
        <!-- 1. Total Routes -->
        <a href="/transport/routes" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Total Routes</span>
                    <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">🚌</div>
                </div>
                <div class="stat-number"><?= number_format($totRoutes) ?></div>
                <div class="stat-trend" style="color: #2563eb;">
                    <span>View Routes &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 2. Total Buses -->
        <a href="/transport/routes" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Total Buses</span>
                    <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">🚍</div>
                </div>
                <div class="stat-number"><?= number_format($totBuses) ?></div>
                <div class="stat-trend" style="color: #10b981;">
                    <span>Active Fleet &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 3. Transport Students -->
        <a href="/transport/routes" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Transport Students</span>
                    <div class="stat-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">👥</div>
                </div>
                <div class="stat-number"><?= number_format($totStudents) ?></div>
                <div class="stat-trend" style="color: #6366f1;">
                    <span>Manage Riders &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 4. Active Routes -->
        <a href="/transport/routes" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Active Routes</span>
                    <div class="stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">🛣️</div>
                </div>
                <div class="stat-number"><?= number_format($actRoutes) ?></div>
                <div class="stat-trend" style="color: #0ea5e9;">
                    <span>Operating &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 5. Fees Collected -->
        <a href="/transport/accounts" class="stat-card-link">
            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <div class="stat-header">
                    <span class="stat-label">Fees Collected</span>
                    <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">💰</div>
                </div>
                <div class="stat-number" style="color: #059669;">₹18.40 L</div>
                <div class="stat-trend" style="color: #059669;">
                    <span><?= $collPct ?>% Collected &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 6. Pending Fees -->
        <a href="/transport/accounts" class="stat-card-link">
            <div class="stat-card" style="border-left: 4px solid var(--danger);">
                <div class="stat-header">
                    <span class="stat-label">Pending Fees</span>
                    <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">⏳</div>
                </div>
                <div class="stat-number" style="color: var(--danger);">₹4.85 L</div>
                <div class="stat-trend" style="color: var(--danger);">
                    <span>210 Unpaid &rarr;</span>
                </div>
            </div>
        </a>
    </div>

    <!-- 5. QUICK ACTIONS -->
    <div class="card-panel">
        <div class="card-panel-header" style="margin-bottom: 1rem;">
            <h3 class="card-panel-title">⚡ Quick Actions</h3>
            <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">Navigation Shortcuts</span>
        </div>
        <div class="quick-actions-grid">
            <a href="/transport/routes" class="qa-btn">
                <div class="qa-icon" style="background: #2563eb;">➕</div>
                <span class="qa-title">+ Add Route</span>
            </a>

            <a href="/transport/routes" class="qa-btn">
                <div class="qa-icon" style="background: #7c3aed;">🚌</div>
                <span class="qa-title">Subscribe Student</span>
            </a>

            <a href="/transport/accounts" class="qa-btn">
                <div class="qa-icon" style="background: #059669;">💳</div>
                <span class="qa-title">Record Payment</span>
            </a>

            <a href="/transport/accounts" class="qa-btn">
                <div class="qa-icon" style="background: #dc2626;">🚨</div>
                <span class="qa-title">View Unpaid Students</span>
            </a>

            <a href="/transport/routes" class="qa-btn">
                <div class="qa-icon" style="background: #0284c7;">🛣️</div>
                <span class="qa-title">View Routes</span>
            </a>
        </div>
    </div>

    <!-- 6. TRANSPORT OVERVIEW & 7. ACCOUNTS SUMMARY (2-COL) -->
    <div class="grid-2-col">

        <!-- 6. TRANSPORT OVERVIEW -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🚌 Transport Overview</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Fleet utilization summary</span>
                </div>
                <a href="/transport/routes" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View Transport &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; text-align: center;">
                    <div style="background: var(--bg-main); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Total Students</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;"><?= number_format($totStudents) ?></div>
                    </div>
                    <div style="background: var(--bg-main); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Active Routes</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #2563eb; margin-top: 0.2rem;"><?= number_format($actRoutes) ?></div>
                    </div>
                    <div style="background: var(--bg-main); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Total Buses</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #10b981; margin-top: 0.2rem;"><?= number_format($totBuses) ?></div>
                    </div>
                    <div style="background: var(--bg-main); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Available Seats</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #6366f1; margin-top: 0.2rem;">180</div>
                    </div>
                </div>

                <div style="background: var(--bg-main); padding: 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; font-weight: 700;">
                        <span>Bus Capacity Utilization</span>
                        <span style="color: #2563eb;">83.3% Occupied</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: 83.3%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. ACCOUNTS SUMMARY -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">💳 Transport Fee Overview</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Revenue &amp; outstanding balances</span>
                </div>
                <a href="/transport/accounts" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View Accounts &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; text-align: center;">
                    <div style="background: var(--bg-main); padding: 0.6rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.68rem; color: var(--text-secondary);">Total Riders</div>
                        <div style="font-size: 1rem; font-weight: 800;"><?= number_format($totStudents) ?></div>
                    </div>
                    <div style="background: rgba(16,185,129,0.08); padding: 0.6rem; border-radius: 8px; border: 1px solid rgba(16,185,129,0.2);">
                        <div style="font-size: 0.68rem; color: #10b981; font-weight: 600;">Paid</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #10b981;"><?= number_format($paidCount) ?></div>
                    </div>
                    <div style="background: rgba(245,158,11,0.08); padding: 0.6rem; border-radius: 8px; border: 1px solid rgba(245,158,11,0.2);">
                        <div style="font-size: 0.68rem; color: #f59e0b; font-weight: 600;">Partial</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #d97706;"><?= number_format($partCount) ?></div>
                    </div>
                    <div style="background: rgba(239,68,68,0.08); padding: 0.6rem; border-radius: 8px; border: 1px solid rgba(239,68,68,0.2);">
                        <div style="font-size: 0.68rem; color: var(--danger); font-weight: 600;">Unpaid</div>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--danger);"><?= number_format($unpaidCount) ?></div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <span>Total Fee: <strong>₹23.25 L</strong></span>
                    <span>Collected: <strong style="color: #10b981;">₹18.40 L</strong></span>
                    <span>Pending: <strong style="color: var(--danger);">₹4.85 L</strong></span>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.25rem;">
                        <span>Collection Rate</span>
                        <span style="color: #10b981;">79.1% Collected</span>
                    </div>
                    <div class="progress-bar-bg" style="margin-top: 0;">
                        <div class="progress-bar-fill" style="width: 79.1%; background: #10b981;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 8. PAYMENT STATUS & 10. IMPORTANT ALERTS (2-COL) -->
    <div class="grid-2-col">

        <!-- 8. PAYMENT STATUS -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">📊 Payment Status</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Fee payment distribution</span>
                </div>
                <a href="/transport/accounts" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View Payment Details &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Paid Students (78.4%)</span>
                        <span style="color: #10b981; font-weight: 700;"><?= number_format($paidCount) ?> Students</span>
                    </div>
                    <div class="progress-bar-bg" style="margin: 0;">
                        <div class="progress-bar-fill" style="width: 78.4%; background: #10b981;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Partially Paid (4.8%)</span>
                        <span style="color: #f59e0b; font-weight: 700;"><?= number_format($partCount) ?> Students</span>
                    </div>
                    <div class="progress-bar-bg" style="margin: 0;">
                        <div class="progress-bar-fill" style="width: 4.8%; background: #f59e0b;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Unpaid Students (16.8%)</span>
                        <span style="color: var(--danger); font-weight: 700;"><?= number_format($unpaidCount) ?> Students</span>
                    </div>
                    <div class="progress-bar-bg" style="margin: 0;">
                        <div class="progress-bar-fill" style="width: 16.8%; background: var(--danger);"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 10. IMPORTANT ALERTS -->
        <div class="card-panel" style="border-top: 4px solid var(--danger);">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title" style="color: var(--danger);">⚠️ Important Alerts</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Action required</span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(239, 68, 68, 0.06); padding: 0.65rem 0.875rem; border-radius: 8px; border: 1px solid rgba(239,68,68,0.2);">
                    <span>⚠ <strong>210 students</strong> have unpaid transport fees.</span>
                    <a href="/transport/accounts" class="btn btn-sm btn-outline-danger" style="font-size: 0.725rem;">View Details &rarr;</a>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(245, 158, 11, 0.06); padding: 0.65rem 0.875rem; border-radius: 8px; border: 1px solid rgba(245,158,11,0.2);">
                    <span>⚠ <strong>₹4.85 L</strong> transport fees are pending.</span>
                    <a href="/transport/accounts" class="btn btn-sm btn-outline-primary" style="font-size: 0.725rem;">View Details &rarr;</a>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(37, 99, 235, 0.06); padding: 0.65rem 0.875rem; border-radius: 8px; border: 1px solid rgba(37,99,235,0.2);">
                    <span>⚠ <strong>5 bus routes</strong> require capacity attention.</span>
                    <a href="/transport/routes" class="btn btn-sm btn-outline-primary" style="font-size: 0.725rem;">View Details &rarr;</a>
                </div>
            </div>
        </div>

    </div>

    <!-- 11. ROUTE SUMMARY & 9. RECENT TRANSPORT ACTIVITY (2-COL) -->
    <div class="grid-2-col">

        <!-- 11. ROUTE SUMMARY (COMPACT TOP 4-5 ROUTES) -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🛣️ Route Summary</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Top active college routes</span>
                </div>
                <a href="/transport/routes" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View All Routes &rarr;
                </a>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="margin: 0;">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Code</th>
                            <th>Students</th>
                            <th>Available Seats</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);">Palamaner</td>
                            <td><span class="badge badge-info">R-03</span></td>
                            <td style="font-weight: 600;">120</td>
                            <td style="font-weight: 700; color: #10b981;">30 Vacant</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);">Chittoor</td>
                            <td><span class="badge badge-info">R-05</span></td>
                            <td style="font-weight: 600;">95</td>
                            <td style="font-weight: 700; color: #10b981;">25 Vacant</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: var(--text-primary);">Vellore</td>
                            <td><span class="badge badge-info">R-08</span></td>
                            <td style="font-weight: 600;">110</td>
                            <td style="font-weight: 700; color: #10b981;">20 Vacant</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 9. RECENT TRANSPORT ACTIVITY (COMPACT 5 ITEMS) -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">⚡ Recent Transport Activity</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Latest transport activity feed</span>
                </div>
                <a href="/transport/accounts" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View All &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong style="color: var(--text-primary);">Rahul Kumar</strong>
                        <div style="font-size: 0.725rem; color: var(--text-secondary);">Subscribed to Route R-03</div>
                    </div>
                    <span style="font-size: 0.725rem; color: var(--text-secondary);">10 mins ago</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong style="color: var(--text-primary);">Priya Sharma</strong>
                        <div style="font-size: 0.725rem; color: var(--text-secondary);">Transport payment received: <strong>₹18,000</strong></div>
                    </div>
                    <span style="font-size: 0.725rem; color: var(--text-secondary);">30 mins ago</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong style="color: var(--text-primary);">Arun Kumar</strong>
                        <div style="font-size: 0.725rem; color: var(--text-secondary);">Route subscription renewed</div>
                    </div>
                    <span style="font-size: 0.725rem; color: var(--text-secondary);">1 hour ago</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <strong style="color: var(--text-primary);">Kiran Reddy</strong>
                        <div style="font-size: 0.725rem; color: var(--text-secondary);">Partial payment recorded: <strong>₹9,000</strong></div>
                    </div>
                    <span style="font-size: 0.725rem; color: var(--text-secondary);">2 hours ago</span>
                </div>
            </div>
        </div>

    </div>

    <!-- 12. ANNOUNCEMENTS SUMMARY -->
    <div class="card-panel">
        <div class="card-panel-header">
            <h3 class="card-panel-title">📢 Transport Announcements</h3>
            <a href="/transport/announcements" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                View All &rarr;
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                    <strong style="font-size: 0.8125rem; color: var(--text-primary);">Fee Deadline Extended</strong>
                    <span class="badge badge-info" style="font-size: 0.65rem;">Today</span>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">
                    Annual transport fee payment deadline extended till 25th August.
                </p>
            </div>

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                    <strong style="font-size: 0.8125rem; color: var(--text-primary);">Route R-03 Timing Updated</strong>
                    <span class="badge badge-secondary" style="font-size: 0.65rem;">Yesterday</span>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">
                    Morning departure updated to 7:20 AM starting Monday.
                </p>
            </div>

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                    <strong style="font-size: 0.8125rem; color: var(--text-primary);">Bus Maintenance Schedule</strong>
                    <span class="badge badge-secondary" style="font-size: 0.65rem;">2 days ago</span>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">
                    Fleet routine fitness inspection scheduled for upcoming weekend.
                </p>
            </div>
        </div>
    </div>

</div>

<?php
/**
 * High-Level Overview Librarian Dashboard — Kuppam Engineering College
 * 
 * Strict Architecture: High-level summary & navigation shortcuts ONLY.
 * No duplicate full module management tables.
 */
$todayDate = date('l, d F Y');
$hour      = (int) date('H');
$greeting  = ($hour < 12) ? 'Good Morning' : (($hour < 17) ? 'Good Afternoon' : 'Good Evening');

$totalBk   = max(1, (int)($stats['total_books'] ?? 0));
$availBk   = (int)($stats['available_books'] ?? 0);
$issuedBk  = (int)($stats['issued_books'] ?? 0);
$overdueBk = (int)($stats['overdue_books'] ?? 0);

$availPct   = round(($availBk / $totalBk) * 100, 1);
$issuedPct  = round(($issuedBk / $totalBk) * 100, 1);
$overduePct = round(($overdueBk / $totalBk) * 100, 1);
?>

<style>
    .lib-dashboard-wrapper {
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
        grid-template-columns: 1.8fr 1fr;
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

    /* Progress bar */
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

<div class="lib-dashboard-wrapper">

    <!-- 1. A. WELCOME SECTION -->
    <div class="welcome-banner">
        <div>
            <h1><?= $greeting ?>, Librarian 👋</h1>
            <p>Here's your library overview for today at <strong>Kuppam Engineering College</strong>.</p>
        </div>
        <div class="date-badge">
            📅 <span><?= $todayDate ?></span>
        </div>
    </div>

    <!-- 2. B. LIBRARY OVERVIEW STATISTICS (6 CARDS) -->
    <div class="stats-grid">
        <!-- 1. Total Books -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">📚</div>
            </div>
            <div class="stat-number"><?= number_format($stats['total_books']) ?></div>
            <div class="stat-trend" style="color: #059669;">
                <span>Total Catalog</span>
            </div>
        </div>

        <!-- 2. Available Books -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Available Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">✅</div>
            </div>
            <div class="stat-number"><?= number_format($stats['available_books']) ?></div>
            <div class="stat-trend" style="color: #10b981;">
                <span><?= $availPct ?>%</span> <span style="color: var(--text-secondary); font-weight: 500;">in stock</span>
            </div>
        </div>

        <!-- 3. Issued Books -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Issued Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">📖</div>
            </div>
            <div class="stat-number"><?= number_format($stats['issued_books']) ?></div>
            <div class="stat-trend" style="color: #6366f1;">
                <span><?= $issuedPct ?>%</span> <span style="color: var(--text-secondary); font-weight: 500;">circulating</span>
            </div>
        </div>

        <!-- 4. Overdue Books -->
        <div class="stat-card" style="border-left: 4px solid var(--danger);">
            <div class="stat-header">
                <span class="stat-label">Overdue Books</span>
                <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">⚠️</div>
            </div>
            <div class="stat-number" style="color: var(--danger);"><?= number_format($stats['overdue_books']) ?></div>
            <div class="stat-trend" style="color: var(--danger);">
                <span><?= $overduePct ?>%</span> <span style="color: var(--text-secondary); font-weight: 500;">overdue</span>
            </div>
        </div>

        <!-- 5. Registered Members -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Registered Members</span>
                <div class="stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">👥</div>
            </div>
            <div class="stat-number"><?= number_format($stats['registered_members']) ?></div>
            <div class="stat-trend" style="color: #0ea5e9;">
                <span>Students &amp; Faculty</span>
            </div>
        </div>

        <!-- 6. Books Due Today -->
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Books Due Today</span>
                <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">⏳</div>
            </div>
            <div class="stat-number"><?= number_format($stats['due_today']) ?></div>
            <div class="stat-trend" style="color: #f59e0b;">
                <span>Expected Returns</span>
            </div>
        </div>
    </div>

    <!-- 3. C. QUICK ACTIONS SHORTCUTS PANEL -->
    <div class="card-panel">
        <div class="card-panel-header" style="margin-bottom: 1rem;">
            <h3 class="card-panel-title">⚡ Quick Actions</h3>
            <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">Navigation Shortcuts</span>
        </div>
        <div class="quick-actions-grid">
            <a href="/library/catalog" class="qa-btn">
                <div class="qa-icon" style="background: #7c3aed;">➕</div>
                <span class="qa-title">+ Add Book</span>
            </a>

            <a href="/library/issue" class="qa-btn">
                <div class="qa-icon" style="background: #2563eb;">📖</div>
                <span class="qa-title">Issue Book</span>
            </a>

            <a href="/library/issue" class="qa-btn">
                <div class="qa-icon" style="background: #059669;">📥</div>
                <span class="qa-title">Return Book</span>
            </a>

            <a href="/students" class="qa-btn">
                <div class="qa-icon" style="background: #0284c7;">👤</div>
                <span class="qa-title">Register Member</span>
            </a>

            <a href="/library/catalog" class="qa-btn">
                <div class="qa-icon" style="background: #d97706;">🔍</div>
                <span class="qa-title">Search Catalog</span>
            </a>

            <a href="/library/reports/overdue" class="qa-btn">
                <div class="qa-icon" style="background: #dc2626;">🚨</div>
                <span class="qa-title">View Overdue</span>
            </a>
        </div>
    </div>

    <!-- 4. D. LIBRARY ACTIVITY & F. OVERDUE ALERT (2-COL) -->
    <div class="grid-2-col">

        <!-- D. LIBRARY ACTIVITY CHART CARD -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">📈 Library Activity Overview</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Issued, Returned &amp; Renewed Volume</span>
                </div>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <a href="/library/reports/circulation" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View Full Report &rarr;
                    </a>
                </div>
            </div>

            <!-- SVG Trend Chart -->
            <div style="padding: 0.5rem 0;">
                <svg viewBox="0 0 400 150" style="width: 100%; height: auto; overflow: visible;">
                    <line x1="0" y1="30" x2="400" y2="30" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
                    <line x1="0" y1="70" x2="400" y2="70" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
                    <line x1="0" y1="110" x2="400" y2="110" stroke="var(--border-color)" stroke-dasharray="4" opacity="0.5" />
                    <defs>
                        <linearGradient id="chartGradOverview" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2563eb" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#2563eb" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <path d="M 10,120 Q 80,40 150,65 T 290,25 T 390,75 L 390,135 L 10,135 Z" fill="url(#chartGradOverview)" />
                    <path d="M 10,120 Q 80,40 150,65 T 290,25 T 390,75" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" />
                    <circle cx="10" cy="120" r="4" fill="#2563eb"/>
                    <circle cx="80" cy="40" r="4" fill="#2563eb"/>
                    <circle cx="150" cy="65" r="4" fill="#2563eb"/>
                    <circle cx="220" cy="45" r="4" fill="#2563eb"/>
                    <circle cx="290" cy="25" r="4" fill="#2563eb"/>
                    <circle cx="390" cy="75" r="4" fill="#2563eb"/>
                </svg>
            </div>
            <div style="display: flex; justify-content: space-around; font-size: 0.75rem; color: var(--text-secondary); border-top: 1px solid var(--border-color); padding-top: 0.5rem; margin-top: 0.25rem;">
                <span><strong style="color:#2563eb;">●</strong> Books Issued: <?= number_format($stats['issued_books']) ?></span>
                <span><strong style="color:#10b981;">●</strong> Books Returned</span>
                <span><strong style="color:#f59e0b;">●</strong> Books Renewed</span>
            </div>
        </div>

        <!-- F. OVERDUE ALERT CARD -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card-panel" style="border-top: 4px solid var(--danger);">
                <div class="card-panel-header" style="margin-bottom: 0.75rem;">
                    <h3 class="card-panel-title" style="color: var(--danger);">🚨 Overdue Books Alert</h3>
                    <span class="badge badge-danger">Critical</span>
                </div>
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--danger); margin-bottom: 0.25rem;">
                    <?= number_format($stats['overdue_books']) ?> Books Overdue
                </div>
                <p style="font-size: 0.8125rem; color: var(--text-secondary); margin: 0 0 1rem 0; line-height: 1.4;">
                    Members currently exceeding lending threshold. Assessed penalty collection rate: ₹5.00/day.
                </p>
                <a href="/library/reports/overdue" class="btn btn-primary" style="background: var(--danger); border-color: var(--danger); width: 100%; text-align: center; text-decoration: none;">
                    View Overdue Reports &rarr;
                </a>
            </div>

            <!-- G. BOOK INVENTORY OVERVIEW -->
            <div class="card-panel">
                <div class="card-panel-header" style="margin-bottom: 0.75rem;">
                    <h3 class="card-panel-title">📊 Inventory Summary</h3>
                    <a href="/library/reports/inventory" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View Inventory Report &rarr;
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.8125rem;">
                    <div>
                        <div style="display: flex; justify-content: space-between; font-weight: 600;">
                            <span>Available Copies (<?= $availPct ?>%)</span>
                            <span style="color: #10b981;"><?= number_format($stats['available_books']) ?></span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: <?= min(100, $availPct) ?>%; background: #10b981;"></div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; font-weight: 600;">
                            <span>Currently Issued (<?= $issuedPct ?>%)</span>
                            <span style="color: #2563eb;"><?= number_format($stats['issued_books']) ?></span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: <?= min(100, $issuedPct) ?>%; background: #2563eb;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 5. E. RECENT ACTIVITY COMPACT FEED & H. DEPARTMENT OVERVIEW (2-COL) -->
    <div class="grid-2-col">

        <!-- E. RECENT ACTIVITY COMPACT FEED (5-6 ITEMS ONLY) -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">⚡ Recent Library Activity</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Latest Member Circulation Events</span>
                </div>
                <a href="/library/reports/circulation" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View All Activity &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(37,99,235,0.1); color:#2563eb; display:flex; align-items:center; justify-content:center; font-weight:700;">📖</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">John Doe (2026-CSE-001)</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Issued <em>"Database System Concepts"</em></div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">10 mins ago</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(16,185,129,0.1); color:#10b981; display:flex; align-items:center; justify-content:center; font-weight:700;">📥</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">Jane Smith (2026-CSE-002)</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Returned <em>"Digital Signal Processing"</em></div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">25 mins ago</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.6rem; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(99,102,241,0.1); color:#6366f1; display:flex; align-items:center; justify-content:center; font-weight:700;">🔄</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">Rahul Sharma (2026-ECE-014)</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Renewed <em>"Microelectronic Circuits"</em></div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">1 hour ago</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(245,158,11,0.1); color:#f59e0b; display:flex; align-items:center; justify-content:center; font-weight:700;">📖</div>
                        <div>
                            <div style="font-weight: 700; font-size: 0.84375rem; color: var(--text-primary);">Dr. Alan Turing (Faculty)</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Issued <em>"Introduction to Algorithms"</em></div>
                        </div>
                    </div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">2 hours ago</span>
                </div>
            </div>
        </div>

        <!-- H. DEPARTMENT OVERVIEW & I. ANNOUNCEMENTS -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Department Overview -->
            <div class="card-panel">
                <div class="card-panel-header">
                    <h3 class="card-panel-title">🏛️ Department Library Usage</h3>
                    <a href="/library/reports/students" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View Details &rarr;
                    </a>
                </div>

                <table class="table" style="margin: 0;">
                    <thead>
                        <tr>
                            <th>Dept</th>
                            <th>Members</th>
                            <th>Books Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deptUsage as $du): ?>
                            <tr>
                                <td><strong style="color: var(--accent-color);"><?= e($du['code']) ?></strong></td>
                                <td style="font-weight: 600;"><?= number_format($du['active_members']) ?></td>
                                <td><span class="badge badge-info"><?= number_format($du['books_issued']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- I. ANNOUNCEMENTS -->
            <div class="card-panel">
                <div class="card-panel-header">
                    <h3 class="card-panel-title">📢 Library Circulars</h3>
                    <a href="/announcements" style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                        View All &rarr;
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 0.8125rem; color: var(--text-primary);">Extended Examination Timings</strong>
                            <span class="badge badge-danger" style="font-size: 0.65rem;">Urgent</span>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                            Central Library open until 8:00 PM during end-semester exams.
                        </p>
                    </div>

                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <strong style="font-size: 0.8125rem; color: var(--text-primary);">IEEE E-Journal Subscription Renewed</strong>
                            <span class="badge badge-info" style="font-size: 0.65rem;">Info</span>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0.25rem 0 0 0;">
                            Access IEEE Xplore digital library on campus Wi-Fi.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

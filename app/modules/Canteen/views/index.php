<?php
/**
 * Modern Overview Canteen Manager Dashboard — Kuppam Engineering College
 * 
 * Strict Architecture: Overview Dashboard ONLY.
 * High-level statistics, sales overview, menu summary, stock alerts, and quick actions.
 * NO full food tables, Add Menu forms, or Order Carts on this page.
 */
$todayDate = date('l, d F Y');
$hour      = (int) date('H');
$greeting  = ($hour < 12) ? 'Good Morning' : (($hour < 17) ? 'Good Afternoon' : 'Good Evening');

$menuItems   = (int)($stats['total_menu_items'] ?? 42);
$availItems  = (int)($stats['available_items'] ?? 36);
$todOrders   = (int)($stats['todays_orders'] ?? 285);
$todSales    = (float)($stats['todays_sales'] ?? 18450.00);
$lowStock    = (int)($stats['low_stock_items'] ?? 6);
$pendOrders  = (int)($stats['pending_orders'] ?? 18);

$compOrders  = (int)($orderStatus['completed'] ?? 240);
$prepOrders  = (int)($orderStatus['preparing'] ?? 12);
$cancOrders  = (int)($orderStatus['cancelled'] ?? 15);
?>

<style>
    .canteen-dashboard-wrapper {
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

<div class="canteen-dashboard-wrapper">

    <!-- 5. DASHBOARD WELCOME -->
    <div class="welcome-banner">
        <div>
            <h1><?= $greeting ?>, Canteen Manager 👋</h1>
            <p>Here's your canteen and mess overview for today at <strong>Kuppam Engineering College</strong>.</p>
        </div>
        <div class="date-badge">
            📅 <span><?= $todayDate ?></span>
        </div>
    </div>

    <!-- 6. CANTEEN STATISTICS (6 COMPACT CARDS) -->
    <div class="stats-grid">
        <!-- 1. Menu Items -->
        <a href="/canteen/menu" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Menu Items</span>
                    <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">🍱</div>
                </div>
                <div class="stat-number"><?= number_format($menuItems) ?></div>
                <div class="stat-trend" style="color: #2563eb;">
                    <span>View Menu &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 2. Available Items -->
        <a href="/canteen/menu" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Available Items</span>
                    <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">✅</div>
                </div>
                <div class="stat-number"><?= number_format($availItems) ?></div>
                <div class="stat-trend" style="color: #10b981;">
                    <span>In Stock &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 3. Today's Orders -->
        <a href="/canteen/orders" class="stat-card-link">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Today's Orders</span>
                    <div class="stat-icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">🧾</div>
                </div>
                <div class="stat-number"><?= number_format($todOrders) ?></div>
                <div class="stat-trend" style="color: #6366f1;">
                    <span>View Orders &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 4. Today's Sales -->
        <a href="/canteen/orders" class="stat-card-link">
            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <div class="stat-header">
                    <span class="stat-label">Today's Sales</span>
                    <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">💰</div>
                </div>
                <div class="stat-number" style="color: #059669;">₹<?= number_format($todSales, 0) ?></div>
                <div class="stat-trend" style="color: #059669;">
                    <span>Total Revenue &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 5. Low Stock Items -->
        <a href="/canteen/inventory" class="stat-card-link">
            <div class="stat-card" style="border-left: 4px solid var(--danger);">
                <div class="stat-header">
                    <span class="stat-label">Low Stock Items</span>
                    <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">⚠️</div>
                </div>
                <div class="stat-number" style="color: var(--danger);"><?= number_format($lowStock) ?></div>
                <div class="stat-trend" style="color: var(--danger);">
                    <span>Stock Warning &rarr;</span>
                </div>
            </div>
        </a>

        <!-- 6. Pending Orders -->
        <a href="/canteen/orders" class="stat-card-link">
            <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                <div class="stat-header">
                    <span class="stat-label">Pending Orders</span>
                    <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">⏳</div>
                </div>
                <div class="stat-number" style="color: #d97706;"><?= number_format($pendOrders) ?></div>
                <div class="stat-trend" style="color: #d97706;">
                    <span>In Kitchen &rarr;</span>
                </div>
            </div>
        </a>
    </div>

    <!-- 7. QUICK ACTIONS -->
    <div class="card-panel">
        <div class="card-panel-header" style="margin-bottom: 1rem;">
            <h3 class="card-panel-title">⚡ Quick Actions</h3>
            <span style="font-size: 0.75rem; color: var(--text-secondary); font-weight: 600;">Navigation Shortcuts</span>
        </div>
        <div class="quick-actions-grid">
            <a href="/canteen/menu" class="qa-btn">
                <div class="qa-icon" style="background: #2563eb;">➕</div>
                <span class="qa-title">+ Add Menu Item</span>
            </a>

            <a href="/canteen/menu" class="qa-btn">
                <div class="qa-icon" style="background: #7c3aed;">🍱</div>
                <span class="qa-title">View Menu</span>
            </a>

            <a href="/canteen/orders" class="qa-btn">
                <div class="qa-icon" style="background: #059669;">🧾</div>
                <span class="qa-title">New Order</span>
            </a>

            <a href="/canteen/inventory" class="qa-btn">
                <div class="qa-icon" style="background: #0284c7;">📦</div>
                <span class="qa-title">Manage Stock</span>
            </a>

            <a href="/canteen/orders" class="qa-btn">
                <div class="qa-icon" style="background: #d97706;">💰</div>
                <span class="qa-title">View Sales</span>
            </a>

            <a href="/canteen/announcements" class="qa-btn">
                <div class="qa-icon" style="background: #6366f1;">📢</div>
                <span class="qa-title">View Announcements</span>
            </a>
        </div>
    </div>

    <!-- 8. TODAY'S SALES & 9. ORDER STATUS OVERVIEWS (2-COL) -->
    <div class="grid-2-col">

        <!-- 8. TODAY'S SALES OVERVIEW -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">💰 Today's Sales Overview</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Daily revenue &amp; order volume</span>
                </div>
                <a href="/canteen/orders" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View Sales &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; text-align: center;">
                    <div style="background: var(--bg-main); padding: 0.6rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.68rem; color: var(--text-secondary);">Total Orders</div>
                        <div style="font-size: 1rem; font-weight: 800;"><?= number_format($todOrders) ?></div>
                    </div>
                    <div style="background: rgba(16,185,129,0.08); padding: 0.6rem; border-radius: 8px; border: 1px solid rgba(16,185,129,0.2);">
                        <div style="font-size: 0.68rem; color: #10b981; font-weight: 600;">Completed</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #10b981;"><?= number_format($compOrders) ?></div>
                    </div>
                    <div style="background: rgba(245,158,11,0.08); padding: 0.6rem; border-radius: 8px; border: 1px solid rgba(245,158,11,0.2);">
                        <div style="font-size: 0.68rem; color: #f59e0b; font-weight: 600;">Pending</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #d97706;"><?= number_format($pendOrders) ?></div>
                    </div>
                    <div style="background: rgba(239,68,68,0.08); padding: 0.6rem; border-radius: 8px; border: 1px solid rgba(239,68,68,0.2);">
                        <div style="font-size: 0.68rem; color: var(--danger); font-weight: 600;">Cancelled</div>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--danger);"><?= number_format($cancOrders) ?></div>
                    </div>
                </div>

                <div style="background: var(--bg-main); padding: 0.875rem 1rem; border-radius: 10px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.875rem; font-weight: 600;">Total Sales Collection:</span>
                    <strong style="font-size: 1.3rem; color: #10b981;">₹<?= number_format($todSales, 2) ?></strong>
                </div>
            </div>
        </div>

        <!-- 9. ORDER STATUS OVERVIEW -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">📊 Today's Order Status</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Kitchen preparation pipeline</span>
                </div>
                <a href="/canteen/orders" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View Orders &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Completed Orders</span>
                        <span style="color: #10b981; font-weight: 700;"><?= $compOrders ?> Orders</span>
                    </div>
                    <div class="progress-bar-bg" style="margin: 0;">
                        <div class="progress-bar-fill" style="width: 84.2%; background: #10b981;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Preparing in Kitchen</span>
                        <span style="color: #2563eb; font-weight: 700;"><?= $prepOrders ?> Orders</span>
                    </div>
                    <div class="progress-bar-bg" style="margin: 0;">
                        <div class="progress-bar-fill" style="width: 4.2%; background: #2563eb;"></div>
                    </div>
                </div>

                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Pending Counter Pickup</span>
                        <span style="color: #f59e0b; font-weight: 700;"><?= $pendOrders ?> Orders</span>
                    </div>
                    <div class="progress-bar-bg" style="margin: 0;">
                        <div class="progress-bar-fill" style="width: 6.3%; background: #f59e0b;"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 10. MENU OVERVIEW & 11. INVENTORY SUMMARY (2-COL) -->
    <div class="grid-2-col">

        <!-- 10. MENU OVERVIEW -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🍱 Today's Menu Overview</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Featured daily dishes &amp; stock status</span>
                </div>
                <a href="/canteen/menu" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    Manage Menu &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                <div style="display: flex; justify-content: space-between; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.25rem;">
                    <span>Total: <?= number_format($menuSum['total_items']) ?> Items</span>
                    <span>Available: <strong style="color:#10b981;"><?= $menuSum['available'] ?></strong></span>
                    <span>Out of Stock: <strong style="color:var(--danger);"><?= $menuSum['out_of_stock'] ?></strong></span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <?php foreach ($menuSum['featured_items'] as $fi): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg-main); padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                            <span style="font-weight: 700; color: var(--text-primary);"><?= e($fi['item_name']) ?></span>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <strong style="color: #2563eb;">₹<?= number_format((float)$fi['price'], 2) ?></strong>
                                <span class="badge badge-success" style="font-size: 0.65rem;">Available</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- 11. INVENTORY / STOCK SUMMARY & 13. LOW STOCK ALERT -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">📦 Inventory &amp; Stock Summary</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Kitchen ingredients &amp; raw stock</span>
                </div>
                <a href="/canteen/inventory" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View Inventory &rarr;
                </a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; text-align: center;">
                    <div style="background: var(--bg-main); padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color);">
                        <div style="font-size: 0.65rem; color: var(--text-secondary);">Total Stock</div>
                        <div style="font-size: 1rem; font-weight: 800;"><?= $invSum['total_items'] ?></div>
                    </div>
                    <div style="background: rgba(16,185,129,0.08); padding: 0.5rem; border-radius: 6px; border: 1px solid rgba(16,185,129,0.2);">
                        <div style="font-size: 0.65rem; color: #10b981; font-weight: 600;">In Stock</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #10b981;"><?= $invSum['in_stock'] ?></div>
                    </div>
                    <div style="background: rgba(245,158,11,0.08); padding: 0.5rem; border-radius: 6px; border: 1px solid rgba(245,158,11,0.2);">
                        <div style="font-size: 0.65rem; color: #f59e0b; font-weight: 600;">Low Stock</div>
                        <div style="font-size: 1rem; font-weight: 800; color: #d97706;"><?= $invSum['low_stock'] ?></div>
                    </div>
                    <div style="background: rgba(239,68,68,0.08); padding: 0.5rem; border-radius: 6px; border: 1px solid rgba(239,68,68,0.2);">
                        <div style="font-size: 0.65rem; color: var(--danger); font-weight: 600;">Out</div>
                        <div style="font-size: 1rem; font-weight: 800; color: var(--danger);"><?= $invSum['out_of_stock'] ?></div>
                    </div>
                </div>

                <!-- 13. LOW STOCK ALERTS -->
                <div style="border-top: 1px solid var(--border-color); padding-top: 0.6rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--danger); margin-bottom: 0.4rem;">⚠️ Low Stock Ingredient Warnings</div>
                    <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.75rem;">
                        <?php foreach ($invSum['alerts'] as $al): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(245,158,11,0.06); padding: 0.4rem 0.6rem; border-radius: 6px;">
                                <span><strong><?= e($al['name']) ?></strong> (<?= e($al['units']) ?>)</span>
                                <span class="badge badge-warning" style="font-size: 0.65rem; background:#f59e0b; color:#fff;"><?= e($al['status']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 12. RECENT ORDERS & 14. POPULAR FOOD ITEMS (2-COL) -->
    <div class="grid-2-col">

        <!-- 12. RECENT ORDERS (COMPACT 5 ITEMS) -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🧾 Recent Orders</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Latest customer orders feed</span>
                </div>
                <a href="/canteen/orders" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                    View All Orders &rarr;
                </a>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="margin: 0; font-size: 0.8125rem;">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Student</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $ro): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: var(--accent-color);"><?= e($ro['id']) ?></td>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($ro['customer']) ?></td>
                                <td><?= e($ro['items']) ?></td>
                                <td style="font-weight: 700;">₹<?= number_format((float)$ro['amount'], 2) ?></td>
                                <td>
                                    <?php if ($ro['status'] === 'completed'): ?>
                                        <span class="badge badge-success">Completed</span>
                                    <?php elseif ($ro['status'] === 'preparing'): ?>
                                        <span class="badge badge-info">Preparing</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning" style="background:#f59e0b; color:#fff;"><?= e(ucfirst($ro['status'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--text-secondary);"><?= e($ro['time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 14. POPULAR FOOD ITEMS -->
        <div class="card-panel">
            <div class="card-panel-header">
                <div>
                    <h3 class="card-panel-title">🔥 Popular Food Items</h3>
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Top ordered dishes today</span>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.8125rem;">
                <?php foreach ($popularItems as $idx => $pi): ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: var(--bg-main); border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <span style="font-weight: 800; color: var(--accent-color); width: 18px;"><?= $idx + 1 ?>.</span>
                            <strong style="color: var(--text-primary);"><?= e($pi['name']) ?></strong>
                        </div>
                        <span class="badge badge-info"><?= number_format($pi['orders']) ?> orders</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- 15. ANNOUNCEMENTS -->
    <div class="card-panel">
        <div class="card-panel-header">
            <h3 class="card-panel-title">📢 Canteen Announcements</h3>
            <a href="/canteen/announcements" style="font-size: 0.8125rem; font-weight: 700; color: var(--accent-color); text-decoration: none;">
                View All &rarr;
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                    <strong style="font-size: 0.8125rem; color: var(--text-primary);">Special Lunch Menu Available Today</strong>
                    <span class="badge badge-info" style="font-size: 0.65rem;">Today</span>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">
                    South Indian Special Thali &amp; Paneer Combo available during afternoon mess hours.
                </p>
            </div>

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                    <strong style="font-size: 0.8125rem; color: var(--text-primary);">Canteen Timing Extended During Exams</strong>
                    <span class="badge badge-secondary" style="font-size: 0.65rem;">Yesterday</span>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">
                    Night snack counter open till 10:30 PM for students preparing for exams.
                </p>
            </div>

            <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 8px; padding: 0.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.25rem;">
                    <strong style="font-size: 0.8125rem; color: var(--text-primary);">New Healthy Snack Items Added</strong>
                    <span class="badge badge-secondary" style="font-size: 0.65rem;">2 days ago</span>
                </div>
                <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">
                    Fresh fruit juices, salads, and sprout bowls now available on daily menu.
                </p>
            </div>
        </div>
    </div>

</div>

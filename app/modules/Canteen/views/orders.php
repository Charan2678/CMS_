<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">🧾 Canteen Orders &amp; Live Tracking</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Track active meal orders, kitchen preparation statuses, and view previous order receipts</p>
    </div>
    <a href="/canteen/menu" class="btn btn-primary" style="font-size: 0.8125rem;">🍱 Browse Canteen Menu</a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<?php if (!empty($canManage) && !empty($salesSum)): ?>
    <!-- Sales Summary Cards (Staff Only) -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; text-align: center;">
            <div style="font-size: 0.8125rem; color: var(--text-secondary); font-weight: 600;">TOTAL ORDERS TODAY</div>
            <div style="font-size: 1.65rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;"><?= number_format($salesSum['total_orders'] ?? 0) ?></div>
        </div>
        <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #10b981;">
            <div style="font-size: 0.8125rem; color: #10b981; font-weight: 600;">COMPLETED ORDERS</div>
            <div style="font-size: 1.65rem; font-weight: 800; color: #10b981; margin-top: 0.2rem;"><?= number_format($salesSum['completed_orders'] ?? 0) ?></div>
        </div>
        <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #f59e0b;">
            <div style="font-size: 0.8125rem; color: #f59e0b; font-weight: 600;">PENDING / PREPARING</div>
            <div style="font-size: 1.65rem; font-weight: 800; color: #d97706; margin-top: 0.2rem;"><?= number_format($salesSum['pending_orders'] ?? 0) ?></div>
        </div>
        <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #2563eb;">
            <div style="font-size: 0.8125rem; color: #2563eb; font-weight: 600;">TOTAL REVENUE</div>
            <div style="font-size: 1.65rem; font-weight: 800; color: #2563eb; margin-top: 0.2rem;">₹<?= number_format((float)($salesSum['total_sales'] ?? 0), 2) ?></div>
        </div>
    </div>
<?php endif; ?>

<!-- Orders Table -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
        <span>My Canteen Meal Orders &amp; Status Log (<?= count($allOrders) ?> Orders)</span>
        <span style="font-size: 0.75rem; color: var(--text-secondary);">Real-Time Kitchen Sync</span>
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date &amp; Time</th>
                    <th>Ordered Food Items</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Order Status</th>
                    <?php if (!empty($canManage)): ?>
                        <th>Update Status</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($allOrders)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No meal orders found. <a href="/canteen/menu">Click here to order food</a>.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($allOrders as $o): ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: 700; color: var(--accent-color);"><?= e($o['order_number']) ?></td>
                            <td style="font-size: 0.8125rem; color: var(--text-secondary);"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></td>
                            <td style="font-weight: 700; color: var(--text-primary);"><?= e($o['item_name']) ?></td>
                            <td style="font-weight: 600;"><?= (int)$o['quantity'] ?></td>
                            <td style="font-weight: 800; font-size: 1rem;">₹<?= number_format((float)$o['total_price'], 2) ?></td>
                            <td><span class="badge badge-secondary"><?= e(str_replace('_', ' ', $o['payment_method'])) ?></span></td>
                            <td>
                                <?php if ($o['order_status'] === 'completed'): ?>
                                    <span class="badge badge-success">✅ Completed</span>
                                <?php elseif ($o['order_status'] === 'preparing'): ?>
                                    <span class="badge badge-info">🔵 Preparing</span>
                                <?php elseif ($o['order_status'] === 'ready'): ?>
                                    <span class="badge badge-success" style="background:#059669; color:#fff;">🟢 Ready for Pickup</span>
                                <?php elseif ($o['order_status'] === 'cancelled'): ?>
                                    <span class="badge badge-danger">🔴 Cancelled</span>
                                <?php else: ?>
                                    <span class="badge badge-warning" style="background: #f59e0b; color: #fff;">🟠 Pending / Placed</span>
                                <?php endif; ?>
                            </td>
                            <?php if (!empty($canManage)): ?>
                                <td>
                                    <form method="POST" action="/canteen/orders" style="margin: 0; display: inline-flex; gap: 0.3rem;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="order_status" class="form-control" style="font-size: 0.75rem; padding: 0.2rem 0.4rem;" onchange="this.form.submit()">
                                            <option value="placed" <?= $o['order_status'] === 'placed' ? 'selected' : '' ?>>Pending / Placed</option>
                                            <option value="preparing" <?= $o['order_status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                                            <option value="ready" <?= $o['order_status'] === 'ready' ? 'selected' : '' ?>>Ready for Pickup</option>
                                            <option value="completed" <?= $o['order_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= $o['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

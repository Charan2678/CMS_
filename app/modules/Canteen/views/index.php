<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: <?= !empty($canManage) ? '1fr 2fr' : '1fr' ?>; gap: 1.5rem; margin-bottom: 2rem;">
    <?php if (!empty($canManage)): ?>
    <!-- Manager Add Item Panel -->
    <div class="card" style="height: fit-content;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add Canteen Menu Item
        </h2>

        <form method="POST" action="/canteen">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="add_item">

            <div class="form-group">
                <label class="form-label" for="item_name">Food Item Name *</label>
                <input type="text" id="item_name" name="item_name" class="form-control" required placeholder="e.g. Masala Dosa / Veg Thali">
            </div>

            <div class="form-group">
                <label class="form-label" for="category">Category *</label>
                <select id="category" name="category" class="form-control" required>
                    <option value="Breakfast">Breakfast</option>
                    <option value="Lunch">Lunch</option>
                    <option value="Snacks">Snacks & Fast Food</option>
                    <option value="Beverages">Beverages & Juice</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="price">Price (₹) *</label>
                <input type="number" step="0.50" id="price" name="price" class="form-control" required placeholder="40.00">
            </div>

            <div class="form-group">
                <label class="form-label" for="stock_status">Stock Status</label>
                <select id="stock_status" name="stock_status" class="form-control">
                    <option value="available">In Stock / Available</option>
                    <option value="out_of_stock">Out of Stock</option>
                </select>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Add Menu Item</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Menu & Ordering Table -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>☕</span> Canteen & Mess Food Menu
        </h2>

        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th style="text-align: right;">Order Food</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary);">No food items available in the menu currently.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $i): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--text-primary);"><?= e($i['item_name']) ?></td>
                                <td style="color: var(--text-secondary);"><?= e($i['category']) ?></td>
                                <td style="font-weight: 700; color: var(--success);">₹<?= number_format((float)$i['price'], 2) ?></td>
                                <td>
                                    <?php if ($i['stock_status'] === 'available'): ?>
                                        <span class="badge badge-success">AVAILABLE</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">OUT OF STOCK</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ($i['stock_status'] === 'available'): ?>
                                        <form method="POST" action="/canteen" style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_action" value="place_order">
                                            <input type="hidden" name="item_id" value="<?= $i['id'] ?>">

                                            <select name="quantity" class="form-control" style="padding: 0.35rem 0.5rem; font-size: 0.8125rem; width: 65px;">
                                                <option value="1">1x</option>
                                                <option value="2">2x</option>
                                                <option value="3">3x</option>
                                                <option value="4">4x</option>
                                                <option value="5">5x</option>
                                            </select>

                                            <select name="payment_method" class="form-control" style="padding: 0.35rem 0.5rem; font-size: 0.8125rem; width: 130px;">
                                                <option value="pay_at_counter">Pay at Counter</option>
                                                <option value="online_upi">Online UPI</option>
                                            </select>

                                            <button type="submit" class="btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8125rem;">
                                                <span>🛒</span> Order
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: var(--text-secondary); font-size: 0.8125rem;">Unavailable</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- My Food Orders & Tokens (For Logged In Users) -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>🎟️</span> My Canteen Orders & Food Tokens
    </h2>

    <?php if (empty($myOrders)): ?>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">You haven't placed any canteen food orders yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Token No</th>
                        <th>Item</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Total Price</th>
                        <th>Payment</th>
                        <th>Order Date</th>
                        <th style="text-align: right;">Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myOrders as $o): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--accent-color); font-family: monospace;"><?= e($o['order_number']) ?></td>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= e($o['item_name']) ?></td>
                            <td style="text-align: center; color: var(--text-primary);"><?= $o['quantity'] ?></td>
                            <td style="text-align: right; color: var(--success); font-weight: 700;">₹<?= number_format((float)$o['total_price'], 2) ?></td>
                            <td>
                                <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase;"><?= str_replace('_', ' ', $o['payment_method']) ?></span>
                                <?php if ($o['payment_status'] === 'paid'): ?>
                                    <span class="badge badge-success">PAID</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">PENDING</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--text-secondary); font-size: 0.8125rem;"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <?php if ($o['order_status'] === 'placed'): ?>
                                    <span class="badge badge-info">Order Placed</span>
                                <?php elseif ($o['order_status'] === 'preparing'): ?>
                                    <span class="badge badge-warning">🍳 Preparing</span>
                                <?php elseif ($o['order_status'] === 'ready'): ?>
                                    <span class="badge badge-success">🔔 Ready for Pickup</span>
                                <?php elseif ($o['order_status'] === 'completed'): ?>
                                    <span class="badge badge-info">Served</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Cancelled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($canManage)): ?>
<!-- Kitchen Dispatch Management Board (For Canteen Manager) -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>🍳</span> Kitchen Live Orders & Dispatch Board
    </h2>

    <?php if (empty($allOrders)): ?>
        <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active student orders received yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>User</th>
                        <th>Item & Qty</th>
                        <th style="text-align: right;">Amount</th>
                        <th>Order Time</th>
                        <th style="text-align: right;">Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allOrders as $ao): ?>
                        <tr>
                            <td style="font-weight: 700; color: var(--accent-color); font-family: monospace;"><?= e($ao['order_number']) ?></td>
                            <td style="color: var(--text-primary);"><?= e($ao['username'] ?? 'Student') ?></td>
                            <td style="font-weight: 600; color: var(--text-primary);"><?= e($ao['item_name']) ?> <span style="color: var(--accent-color);">(x<?= $ao['quantity'] ?>)</span></td>
                            <td style="text-align: right; color: var(--success); font-weight: 700;">₹<?= number_format((float)$ao['total_price'], 2) ?></td>
                            <td style="color: var(--text-secondary); font-size: 0.8125rem;"><?= date('h:i A', strtotime($ao['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <form method="POST" action="/canteen" style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_action" value="update_order_status">
                                    <input type="hidden" name="order_id" value="<?= $ao['id'] ?>">

                                    <select name="order_status" class="form-control" style="padding: 0.3rem 0.5rem; font-size: 0.8125rem; width: 130px;">
                                        <option value="placed" <?= $ao['order_status'] === 'placed' ? 'selected' : '' ?>>Placed</option>
                                        <option value="preparing" <?= $ao['order_status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                                        <option value="ready" <?= $ao['order_status'] === 'ready' ? 'selected' : '' ?>>Ready for Pickup</option>
                                        <option value="completed" <?= $ao['order_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= $ao['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>

                                    <button type="submit" class="btn-primary" style="padding: 0.3rem 0.625rem; font-size: 0.8125rem;">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

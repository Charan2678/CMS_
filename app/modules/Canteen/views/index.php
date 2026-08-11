<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        <?= e($success) ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: <?= !empty($canManage) ? '1fr 2fr' : '2fr 1fr' ?>; gap: 1.5rem; margin-bottom: 2rem;">
    <?php if (!empty($canManage)): ?>
    <!-- Manager Add Item Panel -->
    <div class="card" style="height: fit-content; border-top: 4px solid var(--accent-color);">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add Canteen Menu Item
        </h2>

        <form method="POST" action="/canteen">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="add_item">

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="item_name" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Food Item Name *</label>
                <input type="text" id="item_name" name="item_name" class="form-control" required placeholder="e.g. Masala Dosa / Veg Thali" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="category" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Category *</label>
                <select id="category" name="category" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);" required>
                    <option value="Breakfast">Breakfast</option>
                    <option value="Lunch">Lunch</option>
                    <option value="Snacks">Snacks & Fast Food</option>
                    <option value="Beverages">Beverages & Juice</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label" for="price" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Price (₹) *</label>
                    <input type="number" step="0.50" id="price" name="price" class="form-control" required placeholder="40.00" style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
                <div class="form-group">
                    <label class="form-label" for="stock_quantity" style="font-weight: 600; font-size: 0.8125rem; display: block; margin-bottom: 0.35rem;">Daily Stock Qty *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="50" min="0" class="form-control" required style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary);">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.6rem; font-weight: 700;">Add Menu Item</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Food Menu Table -->
    <div class="card" style="border-top: 4px solid var(--accent-color);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                <span>☕</span> Canteen &amp; Mess Food Menu
            </h2>
            <span class="badge badge-info"><?= count($items) ?> Items Available</span>
        </div>

        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                        <th style="padding: 0.65rem 0.75rem;">Item Name</th>
                        <th style="padding: 0.65rem 0.75rem;">Category</th>
                        <th style="padding: 0.65rem 0.75rem;">Price</th>
                        <th style="padding: 0.65rem 0.75rem;">Stock Status</th>
                        <th style="padding: 0.65rem 0.75rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem 0; color: var(--text-secondary);">No food items available in the menu currently.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $i): ?>
                            <?php $inStock = ($i['stock_status'] === 'available') && ((int)$i['stock_quantity'] > 0); ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem; font-weight: 700; color: var(--text-primary);">
                                    <?= e($i['item_name']) ?>
                                </td>
                                <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($i['category']) ?></td>
                                <td style="padding: 0.75rem; font-weight: 700; color: var(--success); font-size: 0.9375rem;">
                                    ₹<?= number_format((float)$i['price'], 2) ?>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <?php if ($inStock): ?>
                                        <span class="badge badge-success">IN STOCK (<?= e($i['stock_quantity']) ?>)</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">OUT OF STOCK</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem; text-align: right;">
                                    <?php if ($inStock): ?>
                                        <button type="button" onclick="addToCart(<?= $i['id'] ?>, '<?= addslashes($i['item_name']) ?>', <?= (float)$i['price'] ?>, <?= (int)$i['stock_quantity'] ?>)" class="btn btn-sm btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700;">
                                            🛒 Add to Order
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Unavailable</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Multi-Item Shopping Cart Drawer -->
    <div class="card" style="border-top: 4px solid var(--success); height: fit-content;">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 0.4rem;"><span>🛒</span> Meal Order Cart</span>
            <span id="cartCountBadge" class="badge badge-info">0 Items</span>
        </h2>

        <div id="cartItemsContainer" style="min-height: 120px; max-height: 250px; overflow-y: auto; margin-bottom: 1rem;">
            <p id="emptyCartText" style="text-align: center; color: var(--text-secondary); font-size: 0.8125rem; padding: 2rem 0;">Your cart is empty. Click "+ Add to Order" on food items.</p>
        </div>

        <div style="border-top: 2px solid var(--border-color); padding-top: 1rem; margin-bottom: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.1rem; font-weight: 800; color: var(--text-primary);">
                <span>Total Amount:</span>
                <span id="cartTotalDisplay" style="color: var(--success);">₹0.00</span>
            </div>
        </div>

        <form method="POST" action="/canteen" id="cartOrderForm" onsubmit="return validateCartSubmit()">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="place_cart_order">
            <input type="hidden" name="cart_json" id="cartJsonInput" value="[]">

            <div class="form-group" style="margin-bottom: 0.75rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.75rem; display: block; margin-bottom: 0.25rem;">Payment Option</label>
                <select name="payment_method" class="form-control" style="width: 100%; padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-size: 0.8125rem;">
                    <option value="pay_at_counter">💵 Pay at Canteen Counter (Cash/Card)</option>
                    <option value="upi_qr">📱 UPI QR Scan at Counter</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" style="font-weight: 600; font-size: 0.75rem; display: block; margin-bottom: 0.25rem;">Special Notes (Optional)</label>
                <input type="text" name="notes" placeholder="e.g. Extra spicy, no onions" class="form-control" style="width: 100%; padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-surface); color: var(--text-primary); font-size: 0.8125rem;">
            </div>

            <button type="submit" id="checkoutBtn" class="btn btn-success" style="width: 100%; padding: 0.75rem; font-weight: 800; font-size: 0.9375rem;" disabled>
                Confirm &amp; Generate Food Token
            </button>
        </form>
    </div>
</div>

<!-- Order History Tables -->
<?php if (!empty($canManage) && !empty($allOrders)): ?>
<!-- Manager Live Orders Queue -->
<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>📋</span> Live Orders &amp; Mess Queue
    </h2>

    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                    <th style="padding: 0.65rem 0.75rem;">Token / Order #</th>
                    <th style="padding: 0.65rem 0.75rem;">User / Customer</th>
                    <th style="padding: 0.65rem 0.75rem;">Items Ordered</th>
                    <th style="padding: 0.65rem 0.75rem;">Total</th>
                    <th style="padding: 0.65rem 0.75rem;">Payment</th>
                    <th style="padding: 0.65rem 0.75rem;">Order Status</th>
                    <th style="padding: 0.65rem 0.75rem; text-align: center;">Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allOrders as $ord): ?>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 0.75rem; font-family: monospace; font-weight: 800; color: var(--accent-color);">
                            <?= e($ord['order_number']) ?>
                        </td>
                        <td style="padding: 0.75rem;">
                            <div style="font-weight: 600; color: var(--text-primary);"><?= e($ord['username'] ?? 'Customer') ?></div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary);"><?= date('d M, h:i A', strtotime($ord['created_at'])) ?></div>
                        </td>
                        <td style="padding: 0.75rem;">
                            <div style="font-weight: 600; color: var(--text-primary);"><?= e($ord['item_name']) ?></div>
                            <?php if (!empty($ord['items'])): ?>
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.2rem;">
                                    <?php foreach ($ord['items'] as $li): ?>
                                        <span>&bull; <?= e($li['item_name']) ?> &times; <?= $li['quantity'] ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 0.75rem; font-weight: 700; color: var(--success);">
                            ₹<?= number_format((float)$ord['total_price'], 2) ?>
                        </td>
                        <td style="padding: 0.75rem;">
                            <?php if ($ord['payment_status'] === 'paid'): ?>
                                <span class="badge badge-success">PAID</span>
                            <?php else: ?>
                                <span class="badge badge-warning">PENDING (<?= e($ord['payment_method']) ?>)</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 0.75rem;">
                            <span class="badge badge-info" style="text-transform: uppercase;">🍳 <?= e($ord['order_status']) ?></span>
                        </td>
                        <td style="padding: 0.75rem; text-align: center;">
                            <form method="POST" action="/canteen" style="display: inline-flex; gap: 0.35rem; align-items: center;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_action" value="update_order_status">
                                <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">

                                <select name="order_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; width: auto;">
                                    <option value="placed" <?= $ord['order_status'] === 'placed' ? 'selected' : '' ?>>Placed</option>
                                    <option value="preparing" <?= $ord['order_status'] === 'preparing' ? 'selected' : '' ?>>Preparing</option>
                                    <option value="ready" <?= $ord['order_status'] === 'ready' ? 'selected' : '' ?>>Ready (Notify)</option>
                                    <option value="completed" <?= $ord['order_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $ord['order_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>

                                <button type="submit" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Customer My Orders Table -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>📜</span> My Order History &amp; Food Tokens
    </h2>

    <?php if (empty($myOrders)): ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 2rem 0;">You haven't placed any food orders yet.</p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-color); text-align: left; background: var(--bg-main);">
                        <th style="padding: 0.65rem 0.75rem;">Food Token</th>
                        <th style="padding: 0.65rem 0.75rem;">Items</th>
                        <th style="padding: 0.65rem 0.75rem;">Date &amp; Time</th>
                        <th style="padding: 0.65rem 0.75rem;">Amount</th>
                        <th style="padding: 0.65rem 0.75rem;">Payment</th>
                        <th style="padding: 0.65rem 0.75rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myOrders as $mo): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-family: monospace; font-weight: 800; color: var(--accent-color);">
                                <?= e($mo['order_number']) ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <div style="font-weight: 600; color: var(--text-primary);"><?= e($mo['item_name']) ?></div>
                                <?php if (!empty($mo['items'])): ?>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.2rem;">
                                        <?php foreach ($mo['items'] as $li): ?>
                                            <span>&bull; <?= e($li['item_name']) ?> (&times;<?= $li['quantity'] ?>) </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem; color: var(--text-secondary); white-space: nowrap;">
                                <?= date('d M, h:i A', strtotime($mo['created_at'])) ?>
                            </td>
                            <td style="padding: 0.75rem; font-weight: 700; color: var(--success);">
                                ₹<?= number_format((float)$mo['total_price'], 2) ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ($mo['payment_status'] === 'paid'): ?>
                                    <span class="badge badge-success">PAID</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Pay at Counter</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ($mo['order_status'] === 'ready'): ?>
                                    <span class="badge badge-success" style="font-size: 0.75rem; animation: pulse 2s infinite;">🎉 READY FOR PICKUP</span>
                                <?php elseif ($mo['order_status'] === 'completed'): ?>
                                    <span class="badge badge-secondary">COMPLETED</span>
                                <?php elseif ($mo['order_status'] === 'preparing'): ?>
                                    <span class="badge badge-info">🍳 PREPARING</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">PLACED</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
let cart = [];

function addToCart(id, name, price, maxStock) {
    const existing = cart.find(item => item.item_id === id);
    if (existing) {
        if (existing.quantity < maxStock) {
            existing.quantity++;
        } else {
            alert('Cannot add more: Max available stock reached (' + maxStock + ')');
        }
    } else {
        cart.push({ item_id: id, item_name: name, unit_price: price, quantity: 1, max_stock: maxStock });
    }
    renderCart();
}

function updateCartQty(id, delta) {
    const item = cart.find(i => i.item_id === id);
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) {
        cart = cart.filter(i => i.item_id !== id);
    } else if (item.quantity > item.max_stock) {
        item.quantity = item.max_stock;
        alert('Max available stock reached');
    }
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItemsContainer');
    const totalDisplay = document.getElementById('cartTotalDisplay');
    const countBadge = document.getElementById('cartCountBadge');
    const jsonInput = document.getElementById('cartJsonInput');
    const checkoutBtn = document.getElementById('checkoutBtn');

    if (cart.length === 0) {
        container.innerHTML = '<p id="emptyCartText" style="text-align: center; color: var(--text-secondary); font-size: 0.8125rem; padding: 2rem 0;">Your cart is empty. Click "+ Add to Order" on food items.</p>';
        totalDisplay.innerText = '₹0.00';
        countBadge.innerText = '0 Items';
        jsonInput.value = '[]';
        checkoutBtn.disabled = true;
        return;
    }

    let total = 0;
    let count = 0;
    let html = '';

    cart.forEach(item => {
        const subtotal = item.unit_price * item.quantity;
        total += subtotal;
        count += item.quantity;

        html += `
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px dashed var(--border-color); font-size: 0.8125rem;">
                <div style="flex: 1;">
                    <div style="font-weight: 700; color: var(--text-primary);">${item.item_name}</div>
                    <div style="font-size: 0.7rem; color: var(--text-secondary);">₹${item.unit_price.toFixed(2)} &times; ${item.quantity} = ₹${subtotal.toFixed(2)}</div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.35rem;">
                    <button type="button" onclick="updateCartQty(${item.item_id}, -1)" style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 4px; width: 24px; height: 24px; cursor: pointer;">-</button>
                    <span style="font-weight: 700; min-width: 16px; text-align: center;">${item.quantity}</span>
                    <button type="button" onclick="updateCartQty(${item.item_id}, 1)" style="background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 4px; width: 24px; height: 24px; cursor: pointer;">+</button>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
    totalDisplay.innerText = '₹' + total.toFixed(2);
    countBadge.innerText = count + ' Items';
    jsonInput.value = JSON.stringify(cart.map(i => ({ item_id: i.item_id, quantity: i.quantity })));
    checkoutBtn.disabled = false;
}

function validateCartSubmit() {
    if (cart.length === 0) {
        alert('Please add items to cart before submitting.');
        return false;
    }
    return true;
}
</script>

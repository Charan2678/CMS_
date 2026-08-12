<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">🍱 Canteen &amp; Mess Food Menu</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Browse fresh food items, customize order quantities, and place online meal orders</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <?php if (!empty($canManage)): ?>
            <a href="/canteen" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Canteen Overview</a>
        <?php else: ?>
            <a href="/canteen/orders" class="btn btn-primary" style="font-size: 0.8125rem;">🧾 My Canteen Orders &amp; Status</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Complete Food Menu Cards / Table -->
    <div class="card">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
            <span>Today's Available Canteen Menu (<?= count($items) ?> Items)</span>
            <span style="font-size: 0.75rem; color: var(--text-secondary);">Freshly Prepared Dishes</span>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; padding: 1.25rem;">
            <?php foreach ($items as $it): ?>
                <div style="background: var(--bg-main); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.15rem; display: flex; flex-direction: column; justify-content: space-between; gap: 0.875rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.35rem;">
                            <h3 style="font-size: 1rem; font-weight: 800; color: var(--text-primary); margin: 0;"><?= e($it['item_name']) ?></h3>
                            <span class="badge badge-info" style="font-size: 0.7rem;"><?= e($it['category']) ?></span>
                        </div>
                        <div style="font-size: 1.15rem; font-weight: 800; color: #2563eb;">₹<?= number_format((float)$it['price'], 2) ?></div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem;">
                            Stock: 
                            <strong style="color: <?= (int)$it['stock_quantity'] > 0 ? '#10b981' : 'var(--danger)' ?>;">
                                <?= (int)$it['stock_quantity'] > 0 ? (int)$it['stock_quantity'] . ' Available' : 'Out of Stock' ?>
                            </strong>
                        </div>
                    </div>

                    <?php if ($it['stock_status'] === 'available' && (int)$it['stock_quantity'] > 0): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                            <div style="display: inline-flex; align-items: center; border: 1px solid var(--border-color); border-radius: 6px; overflow: hidden; background: var(--bg-surface);">
                                <button type="button" onclick="changeQty(<?= $it['id'] ?>, -1)" style="border: none; background: transparent; padding: 0.3rem 0.6rem; cursor: pointer; font-weight: 800;">-</button>
                                <span id="qty_val_<?= $it['id'] ?>" style="padding: 0 0.5rem; font-weight: 700; font-size: 0.875rem;">1</span>
                                <button type="button" onclick="changeQty(<?= $it['id'] ?>, 1)" style="border: none; background: transparent; padding: 0.3rem 0.6rem; cursor: pointer; font-weight: 800;">+</button>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addToCart(<?= $it['id'] ?>, '<?= e($it['item_name']) ?>', <?= (float)$it['price'] ?>)" style="font-size: 0.8125rem; font-weight: 700;">
                                Add to Cart 🛒
                            </button>
                        </div>
                    <?php else: ?>
                        <div style="border-top: 1px solid var(--border-color); padding-top: 0.75rem;">
                            <span class="badge badge-danger" style="display: block; text-align: center; padding: 0.4rem;">Out of Stock</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Student Food Cart & Management -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Cart Drawer Panel -->
        <div class="card" style="border-top: 4px solid #10b981;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center;">
                <span>🛒 Student Food Cart</span>
                <span id="cartCountBadge" class="badge badge-success" style="font-size: 0.75rem;">0 Items</span>
            </div>
            <div style="padding: 1.25rem;">
                <div id="cartItemList" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
                    <div style="color: var(--text-secondary); text-align: center; padding: 1.5rem; background: var(--bg-main); border-radius: 8px; font-size: 0.8125rem;">
                        Your cart is empty.<br>Select items from the food menu to add.
                    </div>
                </div>

                <div style="border-top: 2px dashed var(--border-color); padding-top: 0.875rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 700; font-size: 0.9375rem;">Total Amount:</span>
                    <strong id="cartTotalDisplay" style="font-size: 1.35rem; color: #10b981;">₹0.00</strong>
                </div>

                <form method="POST" action="/canteen/menu" id="checkoutForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="place_cart_order">
                    <input type="hidden" name="cart_json" id="cartJsonInput">

                    <button type="button" id="placeOrderBtn" onclick="submitFoodCart()" disabled class="btn btn-success" style="width: 100%; background: #10b981; border-color: #10b981; font-weight: 800; padding: 0.75rem; opacity: 0.6; cursor: not-allowed;">
                        Place Order 💳
                    </button>
                </form>
            </div>
        </div>

        <?php if ($canManage): ?>
            <!-- Add Menu Item Form (Canteen Staff Only) -->
            <div class="card">
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
                    ➕ Add Canteen Menu Item
                </div>
                <form method="POST" action="/canteen/menu" style="padding: 1.25rem;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_action" value="add_item">

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Food Item Name *</label>
                        <input type="text" name="item_name" placeholder="e.g. Paneer Butter Masala" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Category</label>
                        <select name="category" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                            <option value="Breakfast">Breakfast</option>
                            <option value="Lunch">Lunch</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Beverages">Beverages</option>
                            <option value="Desserts">Desserts</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Price (₹) *</label>
                        <input type="number" name="price" value="50" step="5" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label class="form-label" style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.25rem;">Daily Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="50" min="0" class="form-control" required style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Add Item to Menu</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    let cart = [];

    function changeQty(itemId, delta) {
        const qtyEl = document.getElementById('qty_val_' + itemId);
        if (!qtyEl) return;
        let current = parseInt(qtyEl.innerText) || 1;
        current = Math.max(1, current + delta);
        qtyEl.innerText = current;
    }

    function addToCart(id, name, price) {
        const qtyEl = document.getElementById('qty_val_' + id);
        const qty = parseInt(qtyEl ? qtyEl.innerText : 1) || 1;

        const existing = cart.find(c => c.item_id === id);
        if (existing) {
            existing.quantity += qty;
        } else {
            cart.push({ item_id: id, item_name: name, unit_price: price, quantity: qty });
        }

        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(c => c.item_id !== id);
        renderCart();
    }

    function updateCartQty(id, delta) {
        const item = cart.find(c => c.item_id === id);
        if (!item) return;
        item.quantity += delta;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cartItemList');
        const countBadge = document.getElementById('cartCountBadge');
        const totalDisplay = document.getElementById('cartTotalDisplay');
        const btn = document.getElementById('placeOrderBtn');

        if (cart.length === 0) {
            container.innerHTML = `
                <div style="color: var(--text-secondary); text-align: center; padding: 1.5rem; background: var(--bg-main); border-radius: 8px; font-size: 0.8125rem;">
                    Your cart is empty.<br>Select items from the food menu to add.
                </div>
            `;
            countBadge.innerText = '0 Items';
            totalDisplay.innerText = '₹0.00';
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.style.cursor = 'not-allowed';
            return;
        }

        let total = 0;
        let html = '';

        cart.forEach(item => {
            const subtotal = item.unit_price * item.quantity;
            total += subtotal;

            html += `
                <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg-main); padding: 0.6rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                    <div>
                        <div style="font-weight: 700; font-size: 0.8125rem; color: var(--text-primary);">${item.item_name}</div>
                        <div style="font-size: 0.725rem; color: var(--text-secondary);">${item.quantity} × ₹${item.unit_price.toFixed(2)}</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.6rem;">
                        <div style="display: inline-flex; align-items: center; border: 1px solid var(--border-color); border-radius: 4px; background: #fff;">
                            <button type="button" onclick="updateCartQty(${item.item_id}, -1)" style="border:none; background:none; padding:0.1rem 0.4rem; cursor:pointer; font-weight:700;">-</button>
                            <span style="font-size:0.75rem; font-weight:700; padding:0 0.3rem;">${item.quantity}</span>
                            <button type="button" onclick="updateCartQty(${item.item_id}, 1)" style="border:none; background:none; padding:0.1rem 0.4rem; cursor:pointer; font-weight:700;">+</button>
                        </div>
                        <strong style="font-size:0.875rem; color:var(--text-primary);">₹${subtotal.toFixed(2)}</strong>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        countBadge.innerText = cart.length + ' Item' + (cart.length > 1 ? 's' : '');
        totalDisplay.innerText = '₹' + total.toFixed(2);
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    }

    function submitFoodCart() {
        if (cart.length === 0) return;
        document.getElementById('cartJsonInput').value = JSON.stringify(cart);
        document.getElementById('checkoutForm').submit();
    }
</script>

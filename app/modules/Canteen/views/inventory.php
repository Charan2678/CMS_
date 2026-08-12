<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0;">📦 Inventory &amp; Raw Material Stock Ledger</h1>
        <p style="color: var(--text-secondary); font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Monitor kitchen ingredients, raw material stock, minimum reorder thresholds, and low stock warnings</p>
    </div>
    <a href="/canteen" class="btn btn-secondary" style="font-size: 0.8125rem;">&larr; Back to Canteen Overview</a>
</div>

<!-- Stock Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card" style="padding: 1.25rem; text-align: center;">
        <div style="font-size: 0.8125rem; color: var(--text-secondary); font-weight: 600;">TOTAL STOCK ITEMS</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: var(--text-primary); margin-top: 0.2rem;"><?= number_format($invSum['total_items']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #10b981;">
        <div style="font-size: 0.8125rem; color: #10b981; font-weight: 600;">IN STOCK</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #10b981; margin-top: 0.2rem;"><?= number_format($invSum['in_stock']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid #f59e0b;">
        <div style="font-size: 0.8125rem; color: #f59e0b; font-weight: 600;">LOW STOCK ITEMS</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: #d97706; margin-top: 0.2rem;"><?= number_format($invSum['low_stock']) ?></div>
    </div>
    <div class="card" style="padding: 1.25rem; text-align: center; border-left: 4px solid var(--danger);">
        <div style="font-size: 0.8125rem; color: var(--danger); font-weight: 600;">OUT OF STOCK</div>
        <div style="font-size: 1.65rem; font-weight: 800; color: var(--danger); margin-top: 0.2rem;"><?= number_format($invSum['out_of_stock']) ?></div>
    </div>
</div>

<!-- Stock Inventory Table -->
<div class="card">
    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); font-weight: 700;">
        Raw Material &amp; Ingredient Stock Ledger
    </div>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Minimum Stock</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invSum['alerts'] as $al): ?>
                    <tr>
                        <td style="font-weight: 700; color: var(--text-primary);"><?= e($al['name']) ?></td>
                        <td><span class="badge badge-info">Ingredients</span></td>
                        <td style="font-weight: 700; color: var(--danger);"><?= e($al['units']) ?></td>
                        <td>25 Units</td>
                        <td>Units / Litres</td>
                        <td><span class="badge badge-warning" style="background: #f59e0b; color: #fff;"><?= e($al['status']) ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="alert('Stock reorder request placed for <?= e($al['name']) ?>')">+ Reorder Stock</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td style="font-weight: 700; color: var(--text-primary);">Sona Masoori Rice</td>
                    <td><span class="badge badge-info">Grains</span></td>
                    <td style="font-weight: 700; color: #10b981;">150 kg</td>
                    <td>50 kg</td>
                    <td>Kilograms</td>
                    <td><span class="badge badge-success">In Stock</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="alert('Stock details updated.')">Adjust Stock</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

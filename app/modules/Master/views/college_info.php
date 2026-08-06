<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<!-- Institutional Header Banner -->
<div class="card" style="margin-bottom: 2rem; border-top: 4px solid var(--accent-color);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 64px; height: 64px; border-radius: 12px; background: rgba(2, 132, 199, 0.12); color: var(--accent-color); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; border: 1px solid var(--border-color);">
                🏛️
            </div>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <?= e($college['name'] ?? 'Kuppam Engineering College') ?>
                    <span class="badge badge-success" style="font-size: 0.75rem;">Verified Campus</span>
                </h1>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
                    Code: <strong style="color: var(--accent-color);"><?= e($college['code'] ?? 'KEC') ?></strong> &bull; Affiliated to <?= e($college['affiliation_body'] ?? 'JNTUA University') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <span>⚙️</span> Manage Institution Master Profile
    </h2>

    <form method="POST" action="/master/colleges">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label class="form-label" for="name">Official College Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= e($college['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Short Code / Abbreviation *</label>
                <input type="text" id="code" name="code" class="form-control" value="<?= e($college['code'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Official Email Address</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($college['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone / Contact Number</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= e($college['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="website">Website Portal URL</label>
                <input type="url" id="website" name="website" class="form-control" value="<?= e($college['website'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="affiliation_body">Affiliation Body / University</label>
                <input type="text" id="affiliation_body" name="affiliation_body" class="form-control" value="<?= e($college['affiliation_body'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="affiliation_number">Affiliation Code / Registration No.</label>
                <input type="text" id="affiliation_number" name="affiliation_number" class="form-control" value="<?= e($college['affiliation_number'] ?? '') ?>">
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label class="form-label" for="address">Permanent Campus Address</label>
                <textarea id="address" name="address" class="form-control" rows="3"><?= e($college['address'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="city">City</label>
                <input type="text" id="city" name="city" class="form-control" value="<?= e($college['city'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="state">State</label>
                <input type="text" id="state" name="state" class="form-control" value="<?= e($college['state'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="pincode">Pincode</label>
                <input type="text" id="pincode" name="pincode" class="form-control" value="<?= e($college['pincode'] ?? '') ?>">
            </div>
        </div>

        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.75rem 2.5rem; font-weight: 700;">Save Institutional Profile</button>
        </div>
    </form>
</div>

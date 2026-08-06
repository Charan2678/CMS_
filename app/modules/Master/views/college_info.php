<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h2 class="panel-title">Manage College Profile</h2>
    </div>

    <form method="POST" action="/master/colleges">
        <?= csrf_field() ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <div class="form-group">
                <label class="form-label" for="name">Official College Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= e($college['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Short Code / Abbreviation *</label>
                <input type="text" id="code" name="code" class="form-control" value="<?= e($college['code'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Official Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= e($college['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">Phone / Contact</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?= e($college['phone'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="website">Website URL</label>
                <input type="url" id="website" name="website" class="form-control" value="<?= e($college['website'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="affiliation_body">Affiliation Body / University</label>
                <input type="text" id="affiliation_body" name="affiliation_body" class="form-control" value="<?= e($college['affiliation_body'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="affiliation_number">Affiliation Code / Number</label>
                <input type="text" id="affiliation_number" name="affiliation_number" class="form-control" value="<?= e($college['affiliation_number'] ?? '') ?>">
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label class="form-label" for="address">Address</label>
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

        <div style="margin-top: 1.5rem; text-align: right;">
            <button type="submit" class="btn-primary" style="width: auto; padding: 0.75rem 2rem;">Save College Information</button>
        </div>
    </form>
</div>

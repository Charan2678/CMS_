<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<!-- Page Header & Title Banner -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.625rem; letter-spacing: -0.015em;">
            <?= icon('landmark', 'icon-md') ?> Institution Master Profile
        </h1>
        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
            Configure official college details, affiliation bodies, contact info, and campus address
        </div>
    </div>
    <div>
        <span class="badge badge-success" style="font-size: 0.8125rem; padding: 0.45rem 0.9rem;"><?= icon('shield-check', 'icon-xs') ?> Verified Campus &bull; ESTD 2001</span>
    </div>
</div>

<!-- College Hero Summary Card -->
<div class="card" style="margin-bottom: 1.75rem; border-top: 4px solid var(--accent-color);">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, rgba(2, 132, 199, 0.16) 0%, rgba(2, 132, 199, 0.06) 100%); color: var(--accent-color); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; border: 1px solid rgba(2, 132, 199, 0.25); box-shadow: var(--shadow-xs);">
                <?= icon('school', 'icon-xl') ?>
            </div>
            <div>
                <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.01em;">
                    <?= e($college['name'] ?? 'Kuppam Engineering College') ?>
                </h2>
                <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.35rem;">
                    Short Code: <strong style="color: var(--accent-color); font-weight: 800;"><?= e($college['code'] ?? 'KEC') ?></strong> &bull; Affiliations: <strong style="color: var(--text-primary); font-weight: 700;"><?= e($college['affiliation_body'] ?? 'JNTUA University') ?></strong>
                </div>
            </div>
        </div>
        
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Official Support Email</div>
                <div style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary);"><?= e($college['email'] ?? 'admin@gitm.edu') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Main Setup Form Card -->
<div class="card">
    <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <?= icon('settings', 'icon-sm') ?> Official Campus Setup Form
    </h2>

    <form method="POST" action="/master/colleges">
        <?= csrf_field() ?>

        <!-- Section 1: General Details -->
        <div style="background: var(--bg-main); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem; box-shadow: var(--shadow-xs);">
            <h3 style="font-size: 0.9375rem; font-weight: 800; color: var(--accent-color); margin-top: 0; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.06em; display: flex; align-items: center; gap: 0.45rem;">
                <?= icon('pin', 'icon-xs') ?> 1. General Institution Information
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="name">Official College Name *</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= e($college['name'] ?? '') ?>" required placeholder="Full College Name">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="code">Short Code / Abbreviation *</label>
                    <input type="text" id="code" name="code" class="form-control" value="<?= e($college['code'] ?? '') ?>" required placeholder="e.g. KEC">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="email">Official Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= e($college['email'] ?? '') ?>" placeholder="admin@college.edu">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="phone">Contact Helpline Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= e($college['phone'] ?? '') ?>" placeholder="+91 9876543210">
                </div>
            </div>
        </div>

        <!-- Section 2: Affiliation & Accreditation -->
        <div style="background: var(--bg-main); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem; box-shadow: var(--shadow-xs);">
            <h3 style="font-size: 0.9375rem; font-weight: 800; color: var(--accent-color); margin-top: 0; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.06em; display: flex; align-items: center; gap: 0.45rem;">
                <?= icon('graduation-cap', 'icon-xs') ?> 2. Affiliation &amp; Registration
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="website">Website Portal URL</label>
                    <input type="url" id="website" name="website" class="form-control" value="<?= e($college['website'] ?? '') ?>" placeholder="https://www.kec.ac.in">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="affiliation_body">Affiliating Body / University</label>
                    <input type="text" id="affiliation_body" name="affiliation_body" class="form-control" value="<?= e($college['affiliation_body'] ?? '') ?>" placeholder="e.g. JNTUA University">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="affiliation_number">Affiliation Code / Registration No.</label>
                    <input type="text" id="affiliation_number" name="affiliation_number" class="form-control" value="<?= e($college['affiliation_number'] ?? '') ?>" placeholder="Reg No / Code">
                </div>
            </div>
        </div>

        <!-- Section 3: Location Details -->
        <div style="background: var(--bg-main); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1.5rem; box-shadow: var(--shadow-xs);">
            <h3 style="font-size: 0.9375rem; font-weight: 800; color: var(--accent-color); margin-top: 0; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.06em; display: flex; align-items: center; gap: 0.45rem;">
                <?= icon('map-pin', 'icon-xs') ?> 3. Campus Location &amp; Permanent Address
            </h3>

            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 1.25rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="address">Permanent Campus Address</label>
                    <input type="text" id="address" name="address" class="form-control" value="<?= e($college['address'] ?? '') ?>" placeholder="Street / Campus Address">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="city">City</label>
                    <input type="text" id="city" name="city" class="form-control" value="<?= e($college['city'] ?? '') ?>" placeholder="City">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="state">State</label>
                    <input type="text" id="state" name="state" class="form-control" value="<?= e($college['state'] ?? '') ?>" placeholder="State">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="pincode">Pincode</label>
                    <input type="text" id="pincode" name="pincode" class="form-control" value="<?= e($college['pincode'] ?? '') ?>" placeholder="517425">
                </div>
            </div>
        </div>

        <!-- Form Actions Bar -->
        <div style="text-align: right; border-top: 1px solid var(--border-color); padding-top: 1.25rem;">
            <button type="submit" class="btn-primary" style="padding: 0.875rem 2.5rem; font-size: 0.9375rem;"><?= icon('save', 'icon-sm') ?> Save Institutional Profile</button>
        </div>
    </form>
</div>

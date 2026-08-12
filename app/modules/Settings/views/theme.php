<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Settings\views\theme.php
?>
<div class="container-fluid py-4" style="max-width: 1000px;">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert" style="border-radius: var(--border-radius); border-left: 5px solid var(--danger);">
            <span style="font-size: 1.2rem; margin-right: 0.5rem;">⚠️</span>
            <div><?= e($error) ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert" style="border-radius: var(--border-radius); border-left: 5px solid var(--success);">
            <span style="font-size: 1.2rem; margin-right: 0.5rem;">✅</span>
            <div><?= e($success) ?></div>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm" style="border-radius: var(--border-radius); overflow: hidden; background: var(--bg-surface);">
        <div class="card-header border-0 py-3" style="background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%); color: #fff;">
            <h4 class="mb-0 font-weight-bold" style="font-size: 1.25rem;">🎨 College Branding & Theme Customizer</h4>
            <p class="mb-0 text-white-50" style="font-size: 0.8125rem;">Configure system-wide visual aesthetics, primary colors, and official documents header details.</p>
        </div>

        <div class="card-body p-4">
            <form action="/settings/theme" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="college_name" class="form-label font-weight-bold" style="color: var(--text-primary);">College Name *</label>
                        <input type="text" class="form-control" id="college_name" name="college_name" value="<?= e($theme['college_name'] ?? 'Kuppam Engineering College') ?>" required style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                    </div>
                </div>

                <hr style="border-color: var(--border-color);">

                <div class="row mb-4">
                    <h5 class="col-12 font-weight-bold mb-3" style="color: var(--accent-color); font-size: 1.05rem;">🖌️ UI Color Scheme & Typography</h5>
                    
                    <div class="col-md-6 mb-3">
                        <label for="color_primary" class="form-label" style="color: var(--text-secondary);">Primary Accent Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control-color p-0 border-0" id="color_primary_picker" value="<?= e($theme['color_primary'] ?? '#0284c7') ?>" oninput="document.getElementById('color_primary').value = this.value" style="width: 45px; height: 38px; border-radius: var(--border-radius) 0 0 var(--border-radius); cursor: pointer;">
                            <input type="text" class="form-control" id="color_primary" name="color_primary" value="<?= e($theme['color_primary'] ?? '#0284c7') ?>" oninput="document.getElementById('color_primary_picker').value = this.value" style="border-radius: 0 var(--border-radius) var(--border-radius) 0; background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="color_secondary" class="form-label" style="color: var(--text-secondary);">Secondary (Gradient / Hover) Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control-color p-0 border-0" id="color_secondary_picker" value="<?= e($theme['color_secondary'] ?? '#0369a1') ?>" oninput="document.getElementById('color_secondary').value = this.value" style="width: 45px; height: 38px; border-radius: var(--border-radius) 0 0 var(--border-radius); cursor: pointer;">
                            <input type="text" class="form-control" id="color_secondary" name="color_secondary" value="<?= e($theme['color_secondary'] ?? '#0369a1') ?>" oninput="document.getElementById('color_secondary_picker').value = this.value" style="border-radius: 0 var(--border-radius) var(--border-radius) 0; background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="font_family" class="form-label" style="color: var(--text-secondary);">Font Family</label>
                        <select class="form-select form-control" id="font_family" name="font_family" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                            <?php $currFont = $theme['font_family'] ?? 'Inter'; ?>
                            <option value="Inter" <?= $currFont === 'Inter' ? 'selected' : '' ?>>Inter</option>
                            <option value="Outfit" <?= $currFont === 'Outfit' ? 'selected' : '' ?>>Outfit</option>
                            <option value="Poppins" <?= $currFont === 'Poppins' ? 'selected' : '' ?>>Poppins</option>
                            <option value="Roboto" <?= $currFont === 'Roboto' ? 'selected' : '' ?>>Roboto</option>
                            <option value="Montserrat" <?= $currFont === 'Montserrat' ? 'selected' : '' ?>>Montserrat</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="border_radius" class="form-label" style="color: var(--text-secondary);">Interface Border Radius</label>
                        <select class="form-select form-control" id="border_radius" name="border_radius" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                            <?php $currRadius = $theme['border_radius'] ?? '8px'; ?>
                            <option value="4px" <?= $currRadius === '4px' ? 'selected' : '' ?>>4px (Sharp)</option>
                            <option value="6px" <?= $currRadius === '6px' ? 'selected' : '' ?>>6px (Slight)</option>
                            <option value="8px" <?= $currRadius === '8px' ? 'selected' : '' ?>>8px (Default)</option>
                            <option value="12px" <?= $currRadius === '12px' ? 'selected' : '' ?>>12px (Rounded)</option>
                            <option value="16px" <?= $currRadius === '16px' ? 'selected' : '' ?>>16px (Pillowy)</option>
                        </select>
                    </div>
                </div>

                <hr style="border-color: var(--border-color);">

                <div class="row mb-4">
                    <h5 class="col-12 font-weight-bold mb-3" style="color: var(--accent-color); font-size: 1.05rem;">📁 Official Branding Assets</h5>
                    
                    <div class="col-md-4 mb-3">
                        <label for="logo" class="form-label" style="color: var(--text-secondary);">College Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                        <?php if (!empty($theme['logo_path'])): ?>
                            <div class="mt-2 p-2 border rounded d-inline-block" style="background: rgba(0,0,0,0.02);">
                                <img src="<?= e($theme['logo_path']) ?>" alt="Logo Preview" style="max-height: 50px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="letterhead" class="form-label" style="color: var(--text-secondary);">Official Letterhead Image</label>
                        <input type="file" class="form-control" id="letterhead" name="letterhead" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                        <?php if (!empty($theme['letterhead_path'])): ?>
                            <div class="mt-2 p-2 border rounded d-inline-block" style="background: rgba(0,0,0,0.02);">
                                <img src="<?= e($theme['letterhead_path']) ?>" alt="Letterhead Preview" style="max-height: 50px;">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="seal" class="form-label" style="color: var(--text-secondary);">Official Authority Seal</label>
                        <input type="file" class="form-control" id="seal" name="seal" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                        <?php if (!empty($theme['seal_path'])): ?>
                            <div class="mt-2 p-2 border rounded d-inline-block" style="background: rgba(0,0,0,0.02);">
                                <img src="<?= e($theme['seal_path']) ?>" alt="Seal Preview" style="max-height: 50px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr style="border-color: var(--border-color);">

                <div class="row mb-4">
                    <h5 class="col-12 font-weight-bold mb-3" style="color: var(--accent-color); font-size: 1.05rem;">📍 Address & Official Details</h5>
                    
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label" style="color: var(--text-secondary);">College Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);"><?= e($theme['address'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="contact_details" class="form-label" style="color: var(--text-secondary);">Contact Details (Phone / Email)</label>
                        <textarea class="form-control" id="contact_details" name="contact_details" rows="3" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);"><?= e($theme['contact_details'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gstin" class="form-label" style="color: var(--text-secondary);">GSTIN / Institutional Registry Code</label>
                        <input type="text" class="form-control" id="gstin" name="gstin" value="<?= e($theme['gstin'] ?? '') ?>" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                    </div>
                </div>

                <div class="text-right d-flex gap-2 justify-content-end">
                    <button type="reset" class="btn btn-light px-4 py-2" style="border-radius: var(--border-radius); font-weight: 600;">Reset</button>
                    <button type="submit" class="btn px-4 py-2 text-white" style="border-radius: var(--border-radius); font-weight: 600; background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);">Save Theme Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Settings\views\role_permissions.php
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

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="/settings/roles" class="btn btn-sm btn-light" style="border-radius: var(--border-radius); font-weight: 600;">
                ⬅️ Back to Roles
            </a>
        </div>
        <div class="text-right">
            <h5 class="mb-0 font-weight-bold" style="color: var(--text-primary);">Role: <?= e($role['name']) ?></h5>
            <small class="text-muted" style="font-family: monospace;"><?= e($role['code']) ?></small>
        </div>
    </div>

    <form action="/settings/roles/<?= (int) $role['id'] ?>/permissions" method="POST">
        <?= csrf_field() ?>

        <?php foreach ($modules as $mod): ?>
            <?php 
            $modId = (int) $mod['id'];
            $modPerms = $permissionsByModule[$modId] ?? [];
            if (empty($modPerms)) continue;
            ?>
            <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius); background: var(--bg-surface); overflow: hidden;">
                <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between" style="background: rgba(2,132,199,0.06); border-bottom: 1px solid var(--border-color) !important;">
                    <div class="d-flex align-items-center">
                        <span style="font-size: 1.2rem; margin-right: 0.5rem; color: var(--accent-color);">📦</span>
                        <h5 class="mb-0 font-weight-bold" style="font-size: 1rem; color: var(--text-primary);"><?= e($mod['name']) ?></h5>
                    </div>
                    <div>
                        <button type="button" class="btn btn-xxs btn-outline-secondary py-1 px-2" onclick="toggleModuleCheckboxes(<?= $modId ?>, true)" style="font-size: 0.6875rem; border-radius: 4px; font-weight: 600;">Select All</button>
                        <button type="button" class="btn btn-xxs btn-outline-secondary py-1 px-2" onclick="toggleModuleCheckboxes(<?= $modId ?>, false)" style="font-size: 0.6875rem; border-radius: 4px; font-weight: 600;">Clear</button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <?php foreach ($modPerms as $perm): ?>
                            <?php 
                            $permId = (int) $perm['id'];
                            $isChecked = in_array($permId, $activePerms, true);
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="form-check d-flex align-items-start gap-2">
                                    <input class="form-check-input mod-chk-<?= $modId ?>" type="checkbox" name="permissions[]" value="<?= $permId ?>" id="perm_<?= $permId ?>" <?= $isChecked ? 'checked' : '' ?> style="margin-top: 0.2rem; cursor: pointer; width: 18px; height: 18px; border-radius: 4px; border-color: var(--input-border);">
                                    <label class="form-check-label" for="perm_<?= $permId ?>" style="cursor: pointer; color: var(--text-primary); font-size: 0.875rem; line-height: 1.3;">
                                        <span class="font-weight-bold d-block" style="font-weight: 600;"><?= e($perm['name']) ?></span>
                                        <small class="text-muted" style="font-size: 0.75rem;"><?= e($perm['description']) ?></small>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="card border-0 shadow-sm" style="border-radius: var(--border-radius); background: var(--bg-surface);">
            <div class="card-body p-4 d-flex justify-content-end gap-2">
                <a href="/settings/roles" class="btn btn-light px-4 py-2" style="border-radius: var(--border-radius); font-weight: 600;">Cancel</a>
                <button type="submit" class="btn px-4 py-2 text-white" style="border-radius: var(--border-radius); font-weight: 600; background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);">
                    Save Role Permissions
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleModuleCheckboxes(modId, state) {
    const checkboxes = document.querySelectorAll('.mod-chk-' + modId);
    checkboxes.forEach(chk => {
        chk.checked = state;
    });
}
</script>

<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Settings\views\roles.php
?>
<div class="container-fluid py-4">
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

    <div class="row">
        <!-- Roles List Table -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: var(--border-radius); background: var(--bg-surface);">
                <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between" style="background: transparent;">
                    <div>
                        <h4 class="mb-0 font-weight-bold" style="font-size: 1.15rem; color: var(--text-primary);">🔑 System Roles</h4>
                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">Default system-defined roles and custom created organization hierarchies.</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" style="color: var(--text-primary);">
                            <thead style="background: var(--table-header-bg);">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-size: 0.75rem; padding: 0.75rem 1.5rem;">Role Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7ps-2" style="font-size: 0.75rem; padding: 0.75rem 1rem;">Role Code</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="font-size: 0.75rem; padding: 0.75rem 1rem;">Reports To (Hierarchy)</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="font-size: 0.75rem; padding: 0.75rem 1rem; width: 180px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $role): ?>
                                    <tr style="border-top: 1px solid var(--border-color); background: var(--bg-card); transition: all 0.2s;" onmouseover="this.style.background='var(--table-row-hover)'" onmouseout="this.style.background='var(--bg-card)'">
                                        <td style="padding: 1rem 1.5rem;">
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0 text-sm font-weight-bold" style="font-size: 0.875rem; color: var(--text-primary);">
                                                    <?= e($role['name']) ?>
                                                    <?php if ($role['is_system_role']): ?>
                                                        <span class="badge bg-secondary text-white ms-2" style="font-size: 0.65rem; border-radius: 4px;">System</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info text-white ms-2" style="font-size: 0.65rem; border-radius: 4px; background-color: var(--accent-color) !important;">Custom</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <small class="text-muted text-xs mt-1" style="font-size: 0.75rem;"><?= e($role['description'] ?: 'No description provided') ?></small>
                                            </div>
                                        </td>
                                        <td style="padding: 1rem 1rem; font-size: 0.8125rem; font-family: monospace; color: var(--text-secondary);">
                                            <?= e($role['code']) ?>
                                        </td>
                                        <td style="padding: 1rem 1rem; font-size: 0.8125rem;">
                                            <?php if (!empty($role['parent_name'])): ?>
                                                <span class="text-info font-weight-bold" style="color: var(--accent-color) !important;">👤 <?= e($role['parent_name']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">None (Top Level)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-center" style="padding: 1rem 1rem;">
                                            <a href="/settings/roles/<?= (int) $role['id'] ?>/permissions" class="btn btn-sm btn-outline-primary px-3" style="border-radius: var(--border-radius); font-size: 0.75rem; font-weight: 600; border-color: var(--accent-color); color: var(--accent-color);" onmouseover="this.style.background='var(--accent-color)'; this.style.color='#fff'" onmouseout="this.style.background='transparent'; this.style.color='var(--accent-color)'">
                                                ⚙️ Permissions
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Custom Role Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: var(--border-radius); background: var(--bg-surface);">
                <div class="card-header border-0 py-3" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0 font-weight-bold" style="font-size: 1.05rem; color: var(--text-primary);">➕ Create Custom Role</h5>
                </div>
                <div class="card-body">
                    <form action="/settings/roles" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="name" class="form-label" style="color: var(--text-secondary); font-size: 0.8125rem; font-weight: 600;">Role Name *</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Head of COE" required style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label" style="color: var(--text-secondary); font-size: 0.8125rem; font-weight: 600;">Role Code (Unique) *</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="e.g. head_of_coe" required style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text); font-family: monospace;">
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Only alphanumeric characters and underscores allowed.</small>
                        </div>

                        <div class="mb-3">
                            <label for="parent_role_id" class="form-label" style="color: var(--text-secondary); font-size: 0.8125rem; font-weight: 600;">Reports To (Hierarchy Parent)</label>
                            <select class="form-select form-control" id="parent_role_id" name="parent_role_id" style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);">
                                <option value="">-- None (Top Level) --</option>
                                <?php foreach ($roles as $rOption): ?>
                                    <option value="<?= (int) $rOption['id'] ?>"><?= e($rOption['name']) ?> (<?= e($rOption['code']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1" style="font-size: 0.7rem;">Configures approvals routing hierarchy mapping.</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label" style="color: var(--text-secondary); font-size: 0.8125rem; font-weight: 600;">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief outline of duties..." style="border-radius: var(--border-radius); background: var(--input-bg); border-color: var(--input-border); color: var(--input-text);"></textarea>
                        </div>

                        <button type="submit" class="btn w-100 py-2 text-white" style="border-radius: var(--border-radius); font-weight: 600; background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);">
                            Create Role
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

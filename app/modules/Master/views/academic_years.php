<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<!-- Page Header & Title Banner -->
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.625rem;">
            <span>📅</span> Academic Years Management
        </h1>
        <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">
            Define academic sessions, set active school years, and manage historical archives
        </div>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <span class="badge badge-success" style="font-size: 0.8125rem; padding: 0.5rem 1rem;">Active Session: 2026-2027</span>
    </div>
</div>

<div class="page-split">
    <!-- Form Card -->
    <div class="card" style="height: fit-content;">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add Academic Year
        </h2>

        <form method="POST" action="/master/academic-years">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="name">Academic Year Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. 2026-2027">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="start_date">Session Start Date *</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="end_date">Session End Date *</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.5rem; background: var(--bg-main); padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid var(--border-color);">
                <input type="checkbox" id="is_current" name="is_current" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                <label for="is_current" style="font-size: 0.875rem; color: var(--text-primary); cursor: pointer; font-weight: 700;">Set as Active Academic Session</label>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.875rem 1.5rem;">Create Academic Year</button>
        </form>
    </div>

    <!-- Directory Card -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📋</span> Configured Academic Sessions Directory
        </h2>

        <div style="overflow-x: auto; width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Academic Session</th>
                        <th>Session Duration</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($academicYears)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No academic years configured yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($academicYears as $ay): ?>
                            <tr>
                                <td style="font-weight: 800; font-size: 0.9375rem; color: var(--text-primary);"><?= e($ay['name']) ?></td>
                                <td style="color: var(--text-secondary); font-size: 0.8125rem; font-weight: 500;">
                                    📅 <?= date('d M Y', strtotime($ay['start_date'])) ?> &mdash; <?= date('d M Y', strtotime($ay['end_date'])) ?>
                                </td>
                                <td>
                                    <?php if ((int)$ay['is_current'] === 1): ?>
                                        <span class="badge badge-success">Active Session</span>
                                    <?php else: ?>
                                        <span class="badge badge-info" style="opacity: 0.7;">Archived</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right;">
                                    <?php if ((int)$ay['is_current'] !== 1): ?>
                                        <form method="POST" action="/master/academic-years" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_action" value="set_current">
                                            <input type="hidden" name="academic_year_id" value="<?= $ay['id'] ?>">
                                            <button type="submit" class="btn-primary" style="padding: 0.4rem 1rem; font-size: 0.75rem; border-radius: 8px;">Set Active</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size: 0.8125rem; color: var(--success); font-weight: 800; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <span>✓</span> Current Active
                                        </span>
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

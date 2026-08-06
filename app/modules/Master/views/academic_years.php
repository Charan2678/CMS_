<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; width: 100%;">
    <!-- Create Academic Year Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>➕</span> Add Academic Year
        </h2>

        <form method="POST" action="/master/academic-years">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="name">Academic Year Name *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. 2026-2027">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="start_date">Start Date *</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="end_date">End Date *</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;">
                <input type="checkbox" id="is_current" name="is_current" value="1">
                <label for="is_current" style="font-size: 0.875rem; color: var(--text-secondary); cursor: pointer;">Set as Active Academic Year</label>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Create Academic Year</button>
        </form>
    </div>

    <!-- Academic Years Directory Table Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📅</span> Academic Years Management
        </h2>

        <div style="overflow-x: auto; width: 100%;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Academic Session</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($academicYears)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No academic years configured yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($academicYears as $ay): ?>
                            <tr>
                                <td style="font-weight: 700; color: var(--text-primary);"><?= e($ay['name']) ?></td>
                                <td style="color: var(--text-secondary); font-size: 0.8125rem;">
                                    <?= date('d M Y', strtotime($ay['start_date'])) ?> to <?= date('d M Y', strtotime($ay['end_date'])) ?>
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
                                            <button type="submit" class="btn-primary" style="padding: 0.35rem 0.875rem; font-size: 0.75rem; border-radius: 6px;">Set Active</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="font-size: 0.75rem; color: var(--success); font-weight: 700;">Current</span>
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

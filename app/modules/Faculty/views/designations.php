<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Form Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Add Designation</h2>
        </div>

        <form method="POST" action="/faculty/designations">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">Designation Title *</label>
                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Senior Professor">
            </div>

            <div class="form-group">
                <label class="form-label" for="code">Designation Code</label>
                <input type="text" id="code" name="code" class="form-control" placeholder="e.g. sr_prof">
            </div>

            <div class="form-group">
                <label class="form-label" for="level">Seniority Level (1-10)</label>
                <input type="number" id="level" name="level" class="form-control" value="5" min="1" max="10">
            </div>

            <button type="submit" class="btn-primary">Create Designation</button>
        </form>
    </div>

    <!-- Table Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Designations Hierarchy</h2>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-color); text-align: left;">
                    <th style="padding: 0.75rem;">Seniority Rank</th>
                    <th style="padding: 0.75rem;">Designation Title</th>
                    <th style="padding: 0.75rem;">Code</th>
                    <th style="padding: 0.75rem;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($designations)): ?>
                    <tr>
                        <td colspan="4" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No designations configured yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($designations as $des): ?>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; font-weight: 700; color: #a5b4fc;">Level <?= e($des['level']) ?></td>
                            <td style="padding: 0.75rem; font-weight: 600;"><?= e($des['name']) ?></td>
                            <td style="padding: 0.75rem; color: var(--text-secondary);"><?= e($des['code']) ?></td>
                            <td style="padding: 0.75rem;">
                                <span class="badge badge-success">Active</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

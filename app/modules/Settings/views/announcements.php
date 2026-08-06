<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: <?= !empty($canBroadcast) ? '1fr 2fr' : '1fr' ?>; gap: 1.5rem;">
    <?php if (!empty($canBroadcast)): ?>
    <!-- Form Panel -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Broadcast Announcement</h2>
        </div>

        <form method="POST" action="/announcements">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="title">Announcement Title *</label>
                <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. Mid-Term Examination Schedule Released">
            </div>

            <div class="form-group">
                <label class="form-label" for="target_role">Target Audience *</label>
                <select id="target_role" name="target_role" class="form-control" required>
                    <option value="all">Everyone (All Roles)</option>
                    <option value="student">Students Only</option>
                    <option value="faculty">Faculty Only</option>
                    <option value="staff">Non-Faculty Staff Only</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="start_date">Display Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="end_date">Display End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="content">Announcement Message Body *</label>
                <textarea id="content" name="content" class="form-control" rows="4" required placeholder="Enter detailed announcement message..."></textarea>
            </div>

            <button type="submit" class="btn-primary">Broadcast Announcement</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Active Announcements Board -->
    <div class="panel">
        <div class="panel-header">
            <h2 class="panel-title">Active Announcements Board</h2>
        </div>

        <?php if (empty($announcements)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">No active announcements broadcasted.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($announcements as $anc): ?>
                    <div style="background: var(--bg-main); padding: 1.25rem; border-radius: 0.625rem; border: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary);"><?= e($anc['title']) ?></h3>
                            <span class="badge badge-info" style="text-transform: uppercase;">Audience: <?= e($anc['target_role'] ?? 'Everyone') ?></span>
                        </div>
                        <p style="color: var(--text-primary); font-size: 0.875rem; line-height: 1.5; margin-bottom: 0.75rem;">
                            <?= nl2br(e($anc['content'])) ?>
                        </p>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); display: flex; justify-content: space-between;">
                            <span>Published by: <?= e($anc['publisher_name'] ?? 'System Admin') ?></span>
                            <span>Date: <?= date('d M Y', strtotime($anc['publish_at'] ?? $anc['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

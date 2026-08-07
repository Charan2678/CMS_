<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?= e($error) ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: <?= !empty($canBroadcast) ? '1fr 2fr' : '1fr' ?>; gap: 1.5rem;">
    <?php if (!empty($canBroadcast)): ?>
    <!-- Create Announcement Form Panel -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📢</span> Broadcast New Circular
        </h2>

        <form method="POST" action="/announcements">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="title">Announcement Title *</label>
                <input type="text" id="title" name="title" class="form-control" required placeholder="e.g. Mid-Term Examination Schedule Released">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="target_role">Target Audience *</label>
                <select id="target_role" name="target_role" class="form-control" required>
                    <option value="all">Everyone (All Roles)</option>
                    <option value="student">Students Only</option>
                    <option value="faculty">Faculty Only</option>
                    <option value="staff">Non-Faculty Staff Only</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label" for="content">Announcement Message Body *</label>
                <textarea id="content" name="content" class="form-control" rows="4" required placeholder="Enter detailed announcement message..."></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%;">Broadcast Announcement</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Active Announcements Board -->
    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin-top: 0; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>📢</span> Campus Live Announcements Board
        </h2>

        <?php if (empty($announcements)): ?>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin: 0;">No active announcements broadcasted.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($announcements as $anc): ?>
                    <div style="background: var(--bg-main); padding: 1.25rem; border-radius: 8px; border: 1px solid var(--border-color);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0;"><?= e($anc['title']) ?></h3>
                            <span class="badge badge-info" style="text-transform: uppercase;">Target: <?= e($anc['target_role'] ?? 'Everyone') ?></span>
                        </div>
                        <p style="color: var(--text-primary); font-size: 0.875rem; line-height: 1.5; margin-bottom: 0.75rem;">
                            <?= nl2br(e($anc['content'])) ?>
                        </p>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); display: flex; justify-content: space-between;">
                            <span>📢 Published by <?= e($anc['publisher_name'] ?? 'System Admin') ?></span>
                            <span>📅 <?= date('d M Y', strtotime($anc['publish_at'] ?? $anc['created_at'] ?? 'now')) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Placement\views\applications.php
$isAdmin = in_array($role, ['super_admin', 'admin', 'tpo'], true);
?>
<div class="panel" style="width: 100%; max-width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">👨‍🎓 Student Placement Applications</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Monitor student applications, shortlist applicants, and track offer statuses.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="badge badge-danger" style="display: block; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; border-radius: 6px; text-align: left;">
            ⚠️ <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="badge badge-success" style="display: block; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; border-radius: 6px; text-align: left;">
            ✓ <?= e($success) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Candidate Placement Pipeline</h2>
        <div style="overflow-x: auto;">
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Student Name</th>
                        <th>Recruitment Drive</th>
                        <th>Application Date</th>
                        <th>Pipeline Status</th>
                        <?php if ($isAdmin): ?>
                            <th style="text-align: right;">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($applications)): ?>
                        <!-- Insert a mock row so TPO can see something in their pipeline first time -->
                        <tr style="border-top: 1px solid var(--border-color); background: var(--bg-card);">
                            <td style="font-family: monospace; font-weight: bold;">2026-CSE-001</td>
                            <td><strong>John Doe</strong></td>
                            <td><strong>TCS Ninja Recruitment 2026</strong><br><small class="text-secondary">Tata Consultancy Services</small></td>
                            <td><?= date('d-m-Y') ?></td>
                            <td><span class="badge badge-info">APPLIED</span></td>
                            <?php if ($isAdmin): ?>
                                <td style="text-align: right;">
                                    <form method="POST" action="/placement/applications" style="display: inline-block;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="application_id" value="1">
                                        <select name="status" class="form-control" style="width: 120px; display: inline-block; height: 32px; padding: 2px 6px; font-size: 0.75rem; vertical-align: middle;" onchange="this.form.submit()">
                                            <option value="applied" selected>Applied</option>
                                            <option value="shortlisted">Shortlisted</option>
                                            <option value="selected">Selected</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: bold;"><?= e($app['roll_number']) ?></td>
                                <td><strong><?= e($app['first_name'] . ' ' . $app['last_name']) ?></strong></td>
                                <td>
                                    <strong><?= e($app['drive_title']) ?></strong><br>
                                    <small class="text-secondary"><?= e($app['company_name']) ?></small>
                                </td>
                                <td><?= date('d-m-Y', strtotime($app['applied_date'])) ?></td>
                                <td>
                                    <?php if ($app['status'] === 'applied'): ?>
                                        <span class="badge badge-info" style="text-transform: uppercase;">Applied</span>
                                    <?php elseif ($app['status'] === 'shortlisted'): ?>
                                        <span class="badge badge-warning" style="text-transform: uppercase;">Shortlisted</span>
                                    <?php elseif ($app['status'] === 'selected'): ?>
                                        <span class="badge badge-success" style="text-transform: uppercase;">Selected</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger" style="text-transform: uppercase;">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($isAdmin): ?>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <form method="POST" action="/placement/applications" style="display: inline-block;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="application_id" value="<?= (int) $app['id'] ?>">
                                            <select name="status" class="form-control" style="width: 130px; display: inline-block; height: 32px; padding: 2px 6px; font-size: 0.75rem; vertical-align: middle;" onchange="this.form.submit()">
                                                <option value="applied" <?= $app['status'] === 'applied' ? 'selected' : '' ?>>Applied</option>
                                                <option value="shortlisted" <?= $app['status'] === 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                                                <option value="selected" <?= $app['status'] === 'selected' ? 'selected' : '' ?>>Selected (Offer)</option>
                                                <option value="rejected" <?= $app['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

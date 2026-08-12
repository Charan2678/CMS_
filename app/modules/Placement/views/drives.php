<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Placement\views\drives.php
$isAdmin = in_array($role, ['super_admin', 'admin', 'tpo'], true);
?>
<div class="panel" style="width: 100%; max-width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">💼 Recruitment & Placement Drives</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Manage active and upcoming campus recruitment cycles and candidate eligibility rules.</p>
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

    <div class="<?= $isAdmin ? 'page-split' : 'card' ?>">
        <?php if ($isAdmin): ?>
            <!-- Left Side: Creation Form -->
            <div class="card">
                <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem;">Create New Job Drive</h2>
                
                <form method="POST" action="/placement/drives" style="display: flex; flex-direction: column; gap: 1rem;">
                    <?= csrf_field() ?>
                    
                    <div>
                        <label class="form-label" for="company_id">Company Partner *</label>
                        <select name="company_id" id="company_id" class="form-control" required>
                            <option value="">-- Select Recruiter --</option>
                            <?php foreach ($companies as $comp): ?>
                                <option value="<?= (int) $comp['id'] ?>"><?= e($comp['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="title">Drive Title *</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="e.g. TCS Ninja Recruitment 2026" required>
                    </div>

                    <div>
                        <label class="form-label" for="designation">Target Designation *</label>
                        <input type="text" name="designation" id="designation" class="form-control" placeholder="e.g. Systems Engineer" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <div>
                            <label class="form-label" for="ctc_lpa">Salary CTC (LPA) *</label>
                            <input type="number" step="0.01" name="ctc_lpa" id="ctc_lpa" class="form-control" placeholder="4.50" required>
                        </div>
                        <div>
                            <label class="form-label" for="eligibility_cgpa">Min CGPA Cutoff</label>
                            <input type="number" step="0.01" name="eligibility_cgpa" id="eligibility_cgpa" class="form-control" placeholder="6.50">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                        <div>
                            <label class="form-label" for="max_backlogs">Max Backlogs</label>
                            <input type="number" name="max_backlogs" id="max_backlogs" class="form-control" placeholder="0" value="0">
                        </div>
                        <div>
                            <label class="form-label" for="drive_date">Drive Date *</label>
                            <input type="date" name="drive_date" id="drive_date" class="form-control" required style="height: 38px;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="location">Drive Venue *</label>
                        <input type="text" name="location" id="location" class="form-control" value="On-Campus" placeholder="e.g. Main Auditorium" required>
                    </div>

                    <div>
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="scheduled">Scheduled</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">
                        💾 Publish Placement Drive
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Right Side / Full Width: Drive Listings -->
        <div>
            <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Active Recruitment Drives</h2>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Company & Designation</th>
                            <th>Package & Cutoff</th>
                            <th>Schedule Date</th>
                            <th>Venue</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($drives)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 3rem;">📭 No active recruitment drives scheduled.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($drives as $drive): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($drive['title']) ?></strong><br>
                                        <small class="text-secondary"><?= e($drive['company_name']) ?> &bull; Designation: <?= e($drive['designation']) ?></small>
                                    </td>
                                    <td>
                                        <strong style="color: var(--accent-color);">₹<?= number_format((float)$drive['ctc_lpa'], 2) ?> LPA</strong><br>
                                        <small class="text-secondary">CGPA $\ge$ <?= number_format((float)$drive['eligibility_cgpa'], 1) ?> &bull; <?= (int)$drive['max_backlogs'] === 0 ? 'No Backlogs' : 'Max ' . $drive['max_backlogs'] . ' backlog' ?></small>
                                    </td>
                                    <td>
                                        <strong style="color: var(--text-primary);"><?= date('d M Y', strtotime($drive['drive_date'])) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?= e($drive['location']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($drive['status'] === 'scheduled'): ?>
                                            <span class="badge badge-info" style="text-transform: uppercase;">Scheduled</span>
                                        <?php elseif ($drive['status'] === 'ongoing'): ?>
                                            <span class="badge badge-warning" style="text-transform: uppercase;">Ongoing</span>
                                        <?php else: ?>
                                            <span class="badge badge-success" style="text-transform: uppercase;">Completed</span>
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
</div>

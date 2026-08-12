<?php
// C:\Users\G Jaswanth\Downloads\cms\app\modules\Placement\views\companies.php
$isAdmin = in_array($role, ['super_admin', 'admin', 'tpo'], true);
?>
<div class="panel" style="width: 100%; max-width: 100%;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; margin: 0;">🏢 Recruiter Corporate Partners</h1>
            <p style="color: var(--text-secondary); margin: 0.25rem 0 0 0; font-size: 0.875rem;">Manage partner corporate recruiters visiting for campus placements and internships.</p>
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
            <!-- Left Side: Registration Form -->
            <div class="card">
                <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem;">Register Corporate Partner</h2>
                
                <form method="POST" action="/placement/companies" style="display: flex; flex-direction: column; gap: 1rem;">
                    <?= csrf_field() ?>
                    
                    <div>
                        <label class="form-label" for="name">Company Name *</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Google India" required>
                    </div>

                    <div>
                        <label class="form-label" for="email">Recruiter Contact Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="e.g. campus@google.com">
                    </div>

                    <div>
                        <label class="form-label" for="phone">Contact Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="e.g. +91 80 2844xxxx">
                    </div>

                    <div>
                        <label class="form-label" for="website">Website / Career Link</label>
                        <input type="url" name="website" id="website" class="form-control" placeholder="e.g. https://careers.google.com">
                    </div>

                    <div>
                        <label class="form-label" for="address">Corporate Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3" placeholder="e.g. Bangalore Campus, India"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">
                        🏢 Save Recruiter Info
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Right Side: Recruiter List Table -->
        <div>
            <h2 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1rem;">Corporate Partners List</h2>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Contact Details</th>
                            <th>Website</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($companies)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 3rem;">📭 No corporate partners registered yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($companies as $comp): ?>
                                <tr>
                                    <td>
                                        <strong><?= e($comp['name']) ?></strong><br>
                                        <small class="text-secondary"><?= e($comp['address']) ?></small>
                                    </td>
                                    <td>
                                        <?= !empty($comp['email']) ? '📧 ' . e($comp['email']) : '' ?><br>
                                        <?= !empty($comp['phone']) ? '📞 ' . e($comp['phone']) : '' ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($comp['website'])): ?>
                                            <a href="<?= e($comp['website']) ?>" target="_blank" style="color: var(--accent-color); font-weight: 600; text-decoration: none;">Link &rarr;</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('d-m-Y', strtotime($comp['created_at'])) ?>
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

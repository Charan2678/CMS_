<?php
/**
 * My Account — General profile page for Admin / Staff / Faculty roles.
 * Students and Parents are directed to the full Student Profile page instead.
 */
?>
<div class="page-header" style="margin-bottom: 1.5rem;">
    <h1 style="font-size:1.4rem;font-weight:700;color:var(--text-primary);margin:0;">My Account</h1>
    <p style="color:var(--text-secondary);font-size:0.875rem;margin-top:0.25rem;">View your account details and manage your password.</p>
</div>

<?php if (!empty($error ?? null)): ?>
    <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
<?php endif; ?>
<?php if (!empty($success ?? null)): ?>
    <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:900px;">

    <!-- ── Account Information Card ── -->
    <div class="card" style="grid-column:1/-1;">
        <div class="card-header" style="display:flex;align-items:center;gap:0.75rem;padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
            <div style="width:56px;height:56px;border-radius:50%;background:var(--accent-color);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;flex-shrink:0;">
                <?php
                    $name = $_SESSION['username'] ?? 'U';
                    echo strtoupper(substr($name, 0, 1));
                ?>
            </div>
            <div>
                <div style="font-size:1.1rem;font-weight:700;color:var(--text-primary);"><?= e($username) ?></div>
                <div style="font-size:0.8125rem;color:var(--accent-color);font-weight:600;"><?= e($roleName) ?></div>
            </div>
        </div>
        <div style="padding:1.25rem 1.5rem;">
            <table style="width:100%;border-collapse:collapse;">
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem 0;color:var(--text-secondary);font-size:0.875rem;font-weight:500;width:35%;">Username</td>
                    <td style="padding:0.75rem 0;color:var(--text-primary);font-weight:600;"><?= e($username) ?></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem 0;color:var(--text-secondary);font-size:0.875rem;font-weight:500;">Email</td>
                    <td style="padding:0.75rem 0;color:var(--text-primary);font-weight:600;">
                        <?= $email ? e($email) : '<span style="color:var(--text-secondary);font-style:italic;">Not set</span>' ?>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem 0;color:var(--text-secondary);font-size:0.875rem;font-weight:500;">Role</td>
                    <td style="padding:0.75rem 0;">
                        <span class="badge badge-info"><?= e($roleName) ?></span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:0.75rem 0;color:var(--text-secondary);font-size:0.875rem;font-weight:500;">User ID</td>
                    <td style="padding:0.75rem 0;color:var(--text-secondary);font-family:monospace;">#<?= e($userId) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ── Change Password Card ── -->
    <div class="card" style="grid-column:1/-1;">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--border-color);">
            <h2 style="font-size:1rem;font-weight:700;color:var(--text-primary);margin:0;">🔒 Change Password</h2>
            <p style="font-size:0.8125rem;color:var(--text-secondary);margin:0.25rem 0 0;">Update your account login password.</p>
        </div>
        <form method="POST" action="/profile" style="padding:1.25rem 1.5rem;">
            <?= csrf_field() ?>
            <input type="hidden" name="_action" value="change_password_admin">

            <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" style="display:block;font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.375rem;">Current Password</label>
                <input type="password" name="current_password" class="form-control" required placeholder="••••••••"
                       style="width:100%;max-width:400px;padding:0.6rem 0.875rem;border:1px solid var(--border-color);border-radius:6px;background:var(--bg-main);color:var(--text-primary);font-size:0.9375rem;outline:none;">
            </div>
            <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" style="display:block;font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.375rem;">New Password <span style="font-weight:400;color:var(--text-secondary);">(min. 8 characters)</span></label>
                <input type="password" name="new_password" class="form-control" required minlength="8" placeholder="••••••••"
                       style="width:100%;max-width:400px;padding:0.6rem 0.875rem;border:1px solid var(--border-color);border-radius:6px;background:var(--bg-main);color:var(--text-primary);font-size:0.9375rem;outline:none;">
            </div>
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label class="form-label" style="display:block;font-size:0.875rem;font-weight:600;color:var(--text-primary);margin-bottom:0.375rem;">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required minlength="8" placeholder="••••••••"
                       style="width:100%;max-width:400px;padding:0.6rem 0.875rem;border:1px solid var(--border-color);border-radius:6px;background:var(--bg-main);color:var(--text-primary);font-size:0.9375rem;outline:none;">
            </div>
            <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.5rem;background:var(--accent-color);color:#fff;border:none;border-radius:6px;font-size:0.9rem;font-weight:600;cursor:pointer;">
                Update Password
            </button>
        </form>
    </div>

</div>

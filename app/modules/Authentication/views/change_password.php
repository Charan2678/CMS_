<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Change Password') ?></title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-brand">Change Password</h1>
                <?php if ($mustChangePassword): ?>
                    <p class="auth-subtitle" style="color: #fca5a5;">First login detected — You must change your default password to continue.</p>
                <?php else: ?>
                    <p class="auth-subtitle">Update your account password</p>
                <?php endif; ?>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/change-password">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password (min. 8 characters)</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8" placeholder="••••••••">
                </div>

                <button type="submit" class="btn-primary">Update Password</button>
            </form>

            <?php if (!$mustChangePassword): ?>
                <div class="auth-footer">
                    <a href="/dashboard">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

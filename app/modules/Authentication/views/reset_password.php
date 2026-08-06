<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Set New Password') ?></title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-brand">Set New Password</h1>
                <p class="auth-subtitle">Enter your new account password below.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($token)): ?>
                <form method="POST" action="/reset-password">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= e($token) ?>">

                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password (min. 8 characters)</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" placeholder="••••••••">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-primary">Reset Password</button>
                </form>
            <?php endif; ?>

            <div class="auth-footer">
                <a href="/login">Back to Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>

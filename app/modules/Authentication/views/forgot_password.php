<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Forgot Password') ?></title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h1 class="auth-brand">Forgot Password</h1>
                <p class="auth-subtitle">Enter your registered email address to receive a password reset link.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= e($success) ?>
                    <?php if (!empty($token)): ?>
                        <div style="margin-top: 0.75rem;">
                            <strong>Dev Mode Reset Link:</strong>
                            <a href="/reset-password?token=<?= urlencode($token) ?>" class="code-badge">
                                /reset-password?token=<?= e($token) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/forgot-password">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="email">Account Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="user@college.edu">
                </div>

                <button type="submit" class="btn-primary">Generate Reset Link</button>
            </form>

            <div class="auth-footer">
                <a href="/login">Back to Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>

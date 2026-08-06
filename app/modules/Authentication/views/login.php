<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuppam Engineering College — Portal Login</title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header" style="text-align: center;">
                <img src="/assets/images/logo.png" alt="Kuppam Engineering College Logo" style="width: 80px; height: 80px; object-fit: contain; margin-bottom: 0.75rem;">
                <h1 class="auth-brand" style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">Kuppam Engineering College</h1>
                <p class="auth-subtitle" style="font-size: 0.8125rem; color: var(--text-secondary); margin-top: 0.25rem;">Sign in to access your ERP portal</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?= e($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label" for="login_id">Username or Email</label>
                    <input type="text" id="login_id" name="login_id" class="form-control" required autofocus placeholder="e.g. admin or admin@college.edu">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn-primary">Sign In</button>
            </form>

            <div class="auth-footer">
                <a href="/forgot-password">Forgot password?</a>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password — Kuppam Engineering College</title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="login-split-container">
        
        <!-- Left Side: Campus Image Panel -->
        <div class="login-slider-section">
            <div class="slide-track">
                <div class="slide-item active" style="background-image: url('/assets/images/slides/slide1.png?v=hd');">
                    <div class="slide-overlay"></div>
                </div>
            </div>
        </div>

        <!-- Right Side: Reset Password Form -->
        <div class="login-form-section">
            <div class="brand-header">
                <img src="/assets/images/logo.png" alt="Kuppam Engineering College Logo" class="brand-logo-img" onerror="this.src='/assets/images/favicon.png';">
                <h1 class="brand-title">Set New Password</h1>
                <p class="brand-subtitle">Enter your new account password below.</p>
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
                        <label class="field-label" for="new_password">
                            <span class="req-star">*</span>New Password <span style="font-weight:400; color:#64748b;">(min. 8 characters)</span>
                        </label>
                        <input type="password" id="new_password" name="new_password" class="form-input" required minlength="8" placeholder="••••••••" autofocus>
                    </div>

                    <div class="form-group">
                        <label class="field-label" for="confirm_password">
                            <span class="req-star">*</span>Confirm New Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" required minlength="8" placeholder="••••••••">
                    </div>

                    <div class="action-buttons-row" style="margin-top: 1.5rem;">
                        <button type="submit" class="btn-login" style="width: 100%;">Reset Password</button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="aux-links" style="margin-top: 1.5rem;">
                <a href="/login" class="passout-link" style="font-weight: 600;">← Back to Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>

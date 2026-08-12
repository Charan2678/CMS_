<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Change Password') ?> — GNUMS Portal</title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="login-split-container">

        <!-- Left Side: Background Panel -->
        <div class="login-slider-section">
            <div class="slide-track">
                <div class="slide-item active" style="background-image: url('/assets/images/slides/slide1.png?v=hd');">
                    <div class="slide-overlay"></div>
                </div>
            </div>
        </div>

        <!-- Right Side: Change Password Form -->
        <div class="login-form-section">
            <div class="brand-header">
                <img src="/assets/images/logo.png" alt="Kuppam Engineering College Logo" class="brand-logo-img" onerror="this.src='/assets/images/favicon.png';">
                <h1 class="brand-title">Change Password</h1>
                <?php if ($mustChangePassword): ?>
                    <p class="brand-subtitle" style="color: #dc2626;">First login detected — you must set a new password to continue.</p>
                <?php else: ?>
                    <p class="brand-subtitle">Update your account password</p>
                <?php endif; ?>
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

            <form method="POST" action="/change-password">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="field-label" for="current_password">
                        <span class="req-star">*</span>Current Password
                    </label>
                    <input type="password" id="current_password" name="current_password"
                           class="form-input" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="field-label" for="new_password">
                        <span class="req-star">*</span>New Password <span style="font-weight:400; color:#64748b;">(min. 8 characters)</span>
                    </label>
                    <input type="password" id="new_password" name="new_password"
                           class="form-input" required minlength="8" placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label class="field-label" for="confirm_password">
                        <span class="req-star">*</span>Confirm New Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           class="form-input" required minlength="8" placeholder="••••••••">
                </div>

                <div class="action-buttons-row">
                    <button type="submit" class="btn-login">Update Password</button>
                    <?php if (!$mustChangePassword): ?>
                        <a href="/dashboard" class="btn-forgot">Back to Dashboard</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="browser-note">Please use Google Chrome for better experience</div>
        </div>

    </div>
</body>
</html>

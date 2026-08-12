<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Kuppam Engineering College</title>
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

        <!-- Right Side: Reset Request Form -->
        <div class="login-form-section">
            <div class="brand-header">
                <img src="/assets/images/logo.png" alt="Kuppam Engineering College Logo" class="brand-logo-img" onerror="this.src='/assets/images/favicon.png';">
                <h1 class="brand-title">Forgot Password</h1>
                <p class="brand-subtitle">Enter your registered email address to receive a password reset link.</p>
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
                            <a href="/reset-password?token=<?= urlencode($token) ?>" class="code-badge" style="display: block; word-break: break-all; font-family: monospace; font-size: 0.8125rem; background: rgba(2, 132, 199, 0.1); color: #0284c7; padding: 0.4rem 0.6rem; border-radius: 4px; margin-top: 0.25rem; text-decoration: none;">
                                /reset-password?token=<?= e($token) ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/forgot-password">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="field-label" for="email">
                        <span class="req-star">*</span>Account Email Address
                    </label>
                    <input type="email" id="email" name="email" class="form-input" required placeholder="user@college.edu" autofocus>
                </div>

                <div class="action-buttons-row" style="margin-top: 1.5rem;">
                    <button type="submit" class="btn-login" style="width: 100%;">Generate Reset Link</button>
                </div>
            </form>

            <div class="aux-links" style="margin-top: 1.5rem;">
                <a href="/login" class="passout-link" style="font-weight: 600;">← Back to Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuppam Engineering College — GNUMS Portal Login</title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <div class="login-split-container">
        
        <!-- Left Side: 5-Second Carousel Slider -->
        <div class="login-slider-section">
            <div class="slide-track" id="slideTrack">
                <div class="slide-item active" style="background-image: url('/assets/images/slides/slide1.png?v=hd');">
                    <div class="slide-overlay"></div>
                </div>
                <div class="slide-item" style="background-image: url('/assets/images/slides/slide2.png?v=hd');">
                    <div class="slide-overlay"></div>
                </div>
                <div class="slide-item" style="background-image: url('/assets/images/slides/slide3.png?v=hd');">
                    <div class="slide-overlay"></div>
                </div>
            </div>

            <!-- Slide Dots / Indicators -->
            <div class="slider-indicators" id="sliderIndicators">
                <div class="dot active" onclick="goToSlide(0)"></div>
                <div class="dot" onclick="goToSlide(1)"></div>
                <div class="dot" onclick="goToSlide(2)"></div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="login-form-section">
            <div class="brand-header">
                <img src="/assets/images/logo.png" alt="Kuppam Engineering College Logo" class="brand-logo-img" onerror="this.src='/assets/images/favicon.png';">
                <h1 class="brand-title">Kuppam Engineering College</h1>
                <p class="brand-subtitle">GNUMS ERP Portal</p>
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

                <!-- Role Selection -->
                <div class="form-group-role">
                    <label class="role-label"><span class="req-star">*</span>Role</label>
                    <div class="role-options">
                        <label class="role-option">
                            <input type="radio" name="role_type" value="staff" <?= ($roleType ?? 'student') === 'staff' ? 'checked' : '' ?>> Staff
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role_type" value="student" <?= ($roleType ?? 'student') === 'student' ? 'checked' : '' ?>> Student
                        </label>
                        <label class="role-option">
                            <input type="radio" name="role_type" value="admin" <?= ($roleType ?? 'student') === 'admin' ? 'checked' : '' ?>> Admin
                        </label>
                    </div>
                </div>

                <!-- Username Field -->
                <div class="form-group">
                    <label class="field-label" for="login_id">
                        <span class="req-star">*</span>Username
                    </label>
                    <input type="text" id="login_id" name="login_id" class="form-input" required autofocus placeholder="Enter Roll Number / Employee ID" value="<?= e($loginIdVal ?? '') ?>">
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label class="field-label" for="password">
                        <span class="req-star">*</span>Password <span class="info-icon" title="Enter your portal password">i</span>
                    </label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••">
                    
                    <label class="show-password-wrapper">
                        <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()"> Show Password
                    </label>
                </div>

                <!-- Action Buttons Row -->
                <div class="action-buttons-row">
                    <button type="submit" class="btn-login">Login</button>
                    <a href="/forgot-password" class="btn-forgot">Forgot Password</a>
                </div>
            </form>

            <!-- Links & Google Sign In -->
            <div class="aux-links">
                <a href="#" class="passout-link">Passout Student Click Here</a>
            </div>

            <div class="google-btn-container">
                <button type="button" class="google-btn" onclick="alert('Google Sign-In integration ready.');">
                    <svg class="google-icon" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.29v3.15C3.26 21.3 7.31 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.29C.47 8.21 0 10.05 0 12s.47 3.79 1.29 5.42l3.99-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.58l3.99 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    Sign in with Google
                </button>
            </div>

            <div class="browser-note">
                Please use Google Chrome for better experience
            </div>

        </div>

    </div>

    <!-- 5-Second Carousel & Show Password Script -->
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide-item');
        const dots = document.querySelectorAll('.dot');
        const totalSlides = slides.length;
        let slideInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            currentSlide = index;
        }

        function nextSlide() {
            let nextIndex = (currentSlide + 1) % totalSlides;
            showSlide(nextIndex);
        }

        function goToSlide(index) {
            showSlide(index);
            resetInterval();
        }

        function startInterval() {
            slideInterval = setInterval(nextSlide, 5000); // 5 Seconds Interval
        }

        function resetInterval() {
            clearInterval(slideInterval);
            startInterval();
        }

        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
            } else {
                pwdInput.type = 'password';
            }
        }

        // Initialize Carousel
        document.addEventListener('DOMContentLoaded', function() {
            showSlide(0);
            startInterval();
        });
    </script>
</body>
</html>

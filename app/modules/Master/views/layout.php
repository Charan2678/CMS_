<?php
$svg = [
    'home'         => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
    'attendance'   => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><path d="M9 16l2 2 4-4"></path></svg>',
    'results'      => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
    'timetable'    => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line><circle cx="16" cy="16" r="3"></circle><polyline points="16 15 16 16 17 16"></polyline></svg>',
    'receipt'      => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>',
    'canteen'      => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M18 8v13M18 3v2M10 3v18M14 3v18M6 3v5a4 4 0 0 0 4 4h0a4 4 0 0 0 4-4V3"></path></svg>',
    'library'      => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
    'hostel'       => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M2 4v16M2 8h20v12M2 17h20M6 8v3a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V8"></path></svg>',
    'transport'    => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="13" rx="2"></rect><circle cx="7.5" cy="15.5" r="1.5"></circle><circle cx="16.5" cy="15.5" r="1.5"></circle><path d="M3 11h18M5 19v2M19 19v2"></path></svg>',
    'announcements'=> '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M22 10.5V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-4.5l-4-3 4-3z"></path><path d="M18 8l-6 4 6 4"></path></svg>',
    'people'       => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
    'college'      => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M3 21h18M3 7v14M21 7v14M6 21V11M10 21V11M14 21V11M18 21V11M12 3L2 7h20l-10-4z"></path></svg>',
    'reports'      => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
    'logs'         => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>',
    'money'        => '<svg class="nav-svg-icon" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>',
];
?>
<?php
$theme = null;
try {
    $themeStmt = db()->query('SELECT * FROM theme_settings WHERE college_id = 1 LIMIT 1');
    $theme = $themeStmt->fetch(\PDO::FETCH_ASSOC);
} catch (\Exception $e) {
    // Fail silently
}
$collegeName = $theme['college_name'] ?? 'Kuppam Engineering College';
$logoPath = $theme['logo_path'] ?? '/assets/images/logo.png';
$primaryColor = $theme['color_primary'] ?? '#0284c7';
$secondaryColor = $theme['color_secondary'] ?? '#0369a1';
$fontFamily = $theme['font_family'] ?? 'Inter';
$borderRadius = $theme['border_radius'] ?? '8px';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Management ERP') ?> — <?= e($collegeName) ?></title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($fontFamily) ?>:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= time() ?>">
    <style>
        :root {
            --sidebar-bg: linear-gradient(180deg, <?= $primaryColor ?> 0%, <?= $secondaryColor ?> 100%);
            --accent-color: <?= $primaryColor ?>;
            --accent-hover: <?= $secondaryColor ?>;
            --input-border-radius: <?= $borderRadius ?>;
            --border-radius: <?= $borderRadius ?>;
        }
        body {
            font-family: '<?= $fontFamily ?>', 'Inter', system-ui, -apple-system, sans-serif !important;
        }
    </style>
</head>
<body>

<div class="app-layout">
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-logo" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="<?= e($logoPath) ?>" alt="Logo" style="width: 36px; height: 36px; object-fit: contain;">
                <span style="font-size: 0.875rem; font-weight: 700; color: #f8fafc; line-height: 1.2; word-break: break-word;"><?= e($collegeName) ?></span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php $r = auth_role(); ?>

            <div class="nav-section-title">MAIN</div>
            <a href="/dashboard" class="nav-item <?= request_uri() === '/dashboard' ? 'active' : '' ?>">
                <?= $svg['home'] ?> Dashboard
            </a>

            <?php if ($r === 'student'): ?>
                <!-- Student Portal Sidebar -->
                <div class="nav-section-title">MY ACADEMICS</div>
                <a href="/attendance" class="nav-item <?= request_uri() === '/attendance' ? 'active' : '' ?>"><?= $svg['attendance'] ?> My Attendance</a>
                <a href="/results" class="nav-item <?= request_uri() === '/results' ? 'active' : '' ?>"><?= $svg['results'] ?> Semester Results</a>
                <a href="/admit-card" class="nav-item <?= request_uri() === '/admit-card' ? 'active' : '' ?>">🎫 Exam Hall Ticket</a>
                <a href="/timetable" class="nav-item <?= request_uri() === '/timetable' ? 'active' : '' ?>"><?= $svg['timetable'] ?> Class Timetable</a>
                <a href="/exam-timetable" class="nav-item <?= request_uri() === '/exam-timetable' ? 'active' : '' ?>">🗓️ Exam Timetable</a>
                <a href="/placement/portal" class="nav-item <?= request_uri() === '/placement/portal' ? 'active' : '' ?>">💼 Placement Portal</a>

                <div class="nav-section-title">MY FINANCE & SERVICES</div>
                <a href="/fee/payments" class="nav-item <?= request_uri() === '/fee/payments' ? 'active' : '' ?>"><?= $svg['receipt'] ?> My Fee Receipts</a>
                <a href="/canteen" class="nav-item <?= request_uri() === '/canteen' ? 'active' : '' ?>"><?= $svg['canteen'] ?> Canteen Menu</a>
                <a href="/library" class="nav-item <?= request_uri() === '/library' ? 'active' : '' ?>"><?= $svg['library'] ?> Library Catalog</a>
                <a href="/hostel" class="nav-item <?= request_uri() === '/hostel' ? 'active' : '' ?>"><?= $svg['hostel'] ?> Hostel Details</a>
                <a href="/transport" class="nav-item <?= request_uri() === '/transport' ? 'active' : '' ?>"><?= $svg['transport'] ?> Transport Routes</a>

                <div class="nav-section-title">CAMPUS COMMUNICATION</div>
                <a href="/announcements" class="nav-item <?= request_uri() === '/announcements' ? 'active' : '' ?>"><?= $svg['announcements'] ?> College Announcements</a>

            <?php elseif (in_array($r, ['faculty', 'hod'])): ?>
                <!-- Faculty & HOD Sidebar -->
                <div class="nav-section-title">PEOPLE DIRECTORY</div>
                <a href="/students" class="nav-item"><?= $svg['people'] ?> Students Directory</a>
                <?php if ($r === 'hod'): ?>
                    <a href="/faculty" class="nav-item"><?= $svg['people'] ?> Faculty Directory</a>
                <?php endif; ?>

                <div class="nav-section-title">ACADEMIC MANAGEMENT</div>
                <a href="/attendance" class="nav-item"><?= $svg['attendance'] ?> Mark Attendance</a>
                <a href="/timetable" class="nav-item"><?= $svg['timetable'] ?> Class Timetable</a>
                <a href="/marks/internal" class="nav-item"><?= $svg['results'] ?> Internal CIA Marks</a>
                <a href="/results" class="nav-item"><?= $svg['results'] ?> Semester Results</a>

                <div class="nav-section-title">COMMUNICATION</div>
                <a href="/announcements" class="nav-item"><?= $svg['announcements'] ?> Announcements</a>

            <?php elseif ($r === 'accounts_staff'): ?>
                <!-- Accounts Staff Sidebar -->
                <div class="nav-section-title">FINANCE & ACCOUNTS</div>
                <a href="/accounts" class="nav-item"><?= $svg['money'] ?> Accounts & Payroll</a>
                <a href="/fee/categories" class="nav-item"><?= $svg['receipt'] ?> Fee Categories</a>
                <a href="/fee/structures" class="nav-item"><?= $svg['money'] ?> Fee Structures</a>
                <a href="/fee/payments" class="nav-item"><?= $svg['receipt'] ?> Fee Payments & Receipts</a>

                <div class="nav-section-title">REPORTS & ALERTS</div>
                <a href="/reports/financial" class="nav-item"><?= $svg['reports'] ?> Financial Reports</a>
                <a href="/announcements" class="nav-item"><?= $svg['announcements'] ?> Announcements</a>

            <?php elseif ($r === 'librarian'): ?>
                <!-- Librarian Sidebar -->
                <div class="nav-section-title">LIBRARY OPERATIONS</div>
                <a href="/library" class="nav-item"><?= $svg['library'] ?> Library Catalog</a>
                <a href="/announcements" class="nav-item"><?= $svg['announcements'] ?> Announcements</a>

            <?php elseif ($r === 'hostel_warden'): ?>
                <!-- Hostel Warden Sidebar -->
                <div class="nav-section-title">HOSTEL OPERATIONS</div>
                <a href="/hostel" class="nav-item"><?= $svg['hostel'] ?> Hostel Management</a>
                <a href="/announcements" class="nav-item"><?= $svg['announcements'] ?> Announcements</a>

            <?php elseif ($r === 'transport_manager'): ?>
                <!-- Transport Manager Sidebar -->
                <div class="nav-section-title">TRANSPORT FLEET</div>
                <a href="/transport" class="nav-item"><?= $svg['transport'] ?> Transport & Bus Routes</a>
                <a href="/announcements" class="nav-item"><?= $svg['announcements'] ?> Announcements</a>

            <?php elseif ($r === 'canteen_manager'): ?>
                <!-- Canteen Manager Sidebar -->
                <div class="nav-section-title">CANTEEN OPERATIONS</div>
                <a href="/canteen" class="nav-item"><?= $svg['canteen'] ?> Canteen & Mess Menu</a>
                <a href="/announcements" class="nav-item"><?= $svg['announcements'] ?> Announcements</a>

            <?php elseif ($r === 'head_of_coe'): ?>
                <!-- Head of COE / Exam Cell Sidebar -->
                <div class="nav-section-title">EXAMINATIONS & SCHEDULING</div>
                <a href="/exam-timetable" class="nav-item <?= request_uri() === '/exam-timetable' ? 'active' : '' ?>">🗓️ Exam Timetable</a>
                <a href="/admit-cards/manage" class="nav-item <?= request_uri() === '/admit-cards/manage' ? 'active' : '' ?>">🎫 Hall Tickets & Eligibility</a>
                <a href="/marks/internal" class="nav-item <?= request_uri() === '/marks/internal' ? 'active' : '' ?>"><?= $svg['results'] ?> Internal CIA Marks</a>
                <a href="/results" class="nav-item <?= request_uri() === '/results' ? 'active' : '' ?>"><?= $svg['results'] ?> Semester Results Engine</a>

                <div class="nav-section-title">PEOPLE DIRECTORY</div>
                <a href="/students" class="nav-item <?= request_uri() === '/students' ? 'active' : '' ?>"><?= $svg['people'] ?> Students Directory</a>

                <div class="nav-section-title">REPORTS & MESSAGES</div>
                <a href="/reports/academic" class="nav-item <?= request_uri() === '/reports/academic' ? 'active' : '' ?>"><?= $svg['reports'] ?> Academic Reports</a>
                <a href="/announcements" class="nav-item <?= request_uri() === '/announcements' ? 'active' : '' ?>"><?= $svg['announcements'] ?> Announcements</a>

            <?php elseif ($r === 'tpo'): ?>
                <!-- Training & Placement Officer Sidebar -->
                <div class="nav-section-title">PLACEMENTS & DRIVES</div>
                <a href="/dashboard" class="nav-item <?= request_uri() === '/dashboard' ? 'active' : '' ?>">💼 Placement Dashboard</a>
                <a href="/placement/drives" class="nav-item <?= request_uri() === '/placement/drives' ? 'active' : '' ?>">📝 Company Drives</a>
                <a href="/placement/companies" class="nav-item <?= request_uri() === '/placement/companies' ? 'active' : '' ?>">🏢 Recruiter Partners</a>
                <a href="/placement/applications" class="nav-item <?= request_uri() === '/placement/applications' ? 'active' : '' ?>">👨‍🎓 Student Applications</a>

                <div class="nav-section-title">TRAINING & COURSES</div>
                <a href="/placement/trainings" class="nav-item <?= request_uri() === '/placement/trainings' ? 'active' : '' ?>">📚 Pre-Placement Training</a>

                <div class="nav-section-title">REPORTS & MESSAGES</div>
                <a href="/placement/reports" class="nav-item <?= request_uri() === '/placement/reports' ? 'active' : '' ?>">📈 Placement Reports</a>
                <a href="/announcements" class="nav-item <?= request_uri() === '/announcements' ? 'active' : '' ?>"><?= $svg['announcements'] ?> Announcements</a>

            <?php else: ?>
                <!-- Super Admin / Admin Full Navigation -->
                <div class="nav-section-title">MASTER DATA</div>
                <a href="/master/colleges" class="nav-item"><?= $svg['college'] ?> College Info</a>
                <a href="/master/academic-years" class="nav-item"><?= $svg['timetable'] ?> Academic Years</a>
                <a href="/master/departments" class="nav-item"><?= $svg['college'] ?> Departments</a>
                <a href="/master/courses" class="nav-item"><?= $svg['library'] ?> Courses & Semesters</a>
                <a href="/master/sections" class="nav-item"><?= $svg['receipt'] ?> Sections</a>
                <a href="/master/subjects" class="nav-item"><?= $svg['library'] ?> Subjects</a>

                <div class="nav-section-title">PEOPLE</div>
                <a href="/students" class="nav-item"><?= $svg['people'] ?> Students Directory</a>
                <a href="/faculty" class="nav-item"><?= $svg['people'] ?> Faculty Directory</a>
                <a href="/staff" class="nav-item"><?= $svg['people'] ?> Non-Faculty Staff</a>

                <div class="nav-section-title">ACADEMICS</div>
                <a href="/attendance" class="nav-item"><?= $svg['attendance'] ?> Attendance Roster</a>


                <a href="/timetable" class="nav-item"><?= $svg['timetable'] ?> Class Timetable</a>
                <a href="/exam-timetable" class="nav-item <?= request_uri() === '/exam-timetable' ? 'active' : '' ?>">🗓️ Exam Timetable</a>
                <a href="/marks/internal" class="nav-item"><?= $svg['results'] ?> Internal CIA Marks</a>
                <a href="/results" class="nav-item"><?= $svg['results'] ?> Semester Results</a>
                <a href="/admit-cards/manage" class="nav-item">🎫 Exam Hall Tickets</a>

                <div class="nav-section-title">FINANCE & FEES</div>
                <a href="/fee/categories" class="nav-item"><?= $svg['receipt'] ?> Fee Categories</a>
                <a href="/fee/structures" class="nav-item"><?= $svg['money'] ?> Fee Structures</a>
                <a href="/fee/payments" class="nav-item"><?= $svg['receipt'] ?> Fee Payments & Receipts</a>

                <div class="nav-section-title">FACILITIES & OPERATIONS</div>
                <a href="/accounts" class="nav-item"><?= $svg['money'] ?> Accounts & Payroll</a>
                <a href="/library" class="nav-item"><?= $svg['library'] ?> Library Catalog</a>
                <a href="/hostel" class="nav-item"><?= $svg['hostel'] ?> Hostel Management</a>
                <a href="/transport" class="nav-item"><?= $svg['transport'] ?> Transport & Routes</a>
                <a href="/canteen" class="nav-item"><?= $svg['canteen'] ?> Canteen & Mess Menu</a>

                <div class="nav-section-title">COMMUNICATION & REPORTS</div>
                <a href="/reports/academic" class="nav-item"><?= $svg['reports'] ?> Academic Reports</a>
                <a href="/reports/financial" class="nav-item"><?= $svg['reports'] ?> Financial Reports</a>
                <a href="/reports/attendance" class="nav-item"><?= $svg['reports'] ?> Attendance Audit</a>
                <a href="/announcements" class="nav-item"><?= $svg['announcements'] ?> Announcements</a>
                <a href="/audit-logs" class="nav-item"><?= $svg['logs'] ?> System Audit Logs</a>

                <div class="nav-section-title">SYSTEM CONFIG</div>
                <a href="/settings/theme" class="nav-item <?= request_uri() === '/settings/theme' ? 'active' : '' ?>">🎨 Theme & Branding</a>
                <a href="/settings/roles" class="nav-item <?= request_uri() === '/settings/roles' ? 'active' : '' ?>">🔑 Role Permissions</a>
            <?php endif; ?>
        </nav>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="top-header">
            <!-- Hamburger (mobile only) -->
            <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>

            <!-- Page Title -->
            <div class="header-title"><?= e($title ?? 'Dashboard') ?></div>

            <!-- Right: Theme + Profile -->
            <div class="user-profile">
                <!-- Theme Toggle -->
                <button id="themeToggleBtn" onclick="toggleTheme()" title="Toggle theme" style="background: rgba(2,132,199,0.1); border: 1px solid var(--border-color); color: var(--text-primary); padding: 0.35rem 0.6rem; border-radius: 7px; font-size: 0.8125rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.3rem; transition: all 0.2s ease; white-space: nowrap;">
                    <span id="themeIcon">💻</span><span id="themeText">System</span>
                </button>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown-wrapper">
                    <button id="profileDropdownBtn" onclick="toggleProfileDropdown()" style="display: flex; align-items: center; gap: 0.5rem; background: var(--bg-main); border: 1px solid var(--border-color); padding: 0.3rem 0.625rem; border-radius: 20px; cursor: pointer; transition: all 0.2s ease;">
                        <!-- Avatar -->
                        <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--accent-color); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8125rem; font-weight: 700; border: 2px solid rgba(255,255,255,0.5); overflow: hidden; flex-shrink: 0;">
                            <?php if (!empty($_SESSION['photo_path'])): ?>
                                <img src="<?= e($_SESSION['photo_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                👤
                            <?php endif; ?>
                        </div>
                        <!-- Name/role hidden on small screens via CSS -->
                        <div class="profile-name-text" style="text-align:left;line-height:1.2;">
                            <div style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);"><?= e($_SESSION['username'] ?? 'User') ?></div>
                            <div class="profile-role-text" style="font-size:0.6875rem;color:var(--text-secondary);"><?= e($_SESSION['role_name'] ?? 'Role') ?></div>
                        </div>
                        <span class="profile-arrow" style="font-size:0.625rem;color:var(--text-secondary);">▼</span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profileDropdownMenu" style="display:none; position: absolute; right: 0; top: 115%; width: 230px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.18); z-index: 9999; padding: 0.5rem 0; text-align: left;">
                        <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--border-color);border-radius:12px 12px 0 0;background:var(--bg-main);">
                            <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);"><?= e($_SESSION['username'] ?? 'User') ?></div>
                            <div style="font-size:0.75rem;color:var(--accent-color);font-weight:600;"><?= e($_SESSION['role_name'] ?? 'Role') ?></div>
                        </div>
                        <a href="/profile" style="display:flex;align-items:center;gap:0.625rem;padding:0.75rem 1rem;color:var(--text-primary);font-size:0.8125rem;font-weight:600;text-decoration:none;">
                            <span>👤</span> My Profile &amp; Documents
                        </a>
                        <a href="/change-password" style="display:flex;align-items:center;gap:0.625rem;padding:0.75rem 1rem;color:var(--text-primary);font-size:0.8125rem;font-weight:600;text-decoration:none;">
                            <span>🔒</span> Security &amp; Password
                        </a>
                        <div style="border-top:1px solid var(--border-color);margin:0.25rem 0;"></div>
                        <a href="/logout" style="display:flex;align-items:center;gap:0.625rem;padding:0.75rem 1rem;color:var(--danger);font-size:0.8125rem;font-weight:700;text-decoration:none;">
                            <span>🚪</span> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            <?= $content ?>
        </main>
    </div>

    <script>
        /* ─── Mobile Sidebar ─── */
        var _sidebar  = document.querySelector('.sidebar');
        var _overlay  = document.getElementById('sidebarOverlay');
        var _hambBtn  = document.getElementById('hamburgerBtn');

        function toggleSidebar() {
            _sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        }

        function openSidebar() {
            _sidebar.classList.add('open');
            _overlay.classList.add('visible');
            _hambBtn.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            _sidebar.classList.remove('open');
            _overlay.classList.remove('visible');
            _hambBtn.classList.remove('open');
            document.body.style.overflow = '';
        }

        _hambBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        _overlay.addEventListener('click', closeSidebar);

        document.querySelectorAll('.nav-item').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) closeSidebar();
            });
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) closeSidebar();
        });

        /* ─── Profile Dropdown ─── */
        function toggleProfileDropdown() {
            const menu = document.getElementById('profileDropdownMenu');
            if (menu) {
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            }
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.querySelector('.profile-dropdown-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const menu = document.getElementById('profileDropdownMenu');
                if (menu) menu.style.display = 'none';
            }
        });

        function applyTheme(theme) {
            const btnIcon = document.getElementById('themeIcon');
            const btnText = document.getElementById('themeText');
            
            if (theme === 'system') {
                document.documentElement.removeAttribute('data-theme');
                const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (btnIcon && btnText) {
                    btnIcon.innerText = '💻';
                    btnText.innerText = 'System (' + (isDark ? 'Dark' : 'Light') + ')';
                }
            } else if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                if (btnIcon && btnText) {
                    btnIcon.innerText = '🌙';
                    btnText.innerText = 'Dark';
                }
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
                if (btnIcon && btnText) {
                    btnIcon.innerText = '☀️';
                    btnText.innerText = 'Light';
                }
            }
        }

        function toggleTheme() {
            const current = localStorage.getItem('theme') || 'system';
            let next = 'light';
            if (current === 'system') next = 'light';
            else if (current === 'light') next = 'dark';
            else if (current === 'dark') next = 'system';
            
            localStorage.setItem('theme', next);
            applyTheme(next);
        }

        (function initTheme() {
            const saved = localStorage.getItem('theme') || 'system';
            applyTheme(saved);
        })();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if ((localStorage.getItem('theme') || 'system') === 'system') {
                applyTheme('system');
            }
        });
    </script>
</body>
</html>

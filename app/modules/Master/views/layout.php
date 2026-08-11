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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Management ERP') ?> — Kuppam Engineering College</title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= time() ?>">
</head>
<body>

<div class="app-layout">
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-logo" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="/assets/images/logo.png" alt="KEC Logo" style="width: 36px; height: 36px; object-fit: contain;">
                <span style="font-size: 0.875rem; font-weight: 700; color: #f8fafc; line-height: 1.2; word-break: break-word;">Kuppam Engineering College</span>
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
                <a href="/leave/apply" class="nav-item <?= request_uri() === '/leave/apply' ? 'active' : '' ?>">📝 Apply Leave &amp; Outpass</a>

                <div class="nav-section-title">MY FINANCE &amp; SERVICES</div>
                <a href="/fee/payments" class="nav-item <?= request_uri() === '/fee/payments' ? 'active' : '' ?>"><?= $svg['receipt'] ?> My Fee Receipts</a>
                <a href="/canteen" class="nav-item <?= request_uri() === '/canteen' ? 'active' : '' ?>"><?= $svg['canteen'] ?> Canteen Menu</a>
                <a href="/library" class="nav-item <?= request_uri() === '/library' ? 'active' : '' ?>"><?= $svg['library'] ?> Library Catalog</a>
                <a href="/hostel" class="nav-item <?= request_uri() === '/hostel' ? 'active' : '' ?>"><?= $svg['hostel'] ?> Hostel Details</a>
                <a href="/transport" class="nav-item <?= request_uri() === '/transport' ? 'active' : '' ?>"><?= $svg['transport'] ?> Transport Routes</a>

                <div class="nav-section-title">CAMPUS COMMUNICATION</div>
                <a href="/announcements" class="nav-item <?= request_uri() === '/announcements' ? 'active' : '' ?>"><?= $svg['announcements'] ?> College Announcements</a>

            <?php elseif ($r === 'parent'): ?>
                <!-- Parent Portal Sidebar -->
                <div class="nav-section-title">MY CHILD / WARD</div>
                <a href="/profile" class="nav-item <?= request_uri() === '/profile' ? 'active' : '' ?>">👨‍🎓 Child Profile &amp; Bio</a>
                <a href="/attendance" class="nav-item <?= request_uri() === '/attendance' ? 'active' : '' ?>"><?= $svg['attendance'] ?> Ward Attendance</a>
                <a href="/results" class="nav-item <?= request_uri() === '/results' ? 'active' : '' ?>"><?= $svg['results'] ?> Semester Results</a>
                <a href="/admit-card" class="nav-item <?= request_uri() === '/admit-card' ? 'active' : '' ?>">🎫 Exam Hall Ticket</a>
                <a href="/timetable" class="nav-item <?= request_uri() === '/timetable' ? 'active' : '' ?>"><?= $svg['timetable'] ?> Class Timetable</a>
                <a href="/leave/apply" class="nav-item <?= request_uri() === '/leave/apply' ? 'active' : '' ?>">📝 Apply Ward Leave</a>

                <div class="nav-section-title">FEE &amp; PAYMENTS</div>
                <a href="/fee/payments" class="nav-item <?= request_uri() === '/fee/payments' ? 'active' : '' ?>"><?= $svg['receipt'] ?> Fee Receipts &amp; Dues</a>

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
                <a href="/leave/review" class="nav-item <?= request_uri() === '/leave/review' ? 'active' : '' ?>">📝 Review Leaves &amp; Outpasses</a>

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
                <a href="/leave/outpasses" class="nav-item <?= request_uri() === '/leave/outpasses' ? 'active' : '' ?>">🚪 Outpasses &amp; Check-in</a>
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
                <a href="/marks/internal" class="nav-item"><?= $svg['results'] ?> Internal CIA Marks</a>
                <a href="/results" class="nav-item"><?= $svg['results'] ?> Semester Results</a>
                <a href="/admit-cards/manage" class="nav-item">🎫 Exam Hall Tickets</a>
                <a href="/leave/review" class="nav-item <?= request_uri() === '/leave/review' ? 'active' : '' ?>">📝 Leave &amp; Outpass Approvals</a>

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

            <!-- Right: Notifications + Theme + Profile -->
            <div class="user-profile" style="display: flex; align-items: center; gap: 0.75rem;">
                <!-- Real-Time Notification Bell Dropdown -->
                <div class="notif-dropdown-wrapper" style="position: relative;">
                    <button id="notifBellBtn" onclick="toggleNotifDropdown()" title="Notifications" style="position: relative; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; font-size: 1.1rem;">
                        🔔
                        <span id="notifBadge" style="display: none; position: absolute; top: -4px; right: -4px; background: var(--danger); color: #fff; font-size: 0.65rem; font-weight: 800; border-radius: 10px; padding: 0.1rem 0.35rem; min-width: 16px; text-align: center; border: 2px solid var(--bg-surface); animation: pulse 2s infinite;">0</span>
                    </button>

                    <!-- Notifications Dropdown Panel -->
                    <div id="notifDropdownMenu" style="display: none; position: absolute; right: 0; top: 125%; width: 340px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: 0 12px 30px rgba(0,0,0,0.22); z-index: 10000; overflow: hidden; text-align: left;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); background: var(--bg-main);">
                            <div style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
                                <span>🔔</span> Notifications <span id="notifCountPill" class="badge badge-info" style="font-size: 0.6875rem; display: none;">0 new</span>
                            </div>
                            <button onclick="markAllNotificationsRead()" style="background: none; border: none; font-size: 0.75rem; color: var(--accent-color); font-weight: 600; cursor: pointer; padding: 0;">Mark all read</button>
                        </div>

                        <!-- Notification List Container -->
                        <div id="notifListContainer" style="max-height: 320px; overflow-y: auto; padding: 0.25rem 0;">
                            <div style="padding: 1.5rem; text-align: center; color: var(--text-secondary); font-size: 0.8125rem;">
                                Loading alerts...
                            </div>
                        </div>

                        <div style="padding: 0.625rem 1rem; text-align: center; border-top: 1px solid var(--border-color); background: var(--bg-main);">
                            <a href="/announcements" style="font-size: 0.75rem; color: var(--accent-color); font-weight: 600; text-decoration: none;">View All Campus Circulars &rarr;</a>
                        </div>
                    </div>
                </div>

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

        /* ─── Notification Bell & Dropdown ─── */
        function toggleNotifDropdown() {
            const menu = document.getElementById('notifDropdownMenu');
            if (menu) {
                const isOpen = menu.style.display === 'block';
                menu.style.display = isOpen ? 'none' : 'block';
                if (!isOpen) {
                    loadNotifications();
                }
            }
        }

        function loadNotifications() {
            fetch('/api/notifications/unread')
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById('notifBadge');
                    const pill = document.getElementById('notifCountPill');
                    const list = document.getElementById('notifListContainer');
                    const count = data.count || 0;

                    if (badge) {
                        if (count > 0) {
                            badge.innerText = count > 99 ? '99+' : count;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }

                    if (pill) {
                        if (count > 0) {
                            pill.innerText = count + ' new';
                            pill.style.display = 'inline-block';
                        } else {
                            pill.style.display = 'none';
                        }
                    }

                    if (list) {
                        if (!data.items || data.items.length === 0) {
                            list.innerHTML = `
                                <div style="padding: 2rem 1rem; text-align: center; color: var(--text-secondary); font-size: 0.8125rem;">
                                    <div style="font-size: 1.75rem; margin-bottom: 0.25rem;">✨</div>
                                    <div>All caught up! No unread notifications.</div>
                                </div>
                            `;
                            return;
                        }

                        let html = '';
                        data.items.forEach(item => {
                            const icon = item.type === 'alert' ? '🚨' : (item.type === 'warning' ? '⚠️' : (item.type === 'success' ? '✅' : '📢'));
                            const link = item.link || '/announcements';
                            html += `
                                <div onclick="markNotificationRead(${item.id}, '${link}')" style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.15s ease; display: flex; gap: 0.75rem; align-items: flex-start; text-decoration: none; color: inherit;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='transparent'">
                                    <span style="font-size: 1.25rem; flex-shrink: 0; line-height: 1;">${icon}</span>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                            <div style="font-size: 0.8125rem; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(item.title)}</div>
                                            <span style="font-size: 0.65rem; color: var(--text-secondary); white-space: nowrap;">${timeAgo(item.created_at)}</span>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.2rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            ${escapeHtml(item.message)}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        list.innerHTML = html;
                    }
                })
                .catch(() => {});
        }

        function markNotificationRead(id, redirectUrl) {
            fetch('/notifications/mark-read/' + id, { method: 'POST' })
                .then(() => {
                    loadNotifications();
                    if (redirectUrl && redirectUrl !== '#') {
                        window.location.href = redirectUrl;
                    }
                })
                .catch(() => {
                    if (redirectUrl && redirectUrl !== '#') window.location.href = redirectUrl;
                });
        }

        function markAllNotificationsRead() {
            fetch('/notifications/mark-all-read', { method: 'POST' })
                .then(() => {
                    loadNotifications();
                });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }

        function timeAgo(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - d) / 1000);
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return Math.floor(diff / 86400) + 'd ago';
        }

        // Auto load unread count on page load
        document.addEventListener('DOMContentLoaded', loadNotifications);

        document.addEventListener('click', function(e) {
            const pWrapper = document.querySelector('.profile-dropdown-wrapper');
            if (pWrapper && !pWrapper.contains(e.target)) {
                const pMenu = document.getElementById('profileDropdownMenu');
                if (pMenu) pMenu.style.display = 'none';
            }

            const nWrapper = document.querySelector('.notif-dropdown-wrapper');
            if (nWrapper && !nWrapper.contains(e.target)) {
                const nMenu = document.getElementById('notifDropdownMenu');
                if (nMenu) nMenu.style.display = 'none';
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

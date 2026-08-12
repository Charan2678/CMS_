<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title><?= e($title ?? 'Management ERP') ?> — Kuppam Engineering College</title>
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="stylesheet" href="/assets/css/app.css?v=<?= time() ?>">
    <!-- Lucide Vector Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

<div class="app-layout">
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="brand-logo" style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="/assets/images/logo.png" alt="KEC Logo" style="width: 34px; height: 34px; object-fit: contain;">
                <span style="font-size: 0.875rem; font-weight: 700; color: #1F2937; line-height: 1.2; word-break: break-word; letter-spacing: -0.01em; font-family: 'Plus Jakarta Sans', sans-serif;">Kuppam Engineering College</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php $r = auth_role(); ?>

            <div class="nav-section-title">MAIN</div>
            <a href="/dashboard" class="nav-item <?= request_uri() === '/dashboard' ? 'active' : '' ?>">
                <?= icon('layout-dashboard') ?> <span>Dashboard</span>
            </a>

            <?php if ($r === 'student'): ?>
                <!-- Student Portal Sidebar -->
                <div class="nav-section-title">MY ACADEMICS</div>
                <a href="/attendance" class="nav-item <?= request_uri() === '/attendance' ? 'active' : '' ?>"><?= icon('calendar-check') ?> <span>My Attendance</span></a>
                <a href="/results" class="nav-item <?= request_uri() === '/results' ? 'active' : '' ?>"><?= icon('file-spreadsheet') ?> <span>Semester Results</span></a>
                <a href="/admit-card" class="nav-item <?= request_uri() === '/admit-card' ? 'active' : '' ?>"><?= icon('ticket') ?> <span>Exam Hall Ticket</span></a>
                <a href="/timetable" class="nav-item <?= request_uri() === '/timetable' ? 'active' : '' ?>"><?= icon('clock') ?> <span>Class Timetable</span></a>
                <a href="/leave/apply" class="nav-item <?= request_uri() === '/leave/apply' ? 'active' : '' ?>"><?= icon('file-signature') ?> <span>Apply Leave &amp; Outpass</span></a>

                <div class="nav-section-title">MY FINANCE &amp; SERVICES</div>
                <a href="/fee/payments" class="nav-item <?= request_uri() === '/fee/payments' ? 'active' : '' ?>"><?= icon('receipt') ?> <span>My Fee Receipts</span></a>
                <a href="/fee/pay" class="nav-item <?= (str_starts_with(request_uri(), '/fee/pay/') || request_uri() === '/fee/pay') ? 'active' : '' ?>"><?= icon('credit-card') ?> <span>Pay Fees &amp; Dues (QR)</span></a>
                <a href="/canteen" class="nav-item <?= request_uri() === '/canteen' ? 'active' : '' ?>"><?= icon('utensils') ?> <span>Canteen Menu</span></a>
                <a href="/library" class="nav-item <?= request_uri() === '/library' ? 'active' : '' ?>"><?= icon('book-open') ?> <span>Library Catalog</span></a>
                <a href="/hostel" class="nav-item <?= request_uri() === '/hostel' ? 'active' : '' ?>"><?= icon('building-2') ?> <span>Hostel Details</span></a>
                <a href="/transport" class="nav-item <?= request_uri() === '/transport' ? 'active' : '' ?>"><?= icon('bus') ?> <span>Transport Routes</span></a>
                <a href="/placement/portal" class="nav-item <?= request_uri() === '/placement/portal' ? 'active' : '' ?>"><?= icon('briefcase') ?> <span>Placement Portal</span></a>

                <div class="nav-section-title">CAMPUS COMMUNICATION</div>
                <a href="/announcements" class="nav-item <?= request_uri() === '/announcements' ? 'active' : '' ?>"><?= icon('megaphone') ?> <span>College Announcements</span></a>

            <?php elseif ($r === 'parent'): ?>
                <!-- Parent Portal Sidebar -->
                <div class="nav-section-title">MY CHILD / WARD</div>
                <a href="/profile" class="nav-item <?= request_uri() === '/profile' ? 'active' : '' ?>"><?= icon('graduation-cap') ?> <span>Child Profile &amp; Bio</span></a>
                <a href="/attendance" class="nav-item <?= request_uri() === '/attendance' ? 'active' : '' ?>"><?= icon('calendar-check') ?> <span>Ward Attendance</span></a>
                <a href="/results" class="nav-item <?= request_uri() === '/results' ? 'active' : '' ?>"><?= icon('file-spreadsheet') ?> <span>Semester Results</span></a>
                <a href="/admit-card" class="nav-item <?= request_uri() === '/admit-card' ? 'active' : '' ?>"><?= icon('ticket') ?> <span>Exam Hall Ticket</span></a>
                <a href="/timetable" class="nav-item <?= request_uri() === '/timetable' ? 'active' : '' ?>"><?= icon('clock') ?> <span>Class Timetable</span></a>
                <a href="/leave/apply" class="nav-item <?= request_uri() === '/leave/apply' ? 'active' : '' ?>"><?= icon('file-signature') ?> <span>Apply Ward Leave</span></a>

                <div class="nav-section-title">FEE &amp; PAYMENTS</div>
                <a href="/fee/payments" class="nav-item <?= request_uri() === '/fee/payments' ? 'active' : '' ?>"><?= icon('receipt') ?> <span>Fee Receipts &amp; Dues</span></a>
                <a href="/fee/pay" class="nav-item <?= (str_starts_with(request_uri(), '/fee/pay/') || request_uri() === '/fee/pay') ? 'active' : '' ?>"><?= icon('credit-card') ?> <span>Pay Fees &amp; Dues (QR)</span></a>

                <div class="nav-section-title">CAMPUS COMMUNICATION</div>
                <a href="/announcements" class="nav-item <?= request_uri() === '/announcements' ? 'active' : '' ?>"><?= icon('megaphone') ?> <span>College Announcements</span></a>

            <?php elseif (in_array($r, ['faculty', 'hod'])): ?>
                <!-- Faculty & HOD Sidebar -->
                <div class="nav-section-title">PEOPLE DIRECTORY</div>
                <a href="/students" class="nav-item"><?= icon('users') ?> <span>Students Directory</span></a>
                <?php if ($r === 'hod'): ?>
                    <a href="/faculty" class="nav-item"><?= icon('school') ?> <span>Faculty Directory</span></a>
                <?php endif; ?>

                <div class="nav-section-title">ACADEMIC MANAGEMENT</div>
                <a href="/attendance" class="nav-item"><?= icon('calendar-check') ?> <span>Mark Attendance</span></a>
                <a href="/timetable" class="nav-item"><?= icon('clock') ?> <span>Class Timetable</span></a>
                <a href="/marks/internal" class="nav-item"><?= icon('file-edit') ?> <span>Internal CIA Marks</span></a>
                <a href="/results" class="nav-item"><?= icon('file-spreadsheet') ?> <span>Semester Results</span></a>
                <a href="/leave/review" class="nav-item <?= request_uri() === '/leave/review' ? 'active' : '' ?>"><?= icon('file-signature') ?> <span>Review Leaves &amp; Outpasses</span></a>

                <div class="nav-section-title">COMMUNICATION</div>
                <a href="/announcements" class="nav-item"><?= icon('megaphone') ?> <span>Announcements</span></a>

            <?php elseif ($r === 'accounts_staff'): ?>
                <!-- Accounts Staff Sidebar -->
                <div class="nav-section-title">FINANCE & ACCOUNTS</div>
                <a href="/accounts" class="nav-item"><?= icon('wallet') ?> <span>Accounts &amp; Payroll</span></a>
                <a href="/fee/categories" class="nav-item"><?= icon('tag') ?> <span>Fee Categories</span></a>
                <a href="/fee/structures" class="nav-item"><?= icon('dollar-sign') ?> <span>Fee Structures</span></a>
                <a href="/fee/payments" class="nav-item"><?= icon('receipt') ?> <span>Fee Payments &amp; Receipts</span></a>

                <div class="nav-section-title">REPORTS & ALERTS</div>
                <a href="/reports/financial" class="nav-item"><?= icon('bar-chart-3') ?> <span>Financial Reports</span></a>
                <a href="/announcements" class="nav-item"><?= icon('megaphone') ?> <span>Announcements</span></a>

            <?php elseif ($r === 'librarian'): ?>
                <!-- Librarian Sidebar -->
                <div class="nav-section-title">LIBRARY OPERATIONS</div>
                <a href="/library" class="nav-item"><?= icon('book-open') ?> <span>Library Catalog</span></a>
                <a href="/announcements" class="nav-item"><?= icon('megaphone') ?> <span>Announcements</span></a>

            <?php elseif ($r === 'hostel_warden'): ?>
                <!-- Hostel Warden Sidebar -->
                <div class="nav-section-title">HOSTEL OPERATIONS</div>
                <a href="/hostel" class="nav-item"><?= icon('building-2') ?> <span>Hostel Management</span></a>
                <a href="/leave/outpasses" class="nav-item <?= request_uri() === '/leave/outpasses' ? 'active' : '' ?>"><?= icon('door-open') ?> <span>Outpasses &amp; Check-in</span></a>
                <a href="/announcements" class="nav-item"><?= icon('megaphone') ?> <span>Announcements</span></a>

            <?php elseif ($r === 'transport_manager'): ?>
                <!-- Transport Manager Sidebar -->
                <div class="nav-section-title">TRANSPORT FLEET</div>
                <a href="/transport" class="nav-item"><?= icon('bus') ?> <span>Transport &amp; Bus Routes</span></a>
                <a href="/announcements" class="nav-item"><?= icon('megaphone') ?> <span>Announcements</span></a>

            <?php elseif ($r === 'canteen_manager'): ?>
                <!-- Canteen Manager Sidebar -->
                <div class="nav-section-title">CANTEEN OPERATIONS</div>
                <a href="/canteen" class="nav-item"><?= icon('utensils') ?> <span>Canteen &amp; Mess Menu</span></a>
                <a href="/announcements" class="nav-item"><?= icon('megaphone') ?> <span>Announcements</span></a>

            <?php else: ?>
                <!-- Super Admin / Admin Full Navigation -->
                <div class="nav-section-title">MASTER DATA</div>
                <a href="/master/colleges" class="nav-item"><?= icon('landmark') ?> <span>College Info</span></a>
                <a href="/master/academic-years" class="nav-item"><?= icon('calendar') ?> <span>Academic Years</span></a>
                <a href="/master/departments" class="nav-item"><?= icon('building') ?> <span>Departments</span></a>
                <a href="/master/courses" class="nav-item"><?= icon('graduation-cap') ?> <span>Courses &amp; Semesters</span></a>
                <a href="/master/sections" class="nav-item"><?= icon('layers') ?> <span>Sections</span></a>
                <a href="/master/subjects" class="nav-item"><?= icon('book') ?> <span>Subjects</span></a>

                <div class="nav-section-title">PEOPLE</div>
                <a href="/students" class="nav-item"><?= icon('users') ?> <span>Students Directory</span></a>
                <a href="/faculty" class="nav-item"><?= icon('school') ?> <span>Faculty Directory</span></a>
                <a href="/staff" class="nav-item"><?= icon('briefcase') ?> <span>Non-Faculty Staff</span></a>

                <div class="nav-section-title">ACADEMICS</div>
                <a href="/attendance" class="nav-item"><?= icon('calendar-check') ?> <span>Attendance Roster</span></a>
                <a href="/timetable" class="nav-item"><?= icon('clock') ?> <span>Class Timetable</span></a>
                <a href="/marks/internal" class="nav-item"><?= icon('file-edit') ?> <span>Internal CIA Marks</span></a>
                <a href="/results" class="nav-item"><?= icon('file-spreadsheet') ?> <span>Semester Results</span></a>
                <a href="/admit-cards/manage" class="nav-item"><?= icon('ticket') ?> <span>Exam Hall Tickets</span></a>
                <a href="/leave/review" class="nav-item <?= request_uri() === '/leave/review' ? 'active' : '' ?>"><?= icon('file-signature') ?> <span>Leave &amp; Outpass Approvals</span></a>

                <div class="nav-section-title">FINANCE &amp; FEES</div>
                <a href="/fee/categories" class="nav-item"><?= icon('tag') ?> <span>Fee Categories</span></a>
                <a href="/fee/structures" class="nav-item"><?= icon('dollar-sign') ?> <span>Fee Structures</span></a>
                <a href="/fee/payments" class="nav-item"><?= icon('receipt') ?> <span>Fee Payments &amp; Receipts</span></a>

                <div class="nav-section-title">FACILITIES &amp; OPERATIONS</div>
                <a href="/accounts" class="nav-item"><?= icon('wallet') ?> <span>Accounts &amp; Payroll</span></a>
                <a href="/library" class="nav-item"><?= icon('book-open') ?> <span>Library Catalog</span></a>
                <a href="/hostel" class="nav-item"><?= icon('building-2') ?> <span>Hostel Management</span></a>
                <a href="/transport" class="nav-item"><?= icon('bus') ?> <span>Transport &amp; Routes</span></a>
                <a href="/canteen" class="nav-item"><?= icon('utensils') ?> <span>Canteen &amp; Mess Menu</span></a>

                <div class="nav-section-title">COMMUNICATION &amp; REPORTS</div>
                <a href="/reports/academic" class="nav-item"><?= icon('bar-chart-3') ?> <span>Academic Reports</span></a>
                <a href="/reports/financial" class="nav-item"><?= icon('trending-up') ?> <span>Financial Reports</span></a>
                <a href="/reports/attendance" class="nav-item"><?= icon('calendar-range') ?> <span>Attendance Audit</span></a>
                <a href="/announcements" class="nav-item"><?= icon('megaphone') ?> <span>Announcements</span></a>
                <a href="/audit-logs" class="nav-item"><?= icon('history') ?> <span>System Audit Logs</span></a>
            <?php endif; ?>
        </nav>

        <!-- Heritage Botanical & Architecture Non-Interactive Line-Art (Background Layer) -->
        <div class="sidebar-botanical-bg" aria-hidden="true">
            <svg viewBox="0 0 160 160" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                <!-- Classical University Arch & Pillars -->
                <path d="M 15 155 L 15 80 Q 40 45 65 80 L 65 155" opacity="0.6"/>
                <path d="M 25 155 L 25 85 Q 40 60 55 85 L 55 155" opacity="0.4"/>
                <line x1="5" y1="155" x2="155" y2="155" opacity="0.5"/>
                <!-- Delicate Botanical Stems & Leaves -->
                <path d="M 50 150 Q 80 110 110 70 Q 130 40 145 20" opacity="0.8"/>
                <path d="M 80 110 Q 65 95 60 85 Q 75 88 83 105" opacity="0.7"/>
                <path d="M 98 85 Q 115 78 125 65 Q 118 85 102 83" opacity="0.7"/>
                <path d="M 120 55 Q 105 45 100 35 Q 115 38 123 52" opacity="0.7"/>
                <path d="M 135 32 Q 150 25 155 15 Q 145 30 137 30" opacity="0.7"/>
                <!-- Small Heritage Laurel Accent -->
                <path d="M 90 145 Q 120 135 140 115" opacity="0.5"/>
                <path d="M 115 137 Q 128 128 135 118" opacity="0.4"/>
            </svg>
        </div>
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
                    <button id="notifBellBtn" onclick="toggleNotifDropdown()" title="Notifications" style="position: relative; background: var(--bg-main); border: 1px solid var(--border-color); color: var(--text-primary); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease; box-shadow: var(--shadow-xs);">
                        <?= icon('bell', 'icon-sm') ?>
                        <span id="notifBadge" style="display: none; position: absolute; top: -4px; right: -4px; background: var(--danger); color: #fff; font-size: 0.65rem; font-weight: 800; border-radius: 10px; padding: 0.1rem 0.35rem; min-width: 16px; text-align: center; border: 2px solid var(--bg-surface); animation: pulse 2s infinite;">0</span>
                    </button>

                    <!-- Notifications Dropdown Panel -->
                    <div id="notifDropdownMenu" style="display: none; position: absolute; right: 0; top: 125%; width: 340px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-xl); z-index: 10000; overflow: hidden; text-align: left; backdrop-filter: blur(16px);">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); background: var(--bg-main);">
                            <div style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.45rem;">
                                <?= icon('bell', 'icon-xs') ?> Notifications <span id="notifCountPill" class="badge badge-info" style="font-size: 0.6875rem; display: none;">0 new</span>
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
                            <a href="/announcements" style="font-size: 0.75rem; color: var(--accent-color); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">View All Campus Circulars <?= icon('arrow-right', 'icon-xs') ?></a>
                        </div>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <div class="profile-dropdown-wrapper">
                    <button id="profileDropdownBtn" onclick="toggleProfileDropdown()" style="display: flex; align-items: center; gap: 0.5rem; background: var(--bg-surface); border: 1px solid var(--border-color); padding: 0.25rem 0.625rem 0.25rem 0.35rem; border-radius: 20px; cursor: pointer; transition: all 0.2s ease; box-shadow: var(--shadow-xs);">
                        <!-- Avatar -->
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--bg-peach); border: 1px solid var(--orange-border); color: var(--orange-accent); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);">
                            <?php if (!empty($_SESSION['photo_path'])): ?>
                                <img src="<?= e($_SESSION['photo_path']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <?= icon('user-round', 'icon-sm', ['style' => 'width: 15px; height: 15px; stroke-width: 1.85px;']) ?>
                            <?php endif; ?>
                        </div>
                        <!-- Name/role hidden on small screens via CSS -->
                        <div class="profile-name-text" style="text-align:left;line-height:1.2;">
                            <div style="font-size:0.8125rem;font-weight:700;color:var(--text-primary);"><?= e($_SESSION['username'] ?? 'User') ?></div>
                            <div class="profile-role-text" style="font-size:0.6875rem;color:var(--text-secondary);"><?= e($_SESSION['role_name'] ?? 'Role') ?></div>
                        </div>
                        <span class="profile-arrow" style="font-size:0.75rem;color:var(--text-secondary);"><?= icon('chevron-down', 'icon-xs') ?></span>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="profileDropdownMenu" style="display:none; position: absolute; right: 0; top: 115%; width: 230px; background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: 12px; box-shadow: var(--shadow-xl); z-index: 9999; padding: 0.5rem 0; text-align: left; backdrop-filter: blur(16px);">
                        <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--border-color);border-radius:12px 12px 0 0;background:var(--bg-main);">
                            <div style="font-size:0.875rem;font-weight:700;color:var(--text-primary);"><?= e($_SESSION['username'] ?? 'User') ?></div>
                            <div style="font-size:0.75rem;color:var(--accent-color);font-weight:600;"><?= e($_SESSION['role_name'] ?? 'Role') ?></div>
                        </div>
                        <a href="/profile" style="display:flex;align-items:center;gap:0.625rem;padding:0.75rem 1rem;color:var(--text-primary);font-size:0.8125rem;font-weight:600;text-decoration:none;transition:background 0.15s ease;">
                            <?= icon('user', 'icon-xs') ?> My Profile &amp; Documents
                        </a>
                        <a href="/change-password" style="display:flex;align-items:center;gap:0.625rem;padding:0.75rem 1rem;color:var(--text-primary);font-size:0.8125rem;font-weight:600;text-decoration:none;transition:background 0.15s ease;">
                            <?= icon('lock', 'icon-xs') ?> Security &amp; Password
                        </a>
                        <div style="border-top:1px solid var(--border-color);margin:0.25rem 0;"></div>
                        <a href="/logout" style="display:flex;align-items:center;gap:0.625rem;padding:0.75rem 1rem;color:var(--danger);font-size:0.8125rem;font-weight:700;text-decoration:none;transition:background 0.15s ease;">
                            <?= icon('log-out', 'icon-xs') ?> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            <?= $content ?>
        </main>
    </div>
</div>

    <script>
        /* ─── Lucide Icons Initialization & Refresh Helper ─── */
        window.refreshIcons = function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            try { localStorage.removeItem('theme'); } catch(e) {}
            refreshIcons();
        });

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
                                    <div style="font-size: 1.75rem; margin-bottom: 0.35rem; color: var(--accent-color);"><i data-lucide="sparkles"></i></div>
                                    <div>All caught up! No unread notifications.</div>
                                </div>
                            `;
                            refreshIcons();
                            return;
                        }

                        let html = '';
                        data.items.forEach(item => {
                            const iconName = item.type === 'alert' ? 'alert-octagon' : (item.type === 'warning' ? 'alert-triangle' : (item.type === 'success' ? 'check-circle-2' : 'megaphone'));
                            const link = item.link || '/announcements';
                            html += `
                                <div onclick="markNotificationRead(${item.id}, '${link}')" style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); cursor: pointer; transition: background 0.15s ease; display: flex; gap: 0.75rem; align-items: flex-start; text-decoration: none; color: inherit;" onmouseover="this.style.background='var(--bg-main)'" onmouseout="this.style.background='transparent'">
                                    <span style="font-size: 1.2rem; flex-shrink: 0; line-height: 1; color: var(--accent-color);"><i data-lucide="${iconName}"></i></span>
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
                        refreshIcons();
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
    </script>
</body>
</html>

<?php

declare(strict_types=1);

use App\Core\Router;
use App\Modules\Accounts\controllers\AccountsController;
use App\Modules\Attendance\controllers\AttendanceController;
use App\Modules\Authentication\controllers\AuthController;
use App\Modules\Canteen\controllers\CanteenController;
use App\Modules\Dashboard\controllers\DashboardController;
use App\Modules\Faculty\controllers\FacultyController;
use App\Modules\Fee\controllers\FeeController;
use App\Modules\Hostel\controllers\HostelController;
use App\Modules\Library\controllers\LibraryController;
use App\Modules\Master\controllers\MasterController;
use App\Modules\Reports\controllers\ReportController;
use App\Modules\Result\controllers\ResultController;
use App\Modules\Settings\controllers\NotificationController;
use App\Modules\Staff\controllers\StaffController;
use App\Modules\Student\controllers\StudentController;
use App\Modules\Transport\controllers\TransportController;

/**
 * ============================================================
 * Central Application Routes
 * ============================================================
 */

// ─── Facilities & Operations Routes ─────────────────────────
Router::get('/library',    [LibraryController::class, 'index']);
Router::post('/library',   [LibraryController::class, 'index']);
Router::get('/hostel',     [HostelController::class, 'index']);
Router::post('/hostel',    [HostelController::class, 'index']);
Router::get('/transport',  [TransportController::class, 'index']);
Router::post('/transport', [TransportController::class, 'index']);
Router::get('/canteen',    [CanteenController::class, 'index']);
Router::post('/canteen',   [CanteenController::class, 'index']);
Router::get('/accounts',   [AccountsController::class, 'index']);

// ─── Notifications & Audit Routes ────────────────────────────
Router::get('/announcements',  [NotificationController::class, 'announcements']);
Router::post('/announcements', [NotificationController::class, 'announcements']);
Router::get('/audit-logs',     [NotificationController::class, 'auditLogs']);

// ─── Reports Routes ──────────────────────────────────────────
Router::get('/reports/academic',   [ReportController::class, 'academic']);
Router::get('/reports/financial',  [ReportController::class, 'financial']);
Router::get('/reports/attendance', [ReportController::class, 'attendance']);

// ─── Fee Routes ──────────────────────────────────────────────
Router::get('/fee/categories',   [FeeController::class, 'categories']);
Router::post('/fee/categories',  [FeeController::class, 'categories']);
Router::get('/fee/structures',   [FeeController::class, 'structures']);
Router::post('/fee/structures',  [FeeController::class, 'structures']);
Router::get('/fee/assign',       [FeeController::class, 'assign']);
Router::post('/fee/assign',      [FeeController::class, 'assign']);
Router::get('/fee/payments',     [FeeController::class, 'payments']);
Router::post('/fee/payments',    [FeeController::class, 'payments']);
Router::get('/fee/receipt/{id}', [FeeController::class, 'receipt']);

// ─── Academic Module Routes ──────────────────────────────────
Router::get('/attendance',     [AttendanceController::class, 'index']);
Router::post('/attendance',    [AttendanceController::class, 'index']);
Router::get('/timetable',      [ResultController::class, 'timetable']);
Router::post('/timetable',     [ResultController::class, 'timetable']);
Router::get('/marks/internal', [ResultController::class, 'internalMarks']);
Router::post('/marks/internal',[ResultController::class, 'internalMarks']);
Router::get('/results',            [ResultController::class, 'results']);
Router::post('/results',           [ResultController::class, 'results']);
Router::get('/admit-card',         [ResultController::class, 'admitCard']);
Router::get('/admit-cards/manage',  [ResultController::class, 'admitCardManage']);
Router::post('/admit-cards/manage', [ResultController::class, 'admitCardManage']);

// ─── Staff Routes ───────────────────────────────────────────
Router::get('/staff',        [StaffController::class, 'index']);
Router::get('/staff/create', [StaffController::class, 'create']);
Router::post('/staff/create',[StaffController::class, 'create']);
Router::get('/staff/{id}',   [StaffController::class, 'show']);

// ─── Faculty Routes ──────────────────────────────────────────
Router::get('/faculty',                 [FacultyController::class, 'index']);
Router::get('/faculty/create',          [FacultyController::class, 'create']);
Router::post('/faculty/create',         [FacultyController::class, 'create']);
Router::get('/faculty/assign-subject',  [FacultyController::class, 'assignSubject']);
Router::post('/faculty/assign-subject', [FacultyController::class, 'assignSubject']);
Router::get('/faculty/designations',    [FacultyController::class, 'designations']);
Router::post('/faculty/designations',   [FacultyController::class, 'designations']);
Router::get('/faculty/{id}',            [FacultyController::class, 'show']);

// ─── Student Routes ──────────────────────────────────────────
Router::get('/profile',             [StudentController::class, 'myProfile']);
Router::post('/profile',            [StudentController::class, 'myProfile']);
Router::get('/students',            [StudentController::class, 'index']);
Router::get('/students/admission',  [StudentController::class, 'admission']);
Router::post('/students/admission', [StudentController::class, 'admission']);
Router::get('/students/{id}/send-credentials', [StudentController::class, 'sendCredentials']);
Router::post('/students/{id}/send-credentials', [StudentController::class, 'sendCredentials']);
Router::get('/students/{id}',       [StudentController::class, 'show']);

// ─── Dashboard Route ─────────────────────────────────────────
Router::get('/dashboard',       [DashboardController::class, 'index']);

// ─── Master Data Routes ──────────────────────────────────────
Router::get('/master/colleges',       [MasterController::class, 'collegeInfo']);
Router::post('/master/colleges',      [MasterController::class, 'collegeInfo']);
Router::get('/master/academic-years', [MasterController::class, 'academicYears']);
Router::post('/master/academic-years',[MasterController::class, 'academicYears']);
Router::get('/master/departments',    [MasterController::class, 'departments']);
Router::post('/master/departments',   [MasterController::class, 'departments']);
Router::get('/master/courses',        [MasterController::class, 'courses']);
Router::post('/master/courses',       [MasterController::class, 'courses']);
Router::get('/master/sections',       [MasterController::class, 'sections']);
Router::post('/master/sections',      [MasterController::class, 'sections']);
Router::get('/master/subjects',       [MasterController::class, 'subjects']);
Router::post('/master/subjects',      [MasterController::class, 'subjects']);

// ─── Authentication Routes ───────────────────────────────────
Router::get('/login',           [AuthController::class, 'login']);
Router::post('/login',          [AuthController::class, 'login']);
Router::get('/logout',          [AuthController::class, 'logout']);
Router::get('/forgot-password', [AuthController::class, 'forgotPassword']);
Router::post('/forgot-password',[AuthController::class, 'forgotPassword']);
Router::get('/reset-password',  [AuthController::class, 'resetPassword']);
Router::post('/reset-password', [AuthController::class, 'resetPassword']);
Router::get('/change-password', [AuthController::class, 'changePassword']);
Router::post('/change-password',[AuthController::class, 'changePassword']);

// Default route -> redirect to login if unauthenticated, else dashboard
Router::get('/', function() {
    if (is_authenticated()) {
        redirect('/dashboard');
    }
    redirect('/login');
});

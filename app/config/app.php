<?php

declare(strict_types=1);

use App\Core\Environment;

/**
 * ============================================================
 * App Configuration
 * ============================================================
 * All values are pulled from the .env file.
 * Never hardcode secrets here.
 * ============================================================
 */
return [

    // ─── Application ─────────────────────────────────────────
    'name'  => Environment::get('APP_NAME',  'College Management System'),
    'env'   => Environment::get('APP_ENV',   'production'),
    'debug' => Environment::get('APP_DEBUG', 'false') === 'true',
    'url'   => Environment::get('APP_URL',   'http://localhost'),
    'key'   => Environment::get('APP_KEY',   ''),

    // ─── Timezone ────────────────────────────────────────────
    'timezone' => 'Asia/Kolkata',

    // ─── Session ─────────────────────────────────────────────
    'session' => [
        'name'     => Environment::get('SESSION_NAME',     'cms_session'),
        'lifetime' => (int) Environment::get('SESSION_LIFETIME', 7200),
        'secure'   => Environment::get('SESSION_SECURE',   'false') === 'true',
        'httponly' => true,
        'samesite' => 'Strict',
    ],

    // ─── File Upload ─────────────────────────────────────────
    'upload' => [
        'max_size'      => (int) Environment::get('UPLOAD_MAX_SIZE', 5242880), // 5 MB
        'allowed_types' => explode(',', Environment::get('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,pdf,doc,docx')),
        'path'          => Environment::get('UPLOAD_PATH',  BASE_PATH . '/storage/uploads'),
    ],

    // ─── Security ────────────────────────────────────────────
    'security' => [
        'bcrypt_rounds'      => (int) Environment::get('BCRYPT_ROUNDS',        12),
        'csrf_lifetime'      => (int) Environment::get('CSRF_TOKEN_LIFETIME',  3600),
        'max_login_attempts' => (int) Environment::get('MAX_LOGIN_ATTEMPTS',   5),
        'lockout_duration'   => (int) Environment::get('LOCKOUT_DURATION',     900),
    ],

    // ─── Logging ─────────────────────────────────────────────
    'log' => [
        'path'  => Environment::get('LOG_PATH',   BASE_PATH . '/storage/logs'),
        'level' => Environment::get('LOG_LEVEL',  'error'),
    ],

];

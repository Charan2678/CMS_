<?php

declare(strict_types=1);

/**
 * ============================================================
 * College Management System — Front Controller
 * ============================================================
 *
 * ALL HTTP requests are routed through this single file.
 * The .htaccess in public/ ensures no other file is the entry.
 *
 * Execution order:
 *   1. Define path constants
 *   2. Load Composer autoloader
 *   3. App::boot() → .env → configs → database → session
 *   4. Router dispatches the request
 * ============================================================
 */

// Support for PHP CLI built-in web server static files
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($url !== '/' && file_exists(__DIR__ . $url) && !is_dir(__DIR__ . $url)) {
        return false;
    }
}

// ─── Path Constants ──────────────────────────────────────────
define('BASE_PATH',    dirname(__DIR__));
define('APP_PATH',     BASE_PATH . '/app');
define('CONFIG_PATH',  APP_PATH  . '/config');
define('MODULE_PATH',  APP_PATH  . '/modules');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOAD_PATH',  STORAGE_PATH . '/uploads');
define('LOG_PATH',     STORAGE_PATH . '/logs');
define('PUBLIC_PATH',  __DIR__);

// ─── Composer Autoloader ─────────────────────────────────────
$autoloader = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloader)) {
    http_response_code(500);
    die('[CMS] Autoloader not found. Run: composer install');
}
require_once $autoloader;

// ─── App Bootstrapper ─────────────────────────────────────────
use App\Core\App;
use App\Core\Router;

App::boot();

// ─── Load Routes & Dispatch ──────────────────────────────────
require_once CONFIG_PATH . '/routes.php';

Router::dispatch();

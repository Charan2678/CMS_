<?php

declare(strict_types=1);

define('BASE_PATH',    dirname(__DIR__));
define('APP_PATH',     BASE_PATH . '/app');
define('CONFIG_PATH',  APP_PATH  . '/config');
define('MODULE_PATH',  APP_PATH  . '/modules');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('UPLOAD_PATH',  STORAGE_PATH . '/uploads');
define('LOG_PATH',     STORAGE_PATH . '/logs');
define('PUBLIC_PATH',  BASE_PATH . '/public');

require_once BASE_PATH . '/vendor/autoload.php';

require_once BASE_PATH . '/app/core/Environment.php';
\App\Core\Environment::load(BASE_PATH . '/.env');

$dbConfig = require CONFIG_PATH . '/database.php';
\App\Core\Database::configure($dbConfig);

// Mock active session for CLI / Testing environment if session is uninitialized
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

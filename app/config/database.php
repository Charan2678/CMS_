<?php

declare(strict_types=1);

use App\Core\Environment;

/**
 * ============================================================
 * Database Configuration
 * ============================================================
 * Consumed by Database::configure() during App::boot().
 * Values come exclusively from .env — never hardcoded here.
 * ============================================================
 */
return [
    'host'     => Environment::get('DB_HOST',     '127.0.0.1'),
    'port'     => Environment::get('DB_PORT',     '3306'),
    'database' => Environment::get('DB_DATABASE', 'cms'),
    'username' => Environment::get('DB_USERNAME', 'root'),
    'password' => Environment::get('DB_PASSWORD', ''),
];

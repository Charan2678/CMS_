<?php

declare(strict_types=1);

namespace App\Core;

/**
 * App
 *
 * Application bootstrapper. Runs once at the start of every request.
 *
 * Execution order (called from public/index.php):
 *   App::boot()
 *     1. Load .env
 *     2. Load app config
 *     3. Configure database
 *     4. Set error reporting
 *     5. Set timezone
 *     6. Start session
 */
class App
{
    /** @var array Loaded app configuration */
    private static array $config = [];

    /**
     * Bootstrap the application.
     * Must be the first call in public/index.php after constants are defined.
     */
    public static function boot(): void
    {
        // Step 1: Parse .env file
        Environment::load(BASE_PATH . '/.env');

        // Step 2: Load app configuration
        self::$config = require CONFIG_PATH . '/app.php';

        // Step 3: Configure database connection (does NOT connect yet)
        // The connection is lazy — it opens only when Database::get() is first called
        $dbConfig = require CONFIG_PATH . '/database.php';
        Database::configure($dbConfig);

        // Step 4: Error reporting
        if (self::$config['debug'] === true) {
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
            error_reporting(0);
        }

        // Step 5: Timezone
        date_default_timezone_set('Asia/Kolkata');

        // Step 6: Session
        self::startSession();
    }

    /**
     * Configure and start the PHP session.
     */
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return; // Already started
        }

        $cfg = self::$config['session'];

        session_name($cfg['name']);

        session_set_cookie_params([
            'lifetime' => 0,                // Session cookie (expires when browser closes)
            'path'     => '/',
            'domain'   => '',
            'secure'   => $cfg['secure'],   // HTTPS only in production
            'httponly' => true,             // No JS access to session cookie
            'samesite' => 'Lax',            // CSRF mitigation with standard navigation compatibility
        ]);

        session_start();

        // Mark session initiated
        if (!isset($_SESSION['_initiated'])) {
            $_SESSION['_initiated'] = true;
        }
    }

    /**
     * Get a config value.
     *
     * @param string $key Dot-notation key, e.g. 'session.name'
     */
    public static function config(string $key = ''): mixed
    {
        if ($key === '') {
            return self::$config;
        }

        $parts = explode('.', $key);
        $value = self::$config;

        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return null;
            }
            $value = $value[$part];
        }

        return $value;
    }
}

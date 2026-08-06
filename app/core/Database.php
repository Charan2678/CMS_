<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database
 *
 * PDO Singleton. One connection for the entire request lifecycle.
 *
 * ─── Rules ───────────────────────────────────────────────────
 * - NEVER open a new PDO() anywhere else in the application.
 * - ALWAYS use Database::get() to obtain the connection.
 * - ALWAYS use prepared statements. Never interpolate values.
 * ─────────────────────────────────────────────────────────────
 *
 * Usage:
 *   Database::configure($config);        // done in App::boot()
 *   $pdo = Database::get();              // anywhere in the app
 *   $stmt = Database::get()->prepare('SELECT * FROM users WHERE id = :id');
 *   $stmt->execute([':id' => $id]);
 *   $row = $stmt->fetch();
 */
class Database
{
    /** @var PDO|null Singleton instance */
    private static ?PDO $instance = null;

    /** @var array Connection configuration */
    private static array $config = [];

    /**
     * Set database configuration.
     * Must be called before get().
     */
    public static function configure(array $config): void
    {
        self::$config = $config;
    }

    /**
     * Get the singleton PDO connection.
     * Creates the connection on first call.
     *
     * @throws \RuntimeException if not configured or connection fails
     */
    public static function get(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Close the connection (e.g., for long-running scripts).
     */
    public static function close(): void
    {
        self::$instance = null;
    }

    /**
     * Test that the connection is alive (ping).
     */
    public static function ping(): bool
    {
        try {
            self::get()->query('SELECT 1');
            return true;
        } catch (PDOException) {
            self::$instance = null; // Force reconnect on next get()
            return false;
        }
    }

    /**
     * Create and return a new PDO connection with hardened settings.
     *
     * @throws \RuntimeException on failure (hides credentials from error message)
     */
    private static function createConnection(): PDO
    {
        if (empty(self::$config)) {
            throw new \RuntimeException(
                'Database is not configured. Ensure Database::configure() is called during bootstrap.'
            );
        }

        $cfg = self::$config;

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $cfg['host']     ?? '127.0.0.1',
            $cfg['port']     ?? '3306',
            $cfg['database'] ?? 'cms'
        );

        $options = [
            // Throw exceptions on all errors (never silent failures)
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

            // Always return rows as associative arrays
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            // Use real prepared statements (not emulated)
            // This prevents a class of SQL injection attacks
            PDO::ATTR_EMULATE_PREPARES   => false,

            // Do not persist connection across requests
            PDO::ATTR_PERSISTENT         => false,

            // Enforce charset at connection level (not just DSN)
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $pdo = new PDO(
                $dsn,
                $cfg['username'] ?? 'root',
                $cfg['password'] ?? '',
                $options
            );
        } catch (PDOException $e) {
            // NEVER expose $e->getMessage() to the user or logs in production.
            // It contains credentials and server info.
            $safe = 'Database connection failed. '
                  . 'Check DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD in your .env file.';

            // In debug mode, log the real error internally
            if (($_ENV['APP_DEBUG'] ?? 'false') === 'true') {
                error_log('[CMS Database] ' . $e->getMessage());
            }

            throw new \RuntimeException($safe);
        }

        return $pdo;
    }

    // ─── Prevent instantiation ────────────────────────────────
    private function __construct() {}
    private function __clone()     {}
}

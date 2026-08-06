<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Environment
 *
 * Parses the .env file and makes all keys available via
 * $_ENV, $_SERVER, and getenv().
 *
 * Usage:
 *   Environment::load(BASE_PATH . '/.env');
 *   Environment::get('DB_HOST');        // returns value or null
 *   Environment::get('DB_PORT', 3306);  // returns value or default
 */
class Environment
{
    private static bool $loaded = false;

    /**
     * Load and parse a .env file.
     * Safe to call multiple times — only loads once.
     *
     * @throws \RuntimeException if file does not exist
     */
    public static function load(string $filePath): void
    {
        if (self::$loaded) {
            return;
        }

        if (!file_exists($filePath)) {
            throw new \RuntimeException(
                '.env file not found. Copy .env.example to .env and configure it.'
            );
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($lines === false) {
            throw new \RuntimeException('.env file could not be read.');
        }

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip blank lines and comments
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // Must contain an = sign
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key   = trim($key);
            $value = trim($value);

            // Skip keys with spaces (invalid)
            if (str_contains($key, ' ')) {
                continue;
            }

            // Strip surrounding quotes: "value" or 'value'
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last  = $value[-1];

                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            // Make available in all standard PHP env mechanisms
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable by key.
     *
     * @param mixed $default Returned if key is not found or value is empty string
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }

    /**
     * Check whether a key exists in the environment.
     */
    public static function has(string $key): bool
    {
        return isset($_ENV[$key]) || getenv($key) !== false;
    }

    /**
     * Reset loaded state (used in testing only).
     */
    public static function reset(): void
    {
        self::$loaded = false;
    }
}

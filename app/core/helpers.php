<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Environment;

/**
 * ============================================================
 * Global Helper Functions
 * ============================================================
 * Loaded via composer.json autoload.files.
 * Available everywhere without import.
 * ============================================================
 */

// ─── Environment ─────────────────────────────────────────────

/**
 * Get an environment variable value.
 *
 * @param mixed $default Returned if the key is missing or empty
 */
function env(string $key, mixed $default = null): mixed
{
    return Environment::get($key, $default);
}

// ─── Database ────────────────────────────────────────────────

/**
 * Get the PDO database connection.
 * Use this everywhere instead of new PDO().
 */
function db(): \PDO
{
    return Database::get();
}

// ─── Paths ───────────────────────────────────────────────────

/**
 * Get an absolute path relative to the project root.
 */
function base_path(string $path = ''): string
{
    return BASE_PATH . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

/**
 * Get an absolute path relative to app/.
 */
function app_path(string $path = ''): string
{
    return APP_PATH . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

/**
 * Get an absolute path relative to storage/.
 */
function storage_path(string $path = ''): string
{
    return STORAGE_PATH . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

/**
 * Get an absolute path relative to public/.
 */
function public_path(string $path = ''): string
{
    return PUBLIC_PATH . ($path !== '' ? DIRECTORY_SEPARATOR . ltrim($path, '/\\') : '');
}

// ─── Configuration ───────────────────────────────────────────

/**
 * Load and return a value from a config file.
 *
 * Config files live in app/config/ and return arrays.
 * Use dot notation: config('app.debug'), config('database.host')
 *
 * Results are cached after first load.
 */
function config(string $key): mixed
{
    static $cache = [];

    $parts   = explode('.', $key, 2);
    $file    = $parts[0];
    $subKey  = $parts[1] ?? null;

    if (!isset($cache[$file])) {
        $path = CONFIG_PATH . DIRECTORY_SEPARATOR . $file . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException("Config file not found: config/{$file}.php");
        }

        $cache[$file] = require $path;
    }

    if ($subKey === null) {
        return $cache[$file];
    }

    return $cache[$file][$subKey] ?? null;
}

// ─── Security / Output ───────────────────────────────────────

/**
 * Escape a string for safe HTML output (prevents XSS).
 * Use this on EVERY value printed to the browser.
 *
 * Example: <?= e($student['name']) ?>
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate a CSRF token and store it in session.
 * Returns the token string.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf_token'];
}

/**
 * Verify a submitted CSRF token against the session token.
 */
function csrf_verify(string $token): bool
{
    if (empty($token)) {
        return false;
    }
    if (!isset($_SESSION['_csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['_csrf_token'], $token);
}

/**
 * Render a hidden CSRF input field.
 * Place inside every <form> tag.
 *
 * Example: <?= csrf_field() ?>
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
}

// ─── HTTP ────────────────────────────────────────────────────

/**
 * Redirect to a URL and stop execution.
 */
function redirect(string $url): never
{
    header('Location: ' . $url, true, 302);
    exit;
}

/**
 * Check if the current request is POST.
 */
function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/**
 * Get the current request URI (path only, no query string).
 */
function request_uri(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $pos = strpos($uri, '?');

    return $pos !== false ? substr($uri, 0, $pos) : $uri;
}

/**
 * Get a value from $_POST with optional default.
 * Does NOT sanitize — sanitize at the point of use.
 */
function post(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $default;
}

/**
 * Get a value from $_GET with optional default.
 */
function query(string $key, mixed $default = null): mixed
{
    return $_GET[$key] ?? $default;
}

// ─── Session ─────────────────────────────────────────────────

/**
 * Get a session value.
 */
function session(string $key, mixed $default = null): mixed
{
    return $_SESSION[$key] ?? $default;
}

/**
 * Set a session value.
 */
function session_set(string $key, mixed $value): void
{
    $_SESSION[$key] = $value;
}

/**
 * Delete a session key.
 */
function session_forget(string $key): void
{
    unset($_SESSION[$key]);
}

/**
 * Flash a one-time message to session.
 */
function flash(string $key, mixed $value): void
{
    $_SESSION['_flash'][$key] = $value;
}

/**
 * Get and immediately remove a flash message.
 */
function flash_get(string $key, mixed $default = null): mixed
{
    $value = $_SESSION['_flash'][$key] ?? $default;
    unset($_SESSION['_flash'][$key]);
    return $value;
}

// ─── Auth ────────────────────────────────────────────────────

/**
 * Check if a user is currently logged in.
 */
function is_authenticated(): bool
{
    return isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
}

/**
 * Get the currently logged-in user's ID.
 */
function auth_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

/**
 * Get the currently logged-in user's role code.
 */
function auth_role(): ?string
{
    return $_SESSION['role_code'] ?? $_SESSION['user_role'] ?? null;
}

/**
 * Abort with an HTTP status code if condition is true.
 */
function abort(int $code, string $message = ''): never
{
    http_response_code($code);
    echo $message ?: 'HTTP ' . $code;
    exit;
}

/**
 * Record an immutable audit log entry.
 */
function audit_log(string $action, string $module, ?array $oldValues = null, ?array $newValues = null): void
{
    try {
        $stmt = db()->prepare('
            INSERT INTO audit_logs (
                college_id, user_id, action, module, old_values, new_values, ip_address, user_agent, created_at
            ) VALUES (
                :college_id, :user_id, :action, :module, :old_values, :new_values, :ip, :ua, NOW()
            )
        ');
        $stmt->execute([
            ':college_id' => $_SESSION['college_id'] ?? 1,
            ':user_id'    => auth_id() ?? 0,
            ':action'     => substr($action, 0, 100),
            ':module'     => substr($module, 0, 50),
            ':old_values' => $oldValues ? json_encode($oldValues) : null,
            ':new_values' => $newValues ? json_encode($newValues) : null,
            ':ip'         => substr($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', 0, 45),
            ':ua'         => substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 500),
        ]);
    } catch (\Exception $e) {
        // Fail silently for audit logging to not disrupt main transaction flow
    }
}

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
 * Alias for flash().
 */
function flash_set(string $key, mixed $value): void
{
    flash($key, $value);
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
 * Get the currently logged-in user's display name.
 */
function auth_name(): string
{
    if (!empty($_SESSION['name'])) {
        return (string) $_SESSION['name'];
    }

    if (!empty($_SESSION['linked_type']) && !empty($_SESSION['linked_id'])) {
        $linkedType = $_SESSION['linked_type'];
        $linkedId   = (int) $_SESSION['linked_id'];
        $tbl        = match($linkedType) {
            'student' => 'students',
            'faculty' => 'faculty',
            'staff'   => 'staff',
            default   => null
        };
        if ($tbl) {
            try {
                $stmt = db()->prepare("SELECT first_name, last_name FROM {$tbl} WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $linkedId]);
                $row = $stmt->fetch();
                if ($row && !empty($row['first_name'])) {
                    $_SESSION['name'] = trim($row['first_name'] . ' ' . ($row['last_name'] ?? ''));
                    return $_SESSION['name'];
                }
            } catch (\Throwable $e) {}
        }
    }

    $username = (string) ($_SESSION['username'] ?? 'User');
    if (preg_match('/^[0-9]{4}-[A-Z]+-[0-9]+/i', $username)) {
        try {
            $stmt = db()->prepare("SELECT first_name, last_name FROM students WHERE roll_number = :roll LIMIT 1");
            $stmt->execute([':roll' => $username]);
            $row = $stmt->fetch();
            if ($row && !empty($row['first_name'])) {
                $_SESSION['name'] = trim($row['first_name'] . ' ' . ($row['last_name'] ?? ''));
                return $_SESSION['name'];
            }
        } catch (\Throwable $e) {}
    }

    return $username;
}

/**
 * Get the currently logged-in user's avatar image URL if available.
 */
function auth_avatar(): ?string
{
    return $_SESSION['profile_photo'] ?? $_SESSION['avatar'] ?? null;
}




/**
 * Get the currently logged-in user array.
 */
function auth_user(): ?array
{
    if (!is_authenticated()) {
        return null;
    }
    return [
        'id'          => auth_id(),
        'username'    => session('username'),
        'role_code'   => auth_role(),
        'role_id'     => session('role_id'),
        'linked_type' => session('linked_type'),
        'linked_id'   => session('linked_id'),
        'college_id'  => session('college_id') ?? 1,
    ];
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

// ─── File Upload Validation ──────────────────────────────────

/**
 * Reusable file upload validator.
 * Validates error codes, max size, server-side MIME type via finfo, allowed extensions, and path safety.
 *
 * @param array $file Single $_FILES element, e.g. $_FILES['document']
 * @return array ['ok' => bool, 'error' => ?string]
 */
function validate_upload(array $file): array
{
    // 1. Reject if error code is present
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'File upload failed or no file provided.'];
    }

    // 2. Reject if file size exceeds maximum limit
    $maxSize = (int) env('UPLOAD_MAX_SIZE', 5242880);
    if (($file['size'] ?? 0) > $maxSize) {
        $maxMb = round($maxSize / (1024 * 1024), 2);
        return ['ok' => false, 'error' => "File size exceeds maximum allowed limit of {$maxMb}MB."];
    }

    // 3. Reject path traversal, null bytes, or missing/disallowed extensions
    $filename = $file['name'] ?? '';
    if (str_contains($filename, '..') || str_contains($filename, "\0")) {
        return ['ok' => false, 'error' => 'Invalid file name. Path traversal detected.'];
    }

    $allowedTypesEnv = env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,pdf,doc,docx,webp,gif');
    $allowedExts     = array_map('trim', explode(',', strtolower((string) $allowedTypesEnv)));

    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (empty($ext) || !in_array(strtolower($ext), $allowedExts, true)) {
        return ['ok' => false, 'error' => 'File must have a valid allowed extension.'];
    }

    // 4. Inspect real server-side MIME type via finfo
    if (empty($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        return ['ok' => false, 'error' => 'Uploaded temporary file does not exist.'];
    }

    $finfoHandle = @finfo_open(FILEINFO_MIME_TYPE);
    $detectedMime = $finfoHandle ? @finfo_file($finfoHandle, $file['tmp_name']) : false;
    if ($finfoHandle) {
        @finfo_close($finfoHandle);
    }

    if (!$detectedMime) {
        return ['ok' => false, 'error' => 'Could not determine file MIME type.'];
    }

    // 5. Map allowed extensions to explicit MIME whitelist
    $mimeMap = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
    ];

    $allowedMimes = [];
    foreach ($allowedExts as $extName) {
        if (isset($mimeMap[$extName])) {
            $allowedMimes[] = $mimeMap[$extName];
        }
    }

    if (!in_array($detectedMime, $allowedMimes, true)) {
        return ['ok' => false, 'error' => "Invalid file type '{$detectedMime}'. Allowed types: " . implode(', ', $allowedExts)];
    }

    return ['ok' => true, 'error' => null];
}

/**
 * Send an HTML email notification.
 *
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $body HTML content body
 * @return bool
 */
function send_mail(string $to, string $subject, string $body): bool
{
    return \App\Core\Mailer::send($to, $subject, $body);
}

// ─── Currency to Words Helper ────────────────────────────────

/**
 * Convert numerical Indian Rupee currency amount into English words.
 *
 * Example: 25000.00 -> "Twenty Five Thousand Rupees Only"
 */
function number_to_words_inr(float $number): string
{
    $number = abs($number);
    $no = (int) floor($number);
    $point = (int) round(($number - $no) * 100);
    $digits_1 = strlen((string)$no);
    $i = 0;
    $str = [];
    $words = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
        50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty',
        90 => 'Ninety'
    ];
    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];

    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $number_part = $no % $divider;
        $no = (int) floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;
        if ($number_part) {
            $counter = count($str);
            $plural = ($counter && $number_part > 9) ? '' : '';
            $unit = ($counter < count($digits)) ? ' ' . $digits[$counter] : '';
            $hundred = ($counter == 1 && isset($str[0]) && $str[0]) ? ' and ' : null;
            $str[] = ($number_part < 21)
                ? $words[$number_part] . $unit . $plural . $hundred
                : $words[(int) floor($number_part / 10) * 10] . ' ' . $words[$number_part % 10] . $unit . $plural . $hundred;
        } else {
            $str[] = null;
        }
    }
    $str = array_filter(array_reverse($str));
    $result = trim(preg_replace('/\s+/', ' ', implode(' ', $str)));
    $result = $result ? $result . ' Rupees' : 'Zero Rupees';

    if ($point > 0) {
        $paise = ($point < 21)
            ? $words[$point]
            : $words[(int) floor($point / 10) * 10] . ' ' . $words[$point % 10];
        $result .= ' and ' . trim($paise) . ' Paise';
    }
    return $result . ' Only';
}

/**
 * Render a Lucide vector icon tag.
 *
 * @param string $name Lucide icon name (e.g. 'graduation-cap', 'check-circle-2', 'credit-card')
 * @param string $class Extra CSS classes
 * @param array $attrs Extra HTML attributes
 */
function icon(string $name, string $class = '', array $attrs = []): string
{
    $attrString = '';
    foreach ($attrs as $k => $v) {
        $attrString .= ' ' . htmlspecialchars((string) $k, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8') . '"';
    }
    $classes = trim('lucide-icon ' . $class);
    return '<i data-lucide="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '"' . $attrString . '></i>';
}



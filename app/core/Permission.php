<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Permission (RBAC Engine)
 *
 * Checks and enforces granular, database-driven permissions.
 * Never use hardcoded role strings like `if ($role == 'Admin')`.
 * Always use `Permission::has('student.create')`.
 */
class Permission
{
    private static ?array $userPermissions = null;

    /**
     * Check if the currently logged-in user has a specific permission code.
     */
    public static function has(string $permissionCode): bool
    {
        // Unauthenticated users have no permissions
        if (!is_authenticated()) {
            return false;
        }

        // Super Admin always has full access, without needing a DB round-trip
        if (($_SESSION['role_code'] ?? null) === 'super_admin') {
            return true;
        }

        return in_array($permissionCode, self::loadUserPermissions(), true);
    }

    /**
     * Enforce a permission. Aborts with HTTP 403 if permission is denied.
     * Unauthenticated users are redirected to the login page instead.
     */
    public static function enforce(string $permissionCode): void
    {
        if (self::has($permissionCode)) {
            return;
        }

        // Not logged in at all -> send to login, not a raw 403
        if (!is_authenticated()) {
            if (is_post()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Your session has expired. Please log in again.'
                ]);
                exit;
            }
            redirect('/login');
        }

        if (is_post()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Access Denied: You do not have permission (' . $permissionCode . ') to perform this action.'
            ]);
            exit;
        }

        http_response_code(403);
        if (file_exists(APP_PATH . '/modules/Master/views/403.php')) {
            require APP_PATH . '/modules/Master/views/403.php';
        } else {
            echo "<h1>403 Forbidden</h1><p>You do not have permission ({$permissionCode}) to access this resource.</p>";
        }
        exit;
    }

    /**
     * Load permissions granted to the current user's role.
     */
    private static function loadUserPermissions(): array
    {
        if (self::$userPermissions !== null) {
            return self::$userPermissions;
        }

        $roleId = $_SESSION['role_id'] ?? null;
        if (!$roleId) {
            self::$userPermissions = [];
            return self::$userPermissions;
        }

        $stmt = db()->prepare('
            SELECT p.code
            FROM role_permissions rp
            JOIN permissions p ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id AND rp.granted = 1
        ');
        $stmt->execute([':role_id' => $roleId]);

        self::$userPermissions = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        return self::$userPermissions;
    }

    /**
     * Clear cached permission list (used after permission updates).
     */
    public static function clearCache(): void
    {
        self::$userPermissions = null;
    }
}

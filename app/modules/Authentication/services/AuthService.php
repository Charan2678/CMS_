<?php

declare(strict_types=1);

namespace App\Modules\Authentication\services;

use App\Core\Database;
use App\Core\Security;
use PDO;

class AuthService
{
    /**
     * Authenticate a user by username or email.
     * Returns an array with ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function login(string $loginId, string $password, ?string $roleType = null, string $ipAddress = '127.0.0.1', string $userAgent = 'Unknown'): array
    {
        // 1. Check lockout
        if (Security::isLockedOut($loginId, $ipAddress)) {
            return [
                'success' => false,
                'message' => 'Too many failed login attempts. Please try again in 15 minutes.',
                'user'    => null
            ];
        }

        // 2. Fetch user
        $stmt = db()->prepare('
            SELECT u.*, r.code AS role_code, r.name AS role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.username = :username OR u.email = :email
            LIMIT 1
        ');
        $stmt->execute([':username' => $loginId, ':email' => $loginId]);
        $user = $stmt->fetch();

        if (!$user) {
            Security::recordLoginAttempt(null, $ipAddress, $userAgent, 'failed');
            return [
                'success' => false,
                'message' => 'Invalid username/email or password.',
                'user'    => null
            ];
        }

        // 3. Verify selected role matches user's actual role
        if (!empty($roleType)) {
            $userRoleCode = strtolower((string) ($user['role_code'] ?? ''));
            $targetRoleType = strtolower($roleType);

            $isValidRole = false;
            if ($targetRoleType === 'admin') {
                $isValidRole = in_array($userRoleCode, ['admin', 'super_admin', 'head_of_coe', 'tpo'], true);
            } elseif ($targetRoleType === 'student') {
                $isValidRole = ($userRoleCode === 'student' || ($user['linked_type'] ?? '') === 'student');
            } elseif ($targetRoleType === 'parent') {
                $isValidRole = ($userRoleCode === 'parent' || ($user['linked_type'] ?? '') === 'parent');
            } elseif ($targetRoleType === 'staff') {
                $isValidRole = (!in_array($userRoleCode, ['student', 'parent'], true));
            }

            if (!$isValidRole) {
                Security::recordLoginAttempt((int) $user['id'], $ipAddress, $userAgent, 'failed');
                return [
                    'success' => false,
                    'message' => 'Invalid role selected for this account. Please select the correct role (Admin, Student, Parent, or Staff).',
                    'user'    => null
                ];
            }
        }

        // 4. Check active state
        if ((int) $user['is_active'] !== 1) {
            Security::recordLoginAttempt((int) $user['id'], $ipAddress, $userAgent, 'failed');
            return [
                'success' => false,
                'message' => 'Your account is deactivated. Please contact administrator.',
                'user'    => null
            ];
        }

        // 5. Verify password
        if (!Security::verifyPassword($password, $user['password_hash'])) {
            Security::recordLoginAttempt((int) $user['id'], $ipAddress, $userAgent, 'failed');
            return [
                'success' => false,
                'message' => 'Invalid username/email or password.',
                'user'    => null
            ];
        }

        // 6. Successful login
        Security::recordLoginAttempt((int) $user['id'], $ipAddress, $userAgent, 'success');

        // Update last_login
        $upd = db()->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
        $upd->execute([':id' => $user['id']]);

        // Regenerate Session & Store User details
        if (!headers_sent() && session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['user_id']              = (int) $user['id'];
        $_SESSION['college_id']           = (int) $user['college_id'];
        $_SESSION['username']             = $user['username'];
        $_SESSION['email']                = $user['email'];
        $_SESSION['role_id']              = (int) $user['role_id'];
        $_SESSION['role_code']            = $user['role_code'];
        $_SESSION['role_name']            = $user['role_name'];
        $_SESSION['linked_type']          = $user['linked_type'];
        $_SESSION['linked_id']            = (int) $user['linked_id'];
        $_SESSION['must_change_password'] = (int) $user['must_change_password'] === 1;

        // Parent-specific ward resolution
        if ($user['linked_type'] === 'parent') {
            try {
                $gStmt = db()->prepare('
                    SELECT g.student_id, g.name AS guardian_name, g.relationship,
                           s.first_name, s.last_name, s.roll_number
                    FROM guardians g
                    JOIN students s ON s.id = g.student_id
                    WHERE g.id = :gid
                    LIMIT 1
                ');
                $gStmt->execute([':gid' => (int) $user['linked_id']]);
                $guardian = $gStmt->fetch();
                if ($guardian) {
                    $_SESSION['parent_ward_id']   = (int) $guardian['student_id'];
                    $_SESSION['ward_name']        = trim($guardian['first_name'] . ' ' . $guardian['last_name']);
                    $_SESSION['ward_roll_number'] = $guardian['roll_number'];
                    $_SESSION['guardian_name']    = $guardian['guardian_name'];
                    $_SESSION['relationship']     = $guardian['relationship'];
                }
            } catch (\Throwable $e) {
                // Fail gracefully
            }
        }

        return [
            'success' => true,
            'message' => 'Login successful.',
            'user'    => $user
        ];
    }

    /**
     * Log out current user and destroy session.
     */
    public function logout(): void
    {
        if (isset($_SESSION['user_id'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            Security::recordLoginAttempt((int) $_SESSION['user_id'], $ip, $ua, 'success');
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Change user password.
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array
    {
        $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        if (!$user || !Security::verifyPassword($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters long.'];
        }

        $newHash = Security::hashPassword($newPassword);
        $upd = db()->prepare('
            UPDATE users
            SET password_hash = :hash, must_change_password = 0, updated_at = NOW()
            WHERE id = :id
        ');
        $upd->execute([':hash' => $newHash, ':id' => $userId]);

        $_SESSION['must_change_password'] = false;

        return ['success' => true, 'message' => 'Password updated successfully.'];
    }

    /**
     * Request password reset token.
     */
    public function requestReset(string $email): array
    {
        $stmt = db()->prepare('SELECT id, username FROM users WHERE email = :email AND is_active = 1 LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Return success even if email not found to prevent email enumeration
            return ['success' => true, 'message' => 'If an account exists with that email, a password reset link has been generated.'];
        }

        $token = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $ins = db()->prepare('
            INSERT INTO password_resets (user_id, token, expires_at, created_at)
            VALUES (:user_id, :token, :expires_at, NOW())
        ');
        $ins->execute([
            ':user_id'    => $user['id'],
            ':token'      => $hashedToken,
            ':expires_at' => $expiresAt
        ]);

        return [
            'success' => true,
            'message' => 'Password reset token generated.',
            'token'   => $token // returned for development/display
        ];
    }

    /**
     * Reset password using token.
     */
    public function resetPassword(string $token, string $newPassword): array
    {
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters long.'];
        }

        $hashedToken = hash('sha256', $token);

        $stmt = db()->prepare('
            SELECT * FROM password_resets
            WHERE token = :token AND used_at IS NULL AND expires_at > NOW()
            ORDER BY created_at DESC LIMIT 1
        ');
        $stmt->execute([':token' => $hashedToken]);
        $reset = $stmt->fetch();

        if (!$reset) {
            return ['success' => false, 'message' => 'Invalid or expired password reset token.'];
        }

        $newHash = Security::hashPassword($newPassword);

        db()->beginTransaction();
        try {
            $updUser = db()->prepare('UPDATE users SET password_hash = :hash, updated_at = NOW() WHERE id = :id');
            $updUser->execute([':hash' => $newHash, ':id' => $reset['user_id']]);

            $updReset = db()->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = :id');
            $updReset->execute([':id' => $reset['id']]);

            db()->commit();
            return ['success' => true, 'message' => 'Password has been reset successfully. You can now login.'];
        } catch (\Exception $e) {
            db()->rollBack();
            return ['success' => false, 'message' => 'Failed to reset password.'];
        }
    }
}

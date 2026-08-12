<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Security
 *
 * Handles password hashing, verification, login attempt tracking, and CSRF utilities.
 */
class Security
{
    /**
     * Hash a plaintext password using bcrypt.
     */
    public static function hashPassword(string $password): string
    {
        $cost = (int) config('app.security.bcrypt_rounds') ?: 12;
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    /**
     * Verify a plaintext password against a stored bcrypt hash.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if a specific user account is currently locked out due to excessive failed attempts.
     */
    public static function isLockedOut(string $username, string $ip): bool
    {
        $maxAttempts = (int) config('app.security.max_login_attempts') ?: 5;
        $lockoutSecs = (int) config('app.security.lockout_duration') ?: 900; // 15 mins

        $stmt = db()->prepare('
            SELECT COUNT(*) FROM login_history
            WHERE user_id = (SELECT id FROM users WHERE username = :u OR email = :u LIMIT 1)
              AND status = "failed"
              AND attempted_at >= DATE_SUB(NOW(), INTERVAL :secs SECOND)
        ');
        $stmt->bindValue(':u', $username);
        $stmt->bindValue(':secs', $lockoutSecs, \PDO::PARAM_INT);
        $stmt->execute();

        return ((int) $stmt->fetchColumn()) >= $maxAttempts;
    }

    /**
     * Clear failed login attempts for a user after a successful login.
     */
    public static function clearFailedAttempts(int $userId): void
    {
        try {
            $stmt = db()->prepare('
                DELETE FROM login_history
                WHERE user_id = :user_id AND status = "failed"
            ');
            $stmt->execute([':user_id' => $userId]);
        } catch (\Exception $e) {
            // Fail silently
        }
    }

    /**
     * Record a login attempt into login_history table.
     */
    public static function recordLoginAttempt(?int $userId, string $ip, string $userAgent, string $status): void
    {
        try {
            if ($status === 'success' && $userId && $userId > 0) {
                self::clearFailedAttempts($userId);
            }

            $stmt = db()->prepare('
                INSERT INTO login_history (user_id, ip_address, user_agent, status, attempted_at)
                VALUES (:user_id, :ip, :ua, :status, NOW())
            ');
            $stmt->execute([
                ':user_id' => ($userId && $userId > 0) ? $userId : null,
                ':ip'      => substr($ip, 0, 45),
                ':ua'      => substr($userAgent, 0, 500),
                ':status'  => $status,
            ]);
        } catch (\Exception $e) {
            // Fail silently for login security logging
        }
    }
}

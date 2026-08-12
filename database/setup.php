<?php
declare(strict_types=1);

/**
 * Database Setup & Seed Utility Script
 *
 * Usage: php database/setup.php
 */

require_once __DIR__ . '/../app/core/Environment.php';
\App\Core\Environment::load(__DIR__ . '/../.env');

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';
$dbName = getenv('DB_DATABASE') ?: 'cms';

echo "=== Initializing CMS Database Setup ===\n";

function getPdo($host, $port, $user, $pass, $dbName = null) {
    $dsn = "mysql:host=$host;port=$port" . ($dbName ? ";dbname=$dbName" : "") . ";charset=utf8mb4";
    return new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ]);
}

try {
    $pdo = getPdo($host, $port, $user, $pass);
    echo "[✓] Connected to MySQL Server.\n";

    echo "[*] Recreating clean database `$dbName`...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");
    $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[✓] Database `$dbName` ready.\n";

    // 1. Execute schema.sql
    $pdo = getPdo($host, $port, $user, $pass, $dbName);
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        echo "[*] Applying schema: database/schema.sql...\n";
        $sql = file_get_contents($schemaFile);
        $pdo->exec($sql);
        echo "[✓] schema.sql executed successfully.\n";
    }

    // 1.5. Execute migrations
    $migrationDir = __DIR__ . '/migrations';
    if (is_dir($migrationDir)) {
        $migrationFiles = glob($migrationDir . '/*.sql');
        sort($migrationFiles);
        foreach ($migrationFiles as $migFile) {
            $baseName = basename($migFile);
            $pdo = getPdo($host, $port, $user, $pass, $dbName);
            $sql = file_get_contents($migFile);
            if (!empty(trim($sql))) {
                try {
                    $pdo->exec($sql);
                    echo "[✓] Executed migration: $baseName\n";
                } catch (Exception $e) {
                    echo "[!] Migration note ($baseName): " . $e->getMessage() . "\n";
                }
            }
        }
    }

    // 2. Execute seeds
    $seedDir = __DIR__ . '/seeds';
    if (is_dir($seedDir)) {
        $seedFiles = glob($seedDir . '/*.sql');
        sort($seedFiles);
        foreach ($seedFiles as $seedFile) {
            $baseName = basename($seedFile);
            echo "[*] Executing seed: $baseName...\n";
            $pdo = getPdo($host, $port, $user, $pass, $dbName);
            $sql = file_get_contents($seedFile);
            if (!empty(trim($sql))) {
                $pdo->exec($sql);
            }
            echo "[✓] Finished seed: $baseName\n";
        }
    }

    // Set known demo passwords (Password123!)
    $pdo = getPdo($host, $port, $user, $pass, $dbName);
    $password = 'Password123!';
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash, is_active = 1");
    $stmt->execute([':hash' => $hash]);
    echo "[✓] Set all test user passwords to: '$password'\n";

    // Check users
    $users = $pdo->query("SELECT id, username, email, role_id, linked_type, is_active FROM users")->fetchAll(PDO::FETCH_ASSOC);
    echo "\n=== Available Users for Login (Password: $password) ===\n";
    foreach ($users as $u) {
        echo sprintf("  - ID: %d | Username: %-15s | Email: %-25s | Role ID: %d | Linked: %s\n", 
            $u['id'], $u['username'], $u['email'], $u['role_id'], $u['linked_type'] ?? 'none');
    }

    // Check tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "\n[✓] Total tables created in `$dbName`: " . count($tables) . "\n";
    echo "=== Database Setup Complete! ===\n";

} catch (Exception $e) {
    echo "[✗] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

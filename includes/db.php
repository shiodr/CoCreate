<?php
$configPath = __DIR__ . '/config.php';
if (is_file($configPath)) {
    require_once $configPath;
}

$host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
$dbname = defined('DB_NAME') ? DB_NAME : 'cocreate';
$username = defined('DB_USER') ? DB_USER : 'root';
$password = defined('DB_PASS') ? DB_PASS : '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Please check includes/config.php.');
}

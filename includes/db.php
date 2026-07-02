<?php
$configPath = __DIR__ . "/config.php";
if (is_file($configPath)) {
    require_once $configPath;
}

$host = defined("DB_HOST") ? DB_HOST : "sql308.infinityfree.com";
$dbname = defined("DB_NAME") ? DB_NAME : "if0_42202301_cocreate";
$username = defined("DB_USER") ? DB_USER : "if0_42202301";
$password = defined("DB_PASS") ? DB_PASS : "b9lqGhYWJJ2TZ62";

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS project_links (
            link_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            project_id INT UNSIGNED NOT NULL,
            link_label VARCHAR(80) NOT NULL,
            link_url VARCHAR(2048) NOT NULL,
            sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_project_links_project
                FOREIGN KEY (project_id) REFERENCES projects(project_id)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    );
} catch (PDOException $e) {
    http_response_code(500);
    die("Database connection failed. Please check includes/config.php.");
}

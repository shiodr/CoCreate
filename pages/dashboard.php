<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$recentStmt = $pdo->query('SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id WHERE p.project_status <> "completed" ORDER BY p.created_at DESC LIMIT 6');
$recent = $recentStmt->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/dashboard.html';
require_once __DIR__ . '/../includes/footer.php';
?>

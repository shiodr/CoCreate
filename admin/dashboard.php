<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '../pages/';
$adminPrefix = '';
$totalUsers = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalProjects = (int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
$totalRequests = (int)$pdo->query('SELECT COUNT(*) FROM join_requests')->fetchColumn();

$users = $pdo->query('SELECT user_id, firstname, lastname, username, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();
$projects = $pdo->query('SELECT p.project_id, p.project_title, p.project_status, p.created_at, u.username FROM projects p JOIN users u ON u.user_id = p.user_id ORDER BY p.created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/admin/dashboard.html';
require_once __DIR__ . '/../includes/footer.php';
?>

<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$stmt = $pdo->prepare('SELECT jr.*, p.project_title, p.project_status, p.category, u.username AS owner FROM join_requests jr JOIN projects p ON p.project_id = jr.project_id JOIN users u ON u.user_id = p.user_id WHERE jr.user_id = ? ORDER BY jr.created_at DESC');
$stmt->execute([current_user_id()]);
$requests = $stmt->fetchAll();

$pageTitle = 'My Join Requests';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/my_requests.html';
require_once __DIR__ . '/../includes/footer.php';
?>

<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '../pages/';
$adminPrefix = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $userId = (int)($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($userId === current_user_id()) {
        flash('error', 'You cannot modify your own account here.');
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$userId]);
        flash('success', 'User deleted.');
    } elseif ($action === 'disable') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'disabled' WHERE user_id = ?");
        $stmt->execute([$userId]);
        flash('success', 'User disabled.');
    } elseif ($action === 'enable') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $stmt->execute([$userId]);
        flash('success', 'User enabled.');
    }

    header('Location: users.php');
    exit;
}

$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/admin/users.html';
require_once __DIR__ . '/../includes/footer.php';
?>

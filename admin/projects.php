<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '../pages/';
$adminPrefix = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        $projectId = (int)($_POST['project_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = ?');
        $stmt->execute([$projectId]);
        flash('success', 'Project removed.');
    }
    header('Location: projects.php');
    exit;
}

$projects = $pdo->query('SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id ORDER BY p.created_at DESC')->fetchAll();

$pageTitle = 'Manage Projects';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/admin/projects.html';
require_once __DIR__ . '/../includes/footer.php';
?>

<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$uid = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_project') {
        $projectId = (int)($_POST['project_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = ? AND user_id = ?');
        $stmt->execute([$projectId, $uid]);
        flash('success', 'Project deleted.');
    }

    if ($action === 'update_request') {
        $requestId = (int)($_POST['request_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['accepted', 'rejected', 'pending'], true)) {
            $stmt = $pdo->prepare('UPDATE join_requests jr JOIN projects p ON p.project_id = jr.project_id SET jr.request_status = ? WHERE jr.request_id = ? AND p.user_id = ?');
            $stmt->execute([$status, $requestId, $uid]);
            flash('success', 'Request updated.');
        }
    }

    header('Location: my_projects.php');
    exit;
}

$projectStmt = $pdo->prepare('SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC');
$projectStmt->execute([$uid]);
$projects = $projectStmt->fetchAll();

$requestStmt = $pdo->prepare('SELECT jr.*, p.project_title, u.firstname, u.lastname, u.username, u.email, u.skills FROM join_requests jr JOIN projects p ON p.project_id = jr.project_id JOIN users u ON u.user_id = jr.user_id WHERE p.user_id = ? ORDER BY jr.created_at DESC');
$requestStmt->execute([$uid]);
$requests = $requestStmt->fetchAll();

$pageTitle = 'My Projects';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/my_projects.html';
require_once __DIR__ . '/../includes/footer.php';
?>

<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT p.*, u.username, u.firstname, u.lastname, u.skills AS owner_skills FROM projects p JOIN users u ON u.user_id = p.user_id WHERE p.project_id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    $pageTitle = 'Project Not Found';
    $emptyStateMessage = 'Project not found.';
    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/../views/empty_state.html';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$isOwner = is_logged_in() && current_user_id() === (int)$project['user_id'];
$existingRequest = null;

if (is_logged_in() && !$isOwner) {
    $check = $pdo->prepare('SELECT * FROM join_requests WHERE project_id = ? AND user_id = ?');
    $check->execute([$id, current_user_id()]);
    $existingRequest = $check->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    check_csrf();
    if ($isOwner) {
        flash('error', 'You cannot request to join your own project.');
    } elseif ($existingRequest) {
        flash('error', 'You already submitted a request for this project.');
    } else {
        $message = trim($_POST['message'] ?? '');
        $insert = $pdo->prepare('INSERT INTO join_requests (project_id, user_id, message) VALUES (?, ?, ?)');
        $insert->execute([$id, current_user_id(), $message]);
        flash('success', 'Join request submitted successfully.');
    }
    header('Location: project.php?id=' . $id);
    exit;
}

$pageTitle = $project['project_title'];
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/project.html';
require_once __DIR__ . '/../includes/footer.php';
?>

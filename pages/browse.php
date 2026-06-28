<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$search = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$skill = trim($_GET['skill'] ?? '');
$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(p.project_title LIKE ? OR p.description LIKE ? OR p.required_skills LIKE ? OR p.category LIKE ?)';
    $term = '%' . $search . '%';
    array_push($params, $term, $term, $term, $term);
}

if ($status !== '' && valid_project_status($status)) {
    $where[] = 'p.project_status = ?';
    $params[] = $status;
}

if ($skill !== '') {
    $where[] = 'p.required_skills LIKE ?';
    $params[] = '%' . $skill . '%';
}

$sql = 'SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$pageTitle = 'Browse Projects';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/browse.html';
require_once __DIR__ . '/../includes/footer.php';
?>

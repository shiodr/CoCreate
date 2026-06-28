<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '../pages/';
$adminPrefix = '';
$requests = $pdo->query('SELECT jr.*, p.project_title, owner.username AS owner, applicant.username AS applicant FROM join_requests jr JOIN projects p ON p.project_id = jr.project_id JOIN users owner ON owner.user_id = p.user_id JOIN users applicant ON applicant.user_id = jr.user_id ORDER BY jr.created_at DESC')->fetchAll();

$pageTitle = 'Monitor Join Requests';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/admin/requests.html';
require_once __DIR__ . '/../includes/footer.php';
?>

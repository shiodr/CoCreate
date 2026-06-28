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
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>Monitor join requests</h1>
  </div>
</section>

<div class="table-wrap">
  <table>
    <thead><tr><th>Project</th><th>Owner</th><th>Applicant</th><th>Status</th><th>Message</th><th>Created</th></tr></thead>
    <tbody>
    <?php foreach ($requests as $request): ?>
      <tr>
        <td><?= e($request['project_title']) ?></td>
        <td>@<?= e($request['owner']) ?></td>
        <td>@<?= e($request['applicant']) ?></td>
        <td><span class="status status-<?= e($request['request_status']) ?>"><?= e(status_label($request['request_status'])) ?></span></td>
        <td><?= e($request['message'] ?: 'No message provided.') ?></td>
        <td class="muted"><?= e(date('M j, Y', strtotime($request['created_at']))) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

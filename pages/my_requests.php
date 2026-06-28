<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$stmt = $pdo->prepare('SELECT jr.*, p.project_title, p.project_status, p.category, p.project_image, u.username AS owner FROM join_requests jr JOIN projects p ON p.project_id = jr.project_id JOIN users u ON u.user_id = p.user_id WHERE jr.user_id = ? ORDER BY jr.created_at DESC');
$stmt->execute([current_user_id()]);
$requests = $stmt->fetchAll();

$pageTitle = 'My Join Requests';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Applications</p>
    <h1>My join requests</h1>
  </div>
  <a class="btn btn-secondary" href="browse.php">Find Projects</a>
</section>

<?php if (!$requests): ?>
  <div class="empty-state">You have not requested to join any projects yet.</div>
<?php else: ?>
  <div class="cards-grid">
    <?php foreach ($requests as $request): ?>
      <article class="card project-card">
        <a class="project-image-link" href="project.php?id=<?= (int)$request['project_id'] ?>">
          <?php if (!empty($request['project_image'])): ?>
            <img class="project-image" src="../<?= e($request['project_image']) ?>" alt="<?= e($request['project_title']) ?>">
          <?php else: ?>
            <span class="project-image project-image-placeholder" data-category="<?= e($request['category']) ?>"><strong><?= e(substr($request['project_title'], 0, 1)) ?></strong></span>
          <?php endif; ?>
        </a>
        <div class="card-meta">
          <span class="status status-<?= e($request['request_status']) ?>"><?= e(status_label($request['request_status'])) ?></span>
          <span><?= e(date('M j, Y', strtotime($request['created_at']))) ?></span>
        </div>
        <h3><a href="project.php?id=<?= (int)$request['project_id'] ?>"><?= e($request['project_title']) ?></a></h3>
        <p class="muted">Owner @<?= e($request['owner']) ?> - <?= e($request['category']) ?></p>
        <p><?= e($request['message'] ?: 'No message provided.') ?></p>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

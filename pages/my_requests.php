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
      <?php ob_start(); ?>
      <p><?= e($request['message'] ?: 'No message provided.') ?></p>
      <?php render_project_card($request, [
          'status_key' => 'request_status',
          'show_skills' => false,
          'meta' => 'Owner @' . $request['owner'] . ' - ' . $request['category'],
          'footer_html' => ob_get_clean(),
      ]); ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

<?php
require_once __DIR__ . "/../includes/session.php";
require_login();
require_once __DIR__ . "/../includes/db.php";

$assetPrefix = "../";
$pagePrefix = "";
$adminPrefix = "../admin/";

$recentStmt = $pdo->query(
    'SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id WHERE p.project_status <> "completed" ORDER BY p.created_at DESC LIMIT 6',
);
$recent = $recentStmt->fetchAll();

$pageTitle = "Dashboard";
require_once __DIR__ . "/../includes/header.php";
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Dashboard</p>
    <h1>Welcome, <?= e($_SESSION["firstname"] ?? "Creator") ?></h1>
  </div>
  <a class="btn btn-primary" href="create_project.php">Create Project</a>
</section>

<section class="section">
  <div class="section-head">
    <h2>Recent projects</h2>
    <a href="browse.php">View all</a>
  </div>
  <div class="cards-grid">
    <?php foreach ($recent as $project): ?>
      <?php render_project_card($project, [
          "meta" => "Created by @" . $project["username"],
      ]); ?>
    <?php endforeach; ?>
    <?php if (!$recent): ?>
      <p class="muted">No projects yet. Start the first one.</p>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . "/../includes/footer.php";
?>

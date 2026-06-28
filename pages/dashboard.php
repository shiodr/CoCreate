<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$recentStmt = $pdo->query('SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id WHERE p.project_status <> "completed" ORDER BY p.created_at DESC LIMIT 6');
$recent = $recentStmt->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Dashboard</p>
    <h1>Welcome, <?= e($_SESSION['firstname'] ?? 'Creator') ?></h1>
  </div>
  <a class="btn btn-primary" href="create_project.php">Create Project</a>
</section>

<div class="quick-actions">
  <a href="browse.php">Browse Projects</a>
  <a href="create_project.php">Create Project</a>
  <a href="my_projects.php">My Projects</a>
  <a href="my_requests.php">Join Requests</a>
  <a href="profile.php">Profile</a>
</div>

<section class="section">
  <div class="section-head">
    <h2>Recent projects</h2>
    <a href="browse.php">View all</a>
  </div>
  <div class="cards-grid">
    <?php foreach ($recent as $project): ?>
      <article class="card project-card">
        <a class="project-image-link" href="project.php?id=<?= (int)$project['project_id'] ?>">
          <?php if (!empty($project['project_image'])): ?>
            <img class="project-image" src="../<?= e($project['project_image']) ?>" alt="<?= e($project['project_title']) ?>">
          <?php else: ?>
            <span class="project-image project-image-placeholder"><?= e(substr($project['project_title'], 0, 1)) ?></span>
          <?php endif; ?>
        </a>
        <div class="card-meta">
          <span class="status status-<?= e($project['project_status']) ?>"><?= e(status_label($project['project_status'])) ?></span>
          <span><?= e(date('M j, Y', strtotime($project['created_at']))) ?></span>
        </div>
        <h3><a href="project.php?id=<?= (int)$project['project_id'] ?>"><?= e($project['project_title']) ?></a></h3>
        <p><?= e(excerpt($project['description'], 140)) ?></p>
        <p class="muted">Created by @<?= e($project['username']) ?></p>
        <div class="tag-row">
          <?php foreach (array_filter(array_map('trim', explode(',', $project['required_skills']))) as $skill): ?>
            <span class="tag"><?= e($skill) ?></span>
          <?php endforeach; ?>
        </div>
      </article>
    <?php endforeach; ?>
    <?php if (!$recent): ?>
      <p class="muted">No projects yet. Start the first one.</p>
    <?php endif; ?>
  </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

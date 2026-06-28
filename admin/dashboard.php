<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$totalUsers = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalProjects = (int)$pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
$totalRequests = (int)$pdo->query('SELECT COUNT(*) FROM join_requests')->fetchColumn();

$users = $pdo->query('SELECT user_id, firstname, lastname, username, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();
$projects = $pdo->query('SELECT p.project_id, p.project_title, p.project_status, p.created_at, u.username FROM projects p JOIN users u ON u.user_id = p.user_id ORDER BY p.created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>System dashboard</h1>
  </div>
</section>

<div class="stats">
  <div class="stat"><span class="num"><?= $totalUsers ?></span><span class="lbl">Users</span></div>
  <div class="stat"><span class="num"><?= $totalProjects ?></span><span class="lbl">Projects</span></div>
  <div class="stat"><span class="num"><?= $totalRequests ?></span><span class="lbl">Join Requests</span></div>
</div>

<div class="quick-actions">
  <a href="users.php">Manage Users</a>
  <a href="projects.php">Manage Projects</a>
  <a href="requests.php">Monitor Requests</a>
</div>

<div class="admin-grid">
  <section class="card">
    <div class="section-head"><h2>Recent users</h2><a href="users.php">Manage</a></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Name</th><th>Role</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= e($user['firstname'] . ' ' . $user['lastname']) ?><br><span class="muted">@<?= e($user['username']) ?></span></td>
            <td><?= e($user['role']) ?></td>
            <td><span class="status status-<?= $user['status'] === 'active' ? 'accepted' : 'rejected' ?>"><?= e($user['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="card">
    <div class="section-head"><h2>Recent projects</h2><a href="projects.php">Manage</a></div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Project</th><th>Owner</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($projects as $project): ?>
          <tr>
            <td><?= e($project['project_title']) ?></td>
            <td>@<?= e($project['username']) ?></td>
            <td><span class="status status-<?= e($project['project_status']) ?>"><?= e(status_label($project['project_status'])) ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

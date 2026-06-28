<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '../pages/';
$adminPrefix = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        $projectId = (int)($_POST['project_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = ?');
        $stmt->execute([$projectId]);
        flash('success', 'Project removed.');
    }
    header('Location: projects.php');
    exit;
}

$projects = $pdo->query('SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id ORDER BY p.created_at DESC')->fetchAll();

$pageTitle = 'Manage Projects';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>Manage projects</h1>
  </div>
</section>

<div class="table-wrap">
  <table>
    <thead><tr><th>Title</th><th>Owner</th><th>Category</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($projects as $project): ?>
      <tr>
        <td><a href="<?= e($pagePrefix ?? '../pages/') ?>project.php?id=<?= (int)$project['project_id'] ?>"><?= e($project['project_title']) ?></a></td>
        <td>@<?= e($project['username']) ?></td>
        <td><?= e($project['category']) ?></td>
        <td><span class="status status-<?= e($project['project_status']) ?>"><?= e(status_label($project['project_status'])) ?></span></td>
        <td class="muted"><?= e(date('M j, Y', strtotime($project['created_at']))) ?></td>
        <td>
          <form class="inline-form" method="post" data-confirm="Remove this project?">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="project_id" value="<?= (int)$project['project_id'] ?>">
            <button class="btn btn-danger" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/uploads.php';

$assetPrefix = '../';
$pagePrefix = '../pages/';
$adminPrefix = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        $projectId = (int)($_POST['project_id'] ?? 0);
        $imageStmt = $pdo->prepare('SELECT project_image FROM projects WHERE project_id = ?');
        $imageStmt->execute([$projectId]);
        $projectImage = $imageStmt->fetchColumn();
        $stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = ?');
        $stmt->execute([$projectId]);
        if ($stmt->rowCount() > 0) {
            delete_uploaded_file($projectImage ?: null, __DIR__ . '/..');
        }
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
    <thead><tr><th>Project</th><th>Owner</th><th>Category</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($projects as $project): ?>
      <tr>
        <td>
          <div class="table-project">
            <?php render_project_media($project, 'table-thumb'); ?>
            <a href="<?= e($pagePrefix) ?>project.php?id=<?= (int)$project['project_id'] ?>"><?= e($project['project_title']) ?></a>
          </div>
        </td>
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

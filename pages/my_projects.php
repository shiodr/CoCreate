<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/uploads.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$uid = current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete_project') {
        $projectId = (int)($_POST['project_id'] ?? 0);
        $imageStmt = $pdo->prepare('SELECT project_image FROM projects WHERE project_id = ? AND user_id = ?');
        $imageStmt->execute([$projectId, $uid]);
        $projectImage = $imageStmt->fetchColumn();
        $stmt = $pdo->prepare('DELETE FROM projects WHERE project_id = ? AND user_id = ?');
        $stmt->execute([$projectId, $uid]);
        if ($stmt->rowCount() > 0) {
            delete_uploaded_file($projectImage ?: null, __DIR__ . '/..');
        }
        flash('success', 'Project deleted.');
    }

    if ($action === 'update_request') {
        $requestId = (int)($_POST['request_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['accepted', 'rejected', 'pending'], true)) {
            $stmt = $pdo->prepare('UPDATE join_requests jr JOIN projects p ON p.project_id = jr.project_id SET jr.request_status = ? WHERE jr.request_id = ? AND p.user_id = ?');
            $stmt->execute([$status, $requestId, $uid]);
            flash('success', 'Request updated.');
        }
    }

    header('Location: my_projects.php');
    exit;
}

$projectStmt = $pdo->prepare('SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC');
$projectStmt->execute([$uid]);
$projects = $projectStmt->fetchAll();

$requestStmt = $pdo->prepare('SELECT jr.*, p.project_title, u.firstname, u.lastname, u.username, u.email, u.skills FROM join_requests jr JOIN projects p ON p.project_id = jr.project_id JOIN users u ON u.user_id = jr.user_id WHERE p.user_id = ? ORDER BY jr.created_at DESC');
$requestStmt->execute([$uid]);
$requests = $requestStmt->fetchAll();

$pageTitle = 'My Projects';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Workspace</p>
    <h1>My projects and requests</h1>
  </div>
  <a class="btn btn-primary" href="create_project.php">New Project</a>
</section>

<section class="section">
  <h2>Your projects</h2>
  <div class="cards-grid">
    <?php foreach ($projects as $project): ?>
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
        <p><?= e(excerpt($project['description'], 130)) ?></p>
        <div class="button-row">
          <a class="btn btn-secondary" href="edit_project.php?id=<?= (int)$project['project_id'] ?>">Edit</a>
          <form method="post" data-confirm="Delete this project? This cannot be undone.">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_project">
            <input type="hidden" name="project_id" value="<?= (int)$project['project_id'] ?>">
            <button class="btn btn-danger" type="submit">Delete</button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
    <?php if (!$projects): ?><div class="empty-state">You have not created any projects yet.</div><?php endif; ?>
  </div>
</section>

<section class="section">
  <h2>Incoming join requests</h2>
  <?php if (!$requests): ?>
    <div class="empty-state">No incoming requests yet.</div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Project</th><th>Applicant</th><th>Message</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($requests as $request): ?>
          <tr>
            <td><?= e($request['project_title']) ?></td>
            <td>
              <?= e($request['firstname'] . ' ' . $request['lastname']) ?><br>
              <span class="muted">@<?= e($request['username']) ?> - <?= e($request['skills']) ?></span>
            </td>
            <td><?= e($request['message'] ?: 'No message provided.') ?></td>
            <td><span class="status status-<?= e($request['request_status']) ?>"><?= e(status_label($request['request_status'])) ?></span></td>
            <td>
              <?php if ($request['request_status'] === 'pending'): ?>
                <form class="inline-form" method="post">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                  <input type="hidden" name="action" value="update_request">
                  <input type="hidden" name="request_id" value="<?= (int)$request['request_id'] ?>">
                  <button class="btn btn-secondary" name="status" value="accepted">Accept</button>
                  <button class="btn btn-danger" name="status" value="rejected">Reject</button>
                </form>
              <?php else: ?>
                <span class="muted">Updated</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

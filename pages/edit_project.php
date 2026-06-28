<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM projects WHERE project_id = ? AND user_id = ?');
$stmt->execute([$id, current_user_id()]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    $pageTitle = 'Project Not Found';
    $emptyStateMessage = 'Project not found or you do not have permission to edit it.';
    require_once __DIR__ . '/../includes/header.php';
    ?>
<div class="empty-state"><?= e($emptyStateMessage) ?></div>

<?php
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $project['project_title'] = trim($_POST['project_title'] ?? '');
    $project['description'] = trim($_POST['description'] ?? '');
    $project['required_skills'] = trim($_POST['required_skills'] ?? '');
    $project['category'] = trim($_POST['category'] ?? '');
    $project['project_status'] = trim($_POST['project_status'] ?? 'open');

    if ($project['project_title'] === '') $errors[] = 'Project title is required.';
    if ($project['description'] === '') $errors[] = 'Description is required.';
    if ($project['required_skills'] === '') $errors[] = 'Required skills are required.';
    if ($project['category'] === '') $errors[] = 'Project category is required.';
    if (!valid_project_status($project['project_status'])) $errors[] = 'Invalid project status.';

    if (!$errors) {
        $update = $pdo->prepare('UPDATE projects SET project_title = ?, description = ?, required_skills = ?, category = ?, project_status = ? WHERE project_id = ? AND user_id = ?');
        $update->execute([
            $project['project_title'],
            $project['description'],
            $project['required_skills'],
            $project['category'],
            $project['project_status'],
            $id,
            current_user_id(),
        ]);
        flash('success', 'Project updated successfully.');
        header('Location: my_projects.php');
        exit;
    }
}

$pageTitle = 'Edit Project';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Edit project</p>
    <h1><?= e($project['project_title']) ?></h1>
  </div>
</section>

<form class="card form-card wide" method="post" data-validate>
  <?php foreach ($errors as $error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endforeach; ?>
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <label>Project title<input required name="project_title" value="<?= e($project['project_title']) ?>"></label>
  <label>Description<textarea required name="description" rows="7"><?= e($project['description']) ?></textarea></label>
  <div class="two-col">
    <label>Required skills<input required name="required_skills" value="<?= e($project['required_skills']) ?>"></label>
    <label>Category<input required name="category" value="<?= e($project['category']) ?>"></label>
  </div>
  <label>Project status
    <select name="project_status" required>
      <option value="open" <?= $project['project_status'] === 'open' ? 'selected' : '' ?>>Open</option>
      <option value="in_progress" <?= $project['project_status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
      <option value="completed" <?= $project['project_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>
  </label>
  <button class="btn btn-primary" type="submit">Update Project</button>
</form>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

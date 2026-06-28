<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/uploads.php';

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
    $removeImage = ($_POST['remove_project_image'] ?? '') === '1';
    $hasNewImage = isset($_FILES['project_image']) && $_FILES['project_image']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($project['project_title'] === '') $errors[] = 'Project title is required.';
    if ($project['description'] === '') $errors[] = 'Description is required.';
    if ($project['required_skills'] === '') $errors[] = 'Required skills are required.';
    if ($project['category'] === '') $errors[] = 'Project category is required.';
    if (!valid_project_status($project['project_status'])) $errors[] = 'Invalid project status.';
    if ($hasNewImage) {
        $imageError = validate_project_image($_FILES['project_image']);
        if ($imageError) $errors[] = $imageError;
    }

    if (!$errors) {
        $projectImage = $project['project_image'] ?? null;

        if ($hasNewImage) {
            [$newImage, $imageError] = save_project_image($_FILES['project_image'], $id, __DIR__ . '/..');
            if ($imageError) {
                $errors[] = $imageError;
            } else {
                delete_uploaded_file($projectImage, __DIR__ . '/..');
                $projectImage = $newImage;
            }
        } elseif ($removeImage) {
            delete_uploaded_file($projectImage, __DIR__ . '/..');
            $projectImage = null;
        }
    }

    if (!$errors) {
        $update = $pdo->prepare('UPDATE projects SET project_title = ?, description = ?, required_skills = ?, category = ?, project_status = ?, project_image = ? WHERE project_id = ? AND user_id = ?');
        $update->execute([
            $project['project_title'],
            $project['description'],
            $project['required_skills'],
            $project['category'],
            $project['project_status'],
            $projectImage,
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

<form class="card form-card wide" method="post" enctype="multipart/form-data" data-validate>
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
  <?php if (!empty($project['project_image'])): ?>
    <div class="image-preview">
      <img src="<?= e(($publicPrefix ?? '../') . $project['project_image']) ?>" alt="Current project image">
      <label class="check-row"><input type="checkbox" name="remove_project_image" value="1"> Remove current image</label>
    </div>
  <?php endif; ?>
  <label>Replace project image <span class="field-hint">Optional. JPG, PNG, GIF, or WebP up to 3 MB.</span>
    <input type="file" name="project_image" accept="image/*">
  </label>
  <button class="btn btn-primary" type="submit">Update Project</button>
</form>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

<?php
require_once __DIR__ . '/includes/session.php';
require_login();
require_once __DIR__ . '/includes/db.php';

$errors = [];
$project = [
    'project_title' => '',
    'description' => '',
    'required_skills' => '',
    'category' => '',
    'project_status' => 'open',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    foreach ($project as $key => $value) {
        $project[$key] = trim($_POST[$key] ?? $value);
    }

    if ($project['project_title'] === '') $errors[] = 'Project title is required.';
    if ($project['description'] === '') $errors[] = 'Description is required.';
    if ($project['required_skills'] === '') $errors[] = 'Required skills are required.';
    if ($project['category'] === '') $errors[] = 'Project category is required.';
    if (!valid_project_status($project['project_status'])) $errors[] = 'Invalid project status.';

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO projects (user_id, project_title, description, required_skills, category, project_status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            current_user_id(),
            $project['project_title'],
            $project['description'],
            $project['required_skills'],
            $project['category'],
            $project['project_status'],
        ]);
        flash('success', 'Project created successfully.');
        header('Location: my_projects.php');
        exit;
    }
}

$pageTitle = 'Create Project';
require_once __DIR__ . '/includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">New project</p>
    <h1>Create project</h1>
  </div>
</section>

<form class="card form-card wide" method="post" data-validate>
  <?php foreach ($errors as $error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endforeach; ?>
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <label>Project title<input required name="project_title" value="<?= e($project['project_title']) ?>"></label>
  <label>Description<textarea required name="description" rows="7"><?= e($project['description']) ?></textarea></label>
  <div class="two-col">
    <label>Required skills<input required name="required_skills" value="<?= e($project['required_skills']) ?>" placeholder="PHP, UI design, writing"></label>
    <label>Category<input required name="category" value="<?= e($project['category']) ?>" placeholder="Web app, art, music"></label>
  </div>
  <label>Project status
    <select name="project_status" required>
      <option value="open" <?= $project['project_status'] === 'open' ? 'selected' : '' ?>>Open</option>
      <option value="in_progress" <?= $project['project_status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
      <option value="completed" <?= $project['project_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>
  </label>
  <button class="btn btn-primary" type="submit">Save Project</button>
</form>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

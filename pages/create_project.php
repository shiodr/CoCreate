<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/uploads.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$errors = [];
$skillOptions = cocreate_skill_options($pdo);
$categoryOptions = cocreate_project_category_options();
$project = [
    'project_title' => '',
    'description' => '',
    'required_skills' => '',
    'category' => '',
    'project_status' => 'open',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $skillOptions = cocreate_merge_choice_options($skillOptions, $_POST['required_skills'] ?? []);
    $project['project_title'] = trim($_POST['project_title'] ?? '');
    $project['description'] = trim($_POST['description'] ?? '');
    $project['required_skills'] = cocreate_join_selected_options($_POST['required_skills'] ?? [], $skillOptions);
    $project['category'] = trim($_POST['category'] ?? '');
    $project['project_status'] = trim($_POST['project_status'] ?? 'open');

    if ($project['project_title'] === '') $errors[] = 'Project title is required.';
    if ($project['description'] === '') $errors[] = 'Description is required.';
    if ($project['required_skills'] === '') $errors[] = 'Required skills are required.';
    if ($project['category'] === '') $errors[] = 'Project category is required.';
    else {
        $matchedCategory = cocreate_canonical_option($project['category'], $categoryOptions);
        if ($matchedCategory === null) {
            $errors[] = 'Choose a project category from the list.';
        } else {
            $project['category'] = $matchedCategory;
        }
    }
    if (!valid_project_status($project['project_status'])) $errors[] = 'Invalid project status.';

    if (!$errors && isset($_FILES['project_image'])) {
        $imageError = validate_project_image($_FILES['project_image']);
        if ($imageError) $errors[] = $imageError;
    }

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

        $projectId = (int)$pdo->lastInsertId();
        if (isset($_FILES['project_image'])) {
            [$projectImage, $imageError] = save_project_image($_FILES['project_image'], $projectId, __DIR__ . '/..');
            if ($imageError) {
                flash('error', $imageError);
            } elseif ($projectImage) {
                $updateImage = $pdo->prepare('UPDATE projects SET project_image = ? WHERE project_id = ? AND user_id = ?');
                $updateImage->execute([$projectImage, $projectId, current_user_id()]);
            }
        }

        flash('success', 'Project created successfully.');
        header('Location: my_projects.php');
        exit;
    }
}

$pageTitle = 'Create Project';
$selectedSkills = cocreate_selected_options($project['required_skills']);
$skillOptions = cocreate_merge_choice_options($skillOptions, array_keys($selectedSkills));
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">New project</p>
    <h1>Create project</h1>
  </div>
</section>

<form class="card form-card wide" method="post" enctype="multipart/form-data" data-validate>
  <?php foreach ($errors as $error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endforeach; ?>
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <label>Project title<input required name="project_title" value="<?= e($project['project_title']) ?>"></label>
  <label>Description<textarea required name="description" rows="7"><?= e($project['description']) ?></textarea></label>
  <?php render_choice_fieldset(
      'required_skills',
      'Required skills',
      $skillOptions,
      $selectedSkills,
      'Choose all that apply.',
      'Add custom skill',
      true,
  ); ?>
  <?php render_project_category_combobox('category', $project['category'], $categoryOptions); ?>
  <label>Project status
    <select name="project_status" required>
      <option value="open" <?= $project['project_status'] === 'open' ? 'selected' : '' ?>>Open</option>
      <option value="in_progress" <?= $project['project_status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
      <option value="completed" <?= $project['project_status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>
  </label>
  <label>Project image <span class="field-hint">Optional. JPG, PNG, GIF, or WebP up to 3 MB.</span>
    <input type="file" name="project_image" accept="image/*">
  </label>
  <button class="btn btn-primary" type="submit">Save Project</button>
</form>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

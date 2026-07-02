<?php
require_once __DIR__ . "/../includes/session.php";
require_login();
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/uploads.php";

$assetPrefix = "../";
$pagePrefix = "";
$adminPrefix = "../admin/";
$skillOptions = cocreate_skill_options($pdo);
$categoryOptions = cocreate_project_category_options($pdo);

$id = (int) ($_GET["id"] ?? 0);
$stmt = $pdo->prepare(
    "SELECT * FROM projects WHERE project_id = ? AND user_id = ?",
);
$stmt->execute([$id, current_user_id()]);
$project = $stmt->fetch();

if (!$project) {

    http_response_code(404);
    $pageTitle = "Project Not Found";
    $emptyStateMessage =
        "Project not found or you do not have permission to edit it.";
    require_once __DIR__ . "/../includes/header.php";
    ?>
<div class="empty-state"><?= e($emptyStateMessage) ?></div>

<?php
require_once __DIR__ . "/../includes/footer.php";
exit();

}

$projectLinks = cocreate_project_link_rows_from_links(
    cocreate_fetch_project_links($pdo, $id),
);
$errors = [];
$categoryOptions = cocreate_merge_choice_options($categoryOptions, [
    $project["category"] ?? "",
]);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();
    $skillOptions = cocreate_merge_choice_options(
        $skillOptions,
        $_POST["required_skills"] ?? [],
    );
    $project["project_title"] = trim($_POST["project_title"] ?? "");
    $project["description"] = trim($_POST["description"] ?? "");
    $project["required_skills"] = cocreate_join_selected_options(
        $_POST["required_skills"] ?? [],
        $skillOptions,
    );
    $project["category"] = trim($_POST["category"] ?? "");
    $project["project_status"] = trim($_POST["project_status"] ?? "open");
    $projectLinks = cocreate_project_link_rows($_POST["project_links"] ?? []);
    $removeImage = ($_POST["remove_project_image"] ?? "") === "1";
    $hasNewImage =
        isset($_FILES["project_image"]) &&
        $_FILES["project_image"]["error"] !== UPLOAD_ERR_NO_FILE;

    if ($project["project_title"] === "") {
        $errors[] = "Project title is required.";
    }
    if ($project["description"] === "") {
        $errors[] = "Description is required.";
    }
    if ($project["required_skills"] === "") {
        $errors[] = "Required skills are required.";
    }
    if ($project["category"] === "") {
        $errors[] = "Project category is required.";
    }
    if (!valid_project_status($project["project_status"])) {
        $errors[] = "Invalid project status.";
    }

    [$validProjectLinks, $linkErrors] = cocreate_validate_project_links(
        $projectLinks,
    );
    $errors = array_merge($errors, $linkErrors);

    if ($hasNewImage) {
        $imageError = validate_project_image($_FILES["project_image"]);
        if ($imageError) {
            $errors[] = $imageError;
        }
    }

    if (!$errors) {
        $projectImage = $project["project_image"] ?? null;

        if ($hasNewImage) {
            [$newImage, $imageError] = save_project_image(
                $_FILES["project_image"],
                $id,
                __DIR__ . "/..",
            );
            if ($imageError) {
                $errors[] = $imageError;
            } else {
                delete_uploaded_file($projectImage, __DIR__ . "/..");
                $projectImage = $newImage;
            }
        } elseif ($removeImage) {
            delete_uploaded_file($projectImage, __DIR__ . "/..");
            $projectImage = null;
        }
    }

    if (!$errors) {
        cocreate_persist_skill_options($pdo, $_POST["required_skills"] ?? []);
        $project["category"] =
            cocreate_persist_project_category($pdo, $project["category"]) ??
            $project["category"];

        $update = $pdo->prepare(
            "UPDATE projects SET project_title = ?, description = ?, required_skills = ?, category = ?, project_status = ?, project_image = ? WHERE project_id = ? AND user_id = ?",
        );
        $update->execute([
            $project["project_title"],
            $project["description"],
            $project["required_skills"],
            $project["category"],
            $project["project_status"],
            $projectImage,
            $id,
            current_user_id(),
        ]);
        cocreate_save_project_links($pdo, $id, $validProjectLinks);
        flash("success", "Project updated successfully.");
        header("Location: my_projects.php");
        exit();
    }
}

$pageTitle = "Edit Project";
$selectedSkills = cocreate_selected_options($project["required_skills"]);
$skillOptions = cocreate_merge_choice_options(
    $skillOptions,
    array_keys($selectedSkills),
);
$categoryOptions = cocreate_merge_choice_options($categoryOptions, [
    $project["category"] ?? "",
]);
require_once __DIR__ . "/../includes/header.php";
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Edit project</p>
    <h1><?= e($project["project_title"]) ?></h1>
  </div>
</section>

<form class="card form-card wide" method="post" enctype="multipart/form-data" data-validate>
  <?php foreach ($errors as $error): ?><div class="alert alert-error"><?= e(
    $error,
) ?></div><?php endforeach; ?>
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <label>Project title<input required name="project_title" value="<?= e(
      $project["project_title"],
  ) ?>"></label>
  <label>Description<textarea required name="description" rows="7"><?= e(
      $project["description"],
  ) ?></textarea></label>
  <?php render_choice_fieldset(
      "required_skills",
      "Required skills",
      $skillOptions,
      $selectedSkills,
      "Choose all that apply.",
      "Add custom skill",
      true,
  ); ?>
  <?php render_project_category_combobox(
      "category",
      $project["category"],
      $categoryOptions,
  ); ?>
  <?php render_project_links_fields($projectLinks); ?>
  <label>Project status
    <select name="project_status" required>
      <option value="open" <?= $project["project_status"] === "open"
          ? "selected"
          : "" ?>>Open</option>
      <option value="in_progress" <?= $project["project_status"] ===
      "in_progress"
          ? "selected"
          : "" ?>>In Progress</option>
      <option value="completed" <?= $project["project_status"] === "completed"
          ? "selected"
          : "" ?>>Completed</option>
    </select>
  </label>
  <?php if (!empty($project["project_image"])): ?>
    <div class="image-preview">
      <img src="<?= e(
          $publicPrefix . $project["project_image"],
      ) ?>" alt="Current project image">
      <label class="check-row"><input type="checkbox" name="remove_project_image" value="1"> Remove current image</label>
    </div>
  <?php endif; ?>
  <label>Replace project image <span class="field-hint">Optional. JPG, PNG, GIF, or WebP up to 3 MB.</span>
    <input type="file" name="project_image" accept="image/*">
  </label>
  <button class="btn btn-primary" type="submit">Update Project</button>
</form>

<?php require_once __DIR__ . "/../includes/footer.php";
?>

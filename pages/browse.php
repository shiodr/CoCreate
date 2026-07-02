<?php
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../includes/db.php";

$assetPrefix = "../";
$pagePrefix = "";
$adminPrefix = "../admin/";

$search = trim($_GET["q"] ?? "");
$status = trim($_GET["status"] ?? "");
$skill = trim($_GET["skill"] ?? "");
$where = [];
$params = [];

if ($search !== "") {
    $where[] =
        "(p.project_title LIKE ? OR p.description LIKE ? OR p.required_skills LIKE ? OR p.category LIKE ?)";
    $term = "%" . $search . "%";
    array_push($params, $term, $term, $term, $term);
}

if ($status !== "" && valid_project_status($status)) {
    $where[] = "p.project_status = ?";
    $params[] = $status;
}

if ($skill !== "") {
    $where[] = "p.required_skills LIKE ?";
    $params[] = "%" . $skill . "%";
}

$sql =
    "SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = cocreate_attach_project_links($pdo, $stmt->fetchAll());

$pageTitle = "Browse Projects";
require_once __DIR__ . "/../includes/header.php";
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Discover</p>
    <h1>Browse projects</h1>
  </div>
  <?php if (
      is_logged_in()
  ): ?><a class="btn btn-primary" href="create_project.php">Create Project</a><?php endif; ?>
</section>

<form class="filter-bar" method="get">
  <input name="q" placeholder="Search title, category, or keywords" value="<?= e(
      $search,
  ) ?>">
  <input name="skill" placeholder="Required skill" value="<?= e($skill) ?>">
  <select name="status">
    <option value="">Any status</option>
    <option value="open" <?= $status === "open"
        ? "selected"
        : "" ?>>Open</option>
    <option value="in_progress" <?= $status === "in_progress"
        ? "selected"
        : "" ?>>In Progress</option>
    <option value="completed" <?= $status === "completed"
        ? "selected"
        : "" ?>>Completed</option>
  </select>
  <button class="btn btn-primary" type="submit">Search</button>
</form>

<div class="cards-grid">
  <?php foreach ($projects as $project): ?>
    <?php render_project_card($project, [
        "excerpt_length" => 150,
        "meta" => "By @" . $project["username"] . " in " . $project["category"],
        "show_button" => true,
    ]); ?>
  <?php endforeach; ?>
  <?php if (!$projects): ?>
    <div class="empty-state">No projects matched your search.</div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . "/../includes/footer.php";
?>

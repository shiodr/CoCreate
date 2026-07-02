<?php
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../includes/db.php";

$assetPrefix = "../";
$pagePrefix = "";
$adminPrefix = "../admin/";

$id = (int) ($_GET["id"] ?? 0);
$stmt = $pdo->prepare(
    "SELECT p.*, u.username, u.firstname, u.lastname, u.skills AS owner_skills FROM projects p JOIN users u ON u.user_id = p.user_id WHERE p.project_id = ?",
);
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {

    http_response_code(404);
    $pageTitle = "Project Not Found";
    $emptyStateMessage = "Project not found.";
    require_once __DIR__ . "/../includes/header.php";
    ?>
<div class="empty-state"><?= e($emptyStateMessage) ?></div>

<?php
require_once __DIR__ . "/../includes/footer.php";
exit();

}

$projectLinks = cocreate_fetch_project_links($pdo, $id);
$isOwner = is_logged_in() && current_user_id() === (int) $project["user_id"];
$existingRequest = null;

if (is_logged_in() && !$isOwner) {
    $check = $pdo->prepare(
        "SELECT * FROM join_requests WHERE project_id = ? AND user_id = ?",
    );
    $check->execute([$id, current_user_id()]);
    $existingRequest = $check->fetch();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_login();
    check_csrf();
    if ($isOwner) {
        flash("error", "You cannot request to join your own project.");
    } elseif ($existingRequest) {
        flash("error", "You already submitted a request for this project.");
    } else {
        $message = trim($_POST["message"] ?? "");
        $insert = $pdo->prepare(
            "INSERT INTO join_requests (project_id, user_id, message) VALUES (?, ?, ?)",
        );
        $insert->execute([$id, current_user_id(), $message]);
        flash("success", "Join request submitted successfully.");
    }
    header("Location: project.php?id=" . $id);
    exit();
}

$pageTitle = $project["project_title"];
require_once __DIR__ . "/../includes/header.php";
?>
<article class="detail-layout">
  <section class="card detail-card">
    <?php render_project_media($project, "detail-image"); ?>
    <div class="card-meta">
      <span class="status status-<?= e($project["project_status"]) ?>"><?= e(
    status_label($project["project_status"]),
) ?></span>
      <span><?= e(date("M j, Y", strtotime($project["created_at"]))) ?></span>
    </div>
    <h1><?= e($project["project_title"]) ?></h1>
    <p class="muted">By <?= e(
        $project["firstname"] . " " . $project["lastname"],
    ) ?> (@<?= e($project["username"]) ?>)</p>
    <p><?= nl2br(e($project["description"])) ?></p>
    <h3>Required skills</h3>
    <?php render_skill_tags($project["required_skills"]); ?>
    <p><strong>Category:</strong> <?= e($project["category"]) ?></p>
    <?php if ($projectLinks): ?>
      <h3>Project links</h3>
      <div class="project-link-stack">
        <?php foreach ($projectLinks as $link): ?>
          <a class="project-link-chip" href="<?= e(
              $link["link_url"],
          ) ?>" target="_blank" rel="noopener noreferrer"><?= e(
    $link["link_label"],
) ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <aside class="card action-card">
    <?php if (!is_logged_in()): ?>
      <h2>Interested?</h2>
      <p class="muted">Login to request to join this project.</p>
      <a class="btn btn-primary full" href="login.php">Login</a>
    <?php elseif ($isOwner): ?>
      <h2>Your project</h2>
      <p class="muted">You can edit this listing or manage incoming requests.</p>
      <a class="btn btn-primary full" href="edit_project.php?id=<?= (int) $project[
          "project_id"
      ] ?>">Edit Project</a>
      <a class="btn btn-secondary full" href="my_projects.php">Manage Requests</a>
    <?php elseif ($existingRequest): ?>
      <h2>Request sent</h2>
      <p>Status: <span class="status status-<?= e(
          $existingRequest["request_status"],
      ) ?>"><?= e(
    status_label($existingRequest["request_status"]),
) ?></span></p>
    <?php else: ?>
      <h2>Request to Join</h2>
      <form method="post" data-validate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label>Message<textarea name="message" rows="5" placeholder="Tell the project owner why you are interested"></textarea></label>
        <button class="btn btn-primary full" type="submit">Request to Join</button>
      </form>
    <?php endif; ?>
  </aside>
</article>

<?php require_once __DIR__ . "/../includes/footer.php";
?>

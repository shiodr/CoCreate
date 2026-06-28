<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$search = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$skill = trim($_GET['skill'] ?? '');
$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(p.project_title LIKE ? OR p.description LIKE ? OR p.required_skills LIKE ? OR p.category LIKE ?)';
    $term = '%' . $search . '%';
    array_push($params, $term, $term, $term, $term);
}

if ($status !== '' && valid_project_status($status)) {
    $where[] = 'p.project_status = ?';
    $params[] = $status;
}

if ($skill !== '') {
    $where[] = 'p.required_skills LIKE ?';
    $params[] = '%' . $skill . '%';
}

$sql = 'SELECT p.*, u.username FROM projects p JOIN users u ON u.user_id = p.user_id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$pageTitle = 'Browse Projects';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Discover</p>
    <h1>Browse projects</h1>
  </div>
  <?php if (is_logged_in()): ?><a class="btn btn-primary" href="create_project.php">Create Project</a><?php endif; ?>
</section>

<form class="filter-bar" method="get">
  <input name="q" placeholder="Search title, category, or keywords" value="<?= e($search) ?>">
  <input name="skill" placeholder="Required skill" value="<?= e($skill) ?>">
  <select name="status">
    <option value="">Any status</option>
    <option value="open" <?= $status === 'open' ? 'selected' : '' ?>>Open</option>
    <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
    <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
  </select>
  <button class="btn btn-primary" type="submit">Search</button>
</form>

<div class="cards-grid">
  <?php foreach ($projects as $project): ?>
    <article class="card project-card">
      <div class="card-meta">
        <span class="status status-<?= e($project['project_status']) ?>"><?= e(status_label($project['project_status'])) ?></span>
        <span><?= e(date('M j, Y', strtotime($project['created_at']))) ?></span>
      </div>
      <h3><?= e($project['project_title']) ?></h3>
      <p><?= e(excerpt($project['description'], 150)) ?></p>
      <p class="muted">By @<?= e($project['username']) ?> in <?= e($project['category']) ?></p>
      <div class="tag-row">
        <?php foreach (array_filter(array_map('trim', explode(',', $project['required_skills']))) as $item): ?>
          <span class="tag"><?= e($item) ?></span>
        <?php endforeach; ?>
      </div>
      <a class="btn btn-secondary full" href="project.php?id=<?= (int)$project['project_id'] ?>">View Details</a>
    </article>
  <?php endforeach; ?>
  <?php if (!$projects): ?>
    <div class="empty-state">No projects matched your search.</div>
  <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

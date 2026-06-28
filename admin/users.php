<?php
require_once __DIR__ . '/../includes/session.php';
require_admin();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '../pages/';
$adminPrefix = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $userId = (int)($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($userId === current_user_id()) {
        flash('error', 'You cannot modify your own account here.');
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$userId]);
        flash('success', 'User deleted.');
    } elseif ($action === 'disable') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'disabled' WHERE user_id = ?");
        $stmt->execute([$userId]);
        flash('success', 'User disabled.');
    } elseif ($action === 'enable') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $stmt->execute([$userId]);
        flash('success', 'User enabled.');
    }

    header('Location: users.php');
    exit;
}

$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="page-head">
  <div>
    <p class="eyebrow">Admin</p>
    <h1>Manage users</h1>
  </div>
</section>

<div class="table-wrap">
  <table>
    <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $user): ?>
      <tr>
        <td><?= e($user['firstname'] . ' ' . $user['lastname']) ?></td>
        <td>@<?= e($user['username']) ?></td>
        <td><?= e($user['email']) ?></td>
        <td><?= e($user['role']) ?></td>
        <td><span class="status status-<?= $user['status'] === 'active' ? 'accepted' : 'rejected' ?>"><?= e($user['status']) ?></span></td>
        <td class="muted"><?= e(date('M j, Y', strtotime($user['created_at']))) ?></td>
        <td>
          <form class="inline-form" method="post">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="user_id" value="<?= (int)$user['user_id'] ?>">
            <?php if ($user['status'] === 'active'): ?>
              <button class="btn btn-ghost" name="action" value="disable">Disable</button>
            <?php else: ?>
              <button class="btn btn-secondary" name="action" value="enable">Enable</button>
            <?php endif; ?>
          </form>
          <form class="inline-form" method="post" data-confirm="Delete this account? This cannot be undone.">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="user_id" value="<?= (int)$user['user_id'] ?>">
            <button class="btn btn-danger" name="action" value="delete">Delete</button>
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

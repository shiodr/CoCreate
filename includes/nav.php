<?php
$root = $assetPrefix ?? '';
$current = basename($_SERVER['PHP_SELF'] ?? '');
?>
<header class="site-header">
  <nav class="nav container">
    <a class="brand" href="<?= e($root) ?>index.php">CoCreate</a>
    <button class="nav-toggle" type="button" aria-label="Open navigation" data-nav-toggle>
      <span></span><span></span><span></span>
    </button>
    <div class="nav-links" data-nav-links>
      <?php if (is_logged_in()): ?>
        <a class="<?= $current === 'dashboard.php' ? 'active' : '' ?>" href="<?= e($root) ?>dashboard.php">Dashboard</a>
        <a class="<?= $current === 'browse.php' ? 'active' : '' ?>" href="<?= e($root) ?>browse.php">Browse</a>
        <a class="<?= $current === 'create_project.php' ? 'active' : '' ?>" href="<?= e($root) ?>create_project.php">Create</a>
        <a class="<?= $current === 'my_projects.php' ? 'active' : '' ?>" href="<?= e($root) ?>my_projects.php">My Projects</a>
        <a class="<?= $current === 'my_requests.php' ? 'active' : '' ?>" href="<?= e($root) ?>my_requests.php">Requests</a>
        <a class="<?= $current === 'profile.php' ? 'active' : '' ?>" href="<?= e($root) ?>profile.php">Profile</a>
        <?php if (is_admin()): ?>
          <a href="<?= e($root) ?>admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <a class="nav-button" href="<?= e($root) ?>logout.php">Logout</a>
      <?php else: ?>
        <a class="<?= $current === 'browse.php' ? 'active' : '' ?>" href="<?= e($root) ?>browse.php">Browse</a>
        <a class="<?= $current === 'login.php' ? 'active' : '' ?>" href="<?= e($root) ?>login.php">Login</a>
        <a class="nav-button" href="<?= e($root) ?>register.php">Register</a>
      <?php endif; ?>
    </div>
  </nav>
</header>

<?php
$pageRoot = $pagePrefix ?? '';
$adminRoot = $adminPrefix ?? 'admin/';
$current = basename($_SERVER['PHP_SELF'] ?? '');
?>
<header class="site-header">
  <nav class="nav container">
    <a class="brand" href="<?= e($pageRoot) ?>index.php">CoCreate</a>
    <button class="theme-toggle" type="button" aria-label="Switch to dark mode" data-theme-toggle>
      <span class="theme-toggle-icon" aria-hidden="true"></span>
    </button>
    <button class="nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" data-nav-toggle>
      <span></span><span></span><span></span>
    </button>
    <div class="nav-links" data-nav-links>
      <?php if (is_logged_in()): ?>
        <a class="<?= $current === 'dashboard.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>dashboard.php">Dashboard</a>
        <a class="<?= $current === 'browse.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>browse.php">Browse</a>
        <a class="<?= $current === 'create_project.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>create_project.php">Create</a>
        <a class="<?= $current === 'my_projects.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>my_projects.php">My Projects</a>
        <a class="<?= $current === 'my_requests.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>my_requests.php">Requests</a>
        <a class="<?= $current === 'profile.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>profile.php">Profile</a>
        <?php if (is_admin()): ?>
          <a href="<?= e($adminRoot) ?>dashboard.php">Admin</a>
        <?php endif; ?>
        <a class="nav-button" href="<?= e($pageRoot) ?>logout.php">Logout</a>
      <?php else: ?>
        <a class="<?= $current === 'browse.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>browse.php">Browse</a>
        <a class="<?= $current === 'login.php' ? 'active' : '' ?>" href="<?= e($pageRoot) ?>login.php">Login</a>
        <a class="nav-button" href="<?= e($pageRoot) ?>register.php">Register</a>
      <?php endif; ?>
    </div>
  </nav>
</header>

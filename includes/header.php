<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/components.php';
$publicPrefix = cocreate_public_prefix($assetPrefix ?? '');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | CoCreate' : 'CoCreate' ?></title>
  <script>
    (() => {
      let saved = null;
      try {
        saved = localStorage.getItem('cocreate-theme');
      } catch (error) {}
      const systemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
      document.documentElement.dataset.theme = saved || (systemDark ? 'dark' : 'light');
    })();
  </script>
  <link rel="stylesheet" href="<?= e($publicPrefix) ?>assets/css/styles.css">
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<main class="container page-shell">
  <?php if ($message = flash('success')): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
  <?php endif; ?>
  <?php if ($message = flash('error')): ?>
    <div class="alert alert-error"><?= e($message) ?></div>
  <?php endif; ?>

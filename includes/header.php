<?php require_once __DIR__ . '/session.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | CoCreate' : 'CoCreate' ?></title>
  <link rel="stylesheet" href="<?= e($assetPrefix ?? '') ?>assets/css/styles.css">
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

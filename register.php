<?php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$old = [
    'firstname' => '',
    'lastname' => '',
    'username' => '',
    'email' => '',
    'skills' => '',
    'interests' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    foreach ($old as $key => $value) {
        $old[$key] = trim($_POST[$key] ?? '');
    }
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($old['firstname'] === '') $errors[] = 'First name is required.';
    if ($old['lastname'] === '') $errors[] = 'Last name is required.';
    if ($old['username'] === '') $errors[] = 'Username is required.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$old['username'], $old['email']]);
        if ((int)$stmt->fetchColumn() > 0) {
            $errors[] = 'Username or email is already registered.';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO users (firstname, lastname, username, email, password_hash, skills, interests) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $old['firstname'],
            $old['lastname'],
            $old['username'],
            $old['email'],
            password_hash($password, PASSWORD_DEFAULT),
            $old['skills'],
            $old['interests'],
        ]);
        flash('success', 'Account Created Successfully. You can now log in.');
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>
<div class="form-layout">
  <section>
    <p class="eyebrow">Join CoCreate</p>
    <h1>Create your account</h1>
    <p class="muted">Build a profile that shows your skills, interests, and what kind of teammates you want to meet.</p>
  </section>
  <form class="card form-card" method="post" data-validate>
    <?php foreach ($errors as $error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endforeach; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="two-col">
      <label>First name<input required name="firstname" value="<?= e($old['firstname']) ?>"></label>
      <label>Last name<input required name="lastname" value="<?= e($old['lastname']) ?>"></label>
    </div>
    <label>Username<input required name="username" value="<?= e($old['username']) ?>"></label>
    <label>Email<input required type="email" name="email" value="<?= e($old['email']) ?>"></label>
    <div class="two-col">
      <label>Password<input required minlength="8" type="password" name="password"></label>
      <label>Confirm password<input required minlength="8" type="password" name="confirm_password"></label>
    </div>
    <label>Skills<textarea name="skills" rows="3" placeholder="PHP, design, writing"><?= e($old['skills']) ?></textarea></label>
    <label>Interests<textarea name="interests" rows="3" placeholder="Web apps, music, games"><?= e($old['interests']) ?></textarea></label>
    <button class="btn btn-primary full" type="submit">Create Account</button>
    <p class="form-note">Already have an account? <a href="login.php">Login here</a>.</p>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

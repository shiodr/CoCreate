<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$error = '';
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $error = 'Enter your username or email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect login credentials.';
        } elseif (($user['status'] ?? 'active') !== 'active') {
            $error = 'This account is disabled. Please contact an administrator.';
        } else {
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['role'] = $user['role'];
            flash('success', 'Welcome back, ' . $user['firstname'] . '.');
            header('Location: ' . ($user['role'] === 'admin' ? '../admin/dashboard.php' : 'dashboard.php'));
            exit;
        }
    }
}

$pageTitle = 'Login';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="form-layout compact">
  <section>
    <p class="eyebrow">Welcome back</p>
    <h1>Login to CoCreate</h1>
    <p class="muted">Continue browsing projects, managing requests, and finding collaborators.</p>
  </section>
  <form class="card form-card" method="post" data-validate>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Username or email<input required name="login" value="<?= e($login) ?>"></label>
    <label>Password<input required type="password" name="password"></label>
    <button class="btn btn-primary full" type="submit">Login</button>
    <p class="form-note">No account yet? <a href="register.php">Register here</a>.</p>
  </form>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

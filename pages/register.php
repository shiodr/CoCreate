<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

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
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/register.html';
require_once __DIR__ . '/../includes/footer.php';
?>

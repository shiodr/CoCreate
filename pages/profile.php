<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';
$uploadPrefix = '../';

$uid = current_user_id();
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$uid]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $user['firstname'] = trim($_POST['firstname'] ?? '');
    $user['lastname'] = trim($_POST['lastname'] ?? '');
    $user['username'] = trim($_POST['username'] ?? '');
    $user['email'] = trim($_POST['email'] ?? '');
    $user['skills'] = trim($_POST['skills'] ?? '');
    $user['interests'] = trim($_POST['interests'] ?? '');
    $user['bio'] = trim($_POST['bio'] ?? '');

    if ($user['firstname'] === '') $errors[] = 'First name is required.';
    if ($user['lastname'] === '') $errors[] = 'Last name is required.';
    if ($user['username'] === '') $errors[] = 'Username is required.';
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';

    if (!$errors) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND user_id <> ?');
        $check->execute([$user['username'], $user['email'], $uid]);
        if ((int)$check->fetchColumn() > 0) {
            $errors[] = 'Username or email is already used by another account.';
        }
    }

    $profilePicture = $user['profile_picture'];
    if (!$errors && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Profile picture upload failed.';
        } else {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowed, true)) {
                $errors[] = 'Profile picture must be an image file.';
            } else {
                $profilePicture = 'uploads/profile_' . $uid . '_' . time() . '.' . $extension;
                if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], __DIR__ . '/../' . $profilePicture)) {
                    $errors[] = 'Could not save the profile picture.';
                }
            }
        }
    }

    if (!$errors) {
        $update = $pdo->prepare('UPDATE users SET firstname = ?, lastname = ?, username = ?, email = ?, profile_picture = ?, skills = ?, interests = ?, bio = ? WHERE user_id = ?');
        $update->execute([
            $user['firstname'],
            $user['lastname'],
            $user['username'],
            $user['email'],
            $profilePicture,
            $user['skills'],
            $user['interests'],
            $user['bio'],
            $uid,
        ]);
        $_SESSION['firstname'] = $user['firstname'];
        flash('success', 'Profile updated successfully.');
        header('Location: profile.php');
        exit;
    }
}

$pageTitle = 'Profile';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/profile.html';
require_once __DIR__ . '/../includes/footer.php';
?>

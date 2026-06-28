<?php
require_once __DIR__ . '/../includes/session.php';
require_login();
require_once __DIR__ . '/../includes/db.php';

$assetPrefix = '../';
$pagePrefix = '';
$adminPrefix = '../admin/';

$errors = [];
$project = [
    'project_title' => '',
    'description' => '',
    'required_skills' => '',
    'category' => '',
    'project_status' => 'open',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    foreach ($project as $key => $value) {
        $project[$key] = trim($_POST[$key] ?? $value);
    }

    if ($project['project_title'] === '') $errors[] = 'Project title is required.';
    if ($project['description'] === '') $errors[] = 'Description is required.';
    if ($project['required_skills'] === '') $errors[] = 'Required skills are required.';
    if ($project['category'] === '') $errors[] = 'Project category is required.';
    if (!valid_project_status($project['project_status'])) $errors[] = 'Invalid project status.';

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO projects (user_id, project_title, description, required_skills, category, project_status) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            current_user_id(),
            $project['project_title'],
            $project['description'],
            $project['required_skills'],
            $project['category'],
            $project['project_status'],
        ]);
        flash('success', 'Project created successfully.');
        header('Location: my_projects.php');
        exit;
    }
}

$pageTitle = 'Create Project';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../views/create_project.html';
require_once __DIR__ . '/../includes/footer.php';
?>

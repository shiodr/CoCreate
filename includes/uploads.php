<?php

function validate_project_image(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return 'Project image upload failed.';
    }

    if (($file['size'] ?? 0) > 3 * 1024 * 1024) {
        return 'Project image must be 3 MB or smaller.';
    }

    $allowed = [
        'jpg' => 'jpg',
        'jpeg' => 'jpg',
        'png' => 'png',
        'gif' => 'gif',
        'webp' => 'webp',
    ];

    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!isset($allowed[$extension])) {
        return 'Project image must be a JPG, PNG, GIF, or WebP file.';
    }

    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return 'Project image must be a valid image file.';
    }

    return null;
}

function save_project_image(array $file, int $projectId, string $rootDir): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }

    $validationError = validate_project_image($file);
    if ($validationError) {
        return [null, $validationError];
    }

    $allowed = [
        'jpg' => 'jpg',
        'jpeg' => 'jpg',
        'png' => 'png',
        'gif' => 'gif',
        'webp' => 'webp',
    ];

    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if ($projectId < 1) {
        return [null, 'Project image could not be linked to this project.'];
    }

    $uploadDir = rtrim($rootDir, '/') . '/uploads/projects';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        return [null, 'Could not create the project image folder.'];
    }

    $filename = 'project_' . $projectId . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$extension];
    $relativePath = 'uploads/projects/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], rtrim($rootDir, '/') . '/' . $relativePath)) {
        return [null, 'Could not save the project image.'];
    }

    return [$relativePath, null];
}

function delete_uploaded_file(?string $relativePath, string $rootDir): void
{
    if (!$relativePath) {
        return;
    }

    $relativePath = ltrim($relativePath, '/');
    if (substr($relativePath, 0, 8) !== 'uploads/') {
        return;
    }

    $fullPath = rtrim($rootDir, '/') . '/' . $relativePath;
    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}

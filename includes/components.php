<?php

function split_skill_list(?string $skills): array
{
    return array_filter(array_map('trim', explode(',', (string)$skills)));
}

function cocreate_public_prefix(?string $preferred = null): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $insideControllerDir = str_contains($path, '/pages/') || str_contains($path, '/admin/');
    if ($insideControllerDir) {
        return $preferred ?? '../';
    }

    if ($preferred === '../') {
        return '';
    }

    return $preferred ?? '';
}

function project_initial(array $project): string
{
    $title = trim((string)($project['project_title'] ?? 'P'));
    if ($title === '') {
        return 'P';
    }

    return strtoupper(function_exists('mb_substr') ? mb_substr($title, 0, 1, 'UTF-8') : substr($title, 0, 1));
}

function render_skill_tags(?string $skills): void
{
    echo '<div class="tag-row">';
    foreach (split_skill_list($skills) as $skill) {
        echo '<span class="tag">' . e($skill) . '</span>';
    }
    echo '</div>';
}

function render_project_media(array $project, string $class = 'project-image'): void
{
    $prefix = $GLOBALS['uploadPrefix'] ?? cocreate_public_prefix('../');
    $title = (string)($project['project_title'] ?? 'Project');
    $category = (string)($project['category'] ?? 'Project');

    if (!empty($project['project_image'])) {
        echo '<img class="' . e($class) . '" src="' . e($prefix . $project['project_image']) . '" alt="' . e($title) . '">';
        return;
    }

    echo '<span class="' . e($class . ' project-image-placeholder') . '" data-category="' . e($category) . '"><strong>' . e(project_initial($project)) . '</strong></span>';
}

function render_project_card(array $project, array $options = []): void
{
    $id = (int)($project['project_id'] ?? 0);
    $title = (string)($project['project_title'] ?? 'Untitled project');
    $description = (string)($project['description'] ?? '');
    $href = $options['href'] ?? 'project.php?id=' . $id;
    $statusKey = $options['status_key'] ?? 'project_status';
    $status = (string)($project[$statusKey] ?? 'open');
    $dateKey = $options['date_key'] ?? 'created_at';
    $date = !empty($project[$dateKey]) ? date('M j, Y', strtotime((string)$project[$dateKey])) : '';
    $excerptLength = (int)($options['excerpt_length'] ?? 140);
    $showSkills = (bool)($options['show_skills'] ?? true);
    $showButton = (bool)($options['show_button'] ?? false);
    $buttonText = (string)($options['button_text'] ?? 'View Details');
    $buttonClass = (string)($options['button_class'] ?? 'btn btn-secondary full');
    $meta = (string)($options['meta'] ?? '');
    $footerHtml = $options['footer_html'] ?? '';

    echo '<article class="card project-card" data-reveal>';
    echo '<a class="project-image-link" href="' . e($href) . '">';
    render_project_media($project);
    echo '</a>';
    echo '<div class="card-meta">';
    echo '<span class="status status-' . e($status) . '">' . e(status_label($status)) . '</span>';
    if ($date !== '') {
        echo '<span>' . e($date) . '</span>';
    }
    echo '</div>';
    echo '<h3><a href="' . e($href) . '">' . e($title) . '</a></h3>';
    if ($description !== '') {
        echo '<p>' . e(excerpt($description, $excerptLength)) . '</p>';
    }
    if ($meta !== '') {
        echo '<p class="muted">' . e($meta) . '</p>';
    }
    if ($showSkills) {
        render_skill_tags($project['required_skills'] ?? '');
    }
    if ($footerHtml !== '') {
        echo $footerHtml;
    }
    if ($showButton) {
        echo '<a class="' . e($buttonClass) . '" href="' . e($href) . '">' . e($buttonText) . '</a>';
    }
    echo '</article>';
}

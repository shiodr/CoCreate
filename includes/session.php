<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION["user_id"]);
}

function is_admin(): bool
{
    return isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
}

function require_login(): void
{
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        header("Location: ../pages/login.php");
        exit();
    }
}

function current_user_id(): ?int
{
    return isset($_SESSION["user_id"]) ? (int) $_SESSION["user_id"] : null;
}

function flash(string $key, ?string $message = null)
{
    if ($message === null) {
        $value = $_SESSION["flash"][$key] ?? null;
        unset($_SESSION["flash"][$key]);
        return $value;
    }

    $_SESSION["flash"][$key] = $message;
    return null;
}

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ""), ENT_QUOTES, "UTF-8");
}

function csrf_token(): string
{
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf"];
}

function check_csrf(): void
{
    if (($_POST["csrf"] ?? "") !== ($_SESSION["csrf"] ?? "")) {
        http_response_code(400);
        die("Invalid CSRF token.");
    }
}

function valid_project_status(string $status): bool
{
    return in_array($status, ["open", "in_progress", "completed"], true);
}

function status_label(string $status): string
{
    return ucwords(str_replace("_", " ", $status));
}

function excerpt(?string $text, int $length = 140): string
{
    $text = trim((string) $text);
    if (function_exists("mb_strimwidth")) {
        return mb_strimwidth($text, 0, $length, "...", "UTF-8");
    }

    return strlen($text) > $length
        ? substr($text, 0, $length - 3) . "..."
        : $text;
}

function split_skill_list(?string $skills): array
{
    return array_filter(array_map("trim", explode(",", (string) $skills)));
}

function cocreate_trim_fields(array $source, array $keys): array
{
    $values = [];
    foreach ($keys as $key) {
        $values[$key] = trim($source[$key] ?? "");
    }

    return $values;
}

function cocreate_validate_identity_fields(array $values): array
{
    $errors = [];

    if (($values["firstname"] ?? "") === "") {
        $errors[] = "First name is required.";
    }
    if (($values["lastname"] ?? "") === "") {
        $errors[] = "Last name is required.";
    }
    if (($values["username"] ?? "") === "") {
        $errors[] = "Username is required.";
    }
    if (!filter_var($values["email"] ?? "", FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }

    return $errors;
}

function cocreate_skill_options(PDO $pdo): array
{
    $fallback = [
        "PHP",
        "MySQL",
        "JavaScript",
        "CSS",
        "UI design",
        "Figma",
        "Branding",
        "Copywriting",
        "UX research",
        "Video editing",
        "Writing",
        "Sound design",
        "Art direction",
        "Moderation",
    ];

    try {
        $options = $pdo
            ->query("SELECT skill_name FROM skills ORDER BY skill_name")
            ->fetchAll(PDO::FETCH_COLUMN);
        return $options ?: $fallback;
    } catch (PDOException $e) {
        return $fallback;
    }
}

function cocreate_interest_options(): array
{
    return [
        "Web apps",
        "Open source",
        "Campus tools",
        "Creative tools",
        "Community building",
        "Short films",
        "Music videos",
        "Games",
        "Education",
        "Social impact",
    ];
}

function cocreate_project_category_options(): array
{
    return [
        "Web App",
        "Mobile App",
        "Open Source",
        "Campus Tool",
        "Community Tool",
        "Creative Media",
        "Design System",
        "Game",
        "Education",
        "Social Impact",
        "Research",
        "Event",
        "Art",
        "Music",
        "Short Film",
        "Music Video",
        "Writing",
    ];
}

function cocreate_option_exists(string $value, array $options): bool
{
    return cocreate_canonical_option($value, $options) !== null;
}

function cocreate_canonical_option(string $value, array $options): ?string
{
    $value = trim($value);
    foreach ($options as $option) {
        if (strcasecmp($value, trim((string) $option)) === 0) {
            return (string) $option;
        }
    }

    return null;
}

function cocreate_merge_choice_options(array $options, array $extra = []): array
{
    $merged = [];
    $seen = [];

    foreach (array_merge($options, $extra) as $option) {
        $option = trim((string) $option);
        if ($option === "") {
            continue;
        }

        $key = function_exists("mb_strtolower")
            ? mb_strtolower($option, "UTF-8")
            : strtolower($option);

        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $merged[] = $option;
    }

    return $merged;
}

function cocreate_join_selected_options($posted, array $allowed): string
{
    if (!is_array($posted)) {
        $posted = [$posted];
    }

    $posted = array_map("trim", $posted);
    return implode(", ", array_values(array_intersect($allowed, $posted)));
}

function cocreate_selected_options(?string $value): array
{
    return array_flip(split_skill_list($value));
}

function render_choice_fieldset(
    string $name,
    string $legend,
    array $options,
    array $selected,
    string $hint = "Choose all that apply.",
    string $customPlaceholder = "Add custom option",
    bool $required = false
): void {
    echo '<fieldset class="choice-group" data-choice-fieldset data-choice-name="' .
        e($name) .
        '"' .
        ($required ? ' data-choice-required="true"' : "") .
        ">";
    echo "<legend>" . e($legend) . "</legend>";
    echo '<p class="field-hint">' . e($hint) . "</p>";
    echo '<div class="choice-grid" data-choice-grid>';
    $selectedLookup = [];
    foreach (array_keys($selected) as $selectedOption) {
        $selectedKey = function_exists("mb_strtolower")
            ? mb_strtolower($selectedOption, "UTF-8")
            : strtolower($selectedOption);
        $selectedLookup[$selectedKey] = true;
    }
    foreach ($options as $option) {
        $optionKey = function_exists("mb_strtolower")
            ? mb_strtolower($option, "UTF-8")
            : strtolower($option);
        echo '<label class="choice-pill">';
        echo '<input type="checkbox" name="' .
            e($name) .
            '[]" value="' .
            e($option) .
            '"' .
            (isset($selectedLookup[$optionKey]) ? " checked" : "") .
            ">";
        echo "<span>" . e($option) . "</span>";
        echo "</label>";
    }
    echo "</div>";
    echo '<div class="choice-adder">';
    echo '<input class="choice-input" type="text" data-choice-input placeholder="' .
        e($customPlaceholder) .
        '" aria-label="' .
        e($customPlaceholder) .
        '" hidden>';
    echo '<button class="choice-add-button" type="button" data-choice-add aria-expanded="false" aria-label="' .
        e($customPlaceholder) .
        '"><span aria-hidden="true">+</span><span data-choice-add-label>Add</span></button>';
    echo "</div>";
    echo "</fieldset>";
}

function render_project_category_combobox(
    string $name,
    string $value,
    array $options,
    string $label = "Category Type"
): void {
    $inputId = $name . "-combobox";
    $listId = $name . "-options";

    echo '<div class="combobox-field">';
    echo '<label for="' . e($inputId) . '">' . e($label) . "</label>";
    echo '<span class="field-hint">Type to search, then choose a category.</span>';
    echo '<div class="combobox" data-combobox>';
    echo '<input id="' .
        e($inputId) .
        '" required name="' .
        e($name) .
        '" value="' .
        e($value) .
        '" placeholder="Search categories" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-controls="' .
        e($listId) .
        '" data-combobox-input>';
    echo '<button class="combobox-toggle" type="button" data-combobox-toggle aria-label="Show categories" aria-controls="' .
        e($listId) .
        '"></button>';
    echo '<div class="combobox-list" id="' .
        e($listId) .
        '" role="listbox" data-combobox-list hidden>';
    foreach ($options as $option) {
        echo '<button type="button" role="option" class="combobox-option" data-combobox-option data-value="' .
            e($option) .
            '">' .
            e($option) .
            "</button>";
    }
    echo '<p class="combobox-empty" data-combobox-empty hidden>No matching categories</p>';
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

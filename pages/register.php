<?php
require_once __DIR__ . "/../includes/session.php";
require_once __DIR__ . "/../includes/db.php";

$assetPrefix = "../";
$pagePrefix = "";
$adminPrefix = "../admin/";

$errors = [];
$skillOptions = cocreate_skill_options($pdo);
$interestOptions = cocreate_interest_options();
$old = [
    "firstname" => "",
    "lastname" => "",
    "username" => "",
    "email" => "",
    "skills" => "",
    "interests" => "",
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();
    $old = array_merge(
        $old,
        cocreate_trim_fields($_POST, [
            "firstname",
            "lastname",
            "username",
            "email",
        ]),
    );
    $old["skills"] = cocreate_join_selected_options(
        $_POST["skills"] ?? [],
        $skillOptions,
    );
    $old["interests"] = cocreate_join_selected_options(
        $_POST["interests"] ?? [],
        $interestOptions,
    );
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    $errors = array_merge($errors, cocreate_validate_identity_fields($old));
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM users WHERE username = ? OR email = ?",
        );
        $stmt->execute([$old["username"], $old["email"]]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = "Username or email is already registered.";
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO users (firstname, lastname, username, email, password_hash, skills, interests) VALUES (?, ?, ?, ?, ?, ?, ?)",
        );
        $stmt->execute([
            $old["firstname"],
            $old["lastname"],
            $old["username"],
            $old["email"],
            password_hash($password, PASSWORD_DEFAULT),
            $old["skills"],
            $old["interests"],
        ]);
        flash("success", "Account Created Successfully. You can now log in.");
        header("Location: login.php");
        exit();
    }
}

$pageTitle = "Register";
$selectedSkills = cocreate_selected_options($old["skills"]);
$selectedInterests = cocreate_selected_options($old["interests"]);
require_once __DIR__ . "/../includes/header.php";
?>
<div class="form-layout">
  <section>
    <p class="eyebrow">Join CoCreate</p>
    <h1>Create your account</h1>
    <p class="muted">Build a profile that shows your skills, interests, and what kind of teammates you want to meet.</p>
  </section>
  <form class="card form-card" method="post" data-validate>
    <?php foreach ($errors as $error): ?><div class="alert alert-error"><?= e(
    $error,
) ?></div><?php endforeach; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="two-col">
      <label>First name<input required name="firstname" value="<?= e(
          $old["firstname"],
      ) ?>"></label>
      <label>Last name<input required name="lastname" value="<?= e(
          $old["lastname"],
      ) ?>"></label>
    </div>
    <label>Username<input required name="username" value="<?= e(
        $old["username"],
    ) ?>"></label>
    <label>Email<input required type="email" name="email" value="<?= e(
        $old["email"],
    ) ?>"></label>
    <div class="two-col">
      <label>Password<input required minlength="8" type="password" name="password"></label>
      <label>Confirm password<input required minlength="8" type="password" name="confirm_password"></label>
    </div>
    <?php render_choice_fieldset(
        "skills",
        "Skills",
        $skillOptions,
        $selectedSkills,
    ); ?>
    <?php render_choice_fieldset(
        "interests",
        "Interests",
        $interestOptions,
        $selectedInterests,
    ); ?>
    <button class="btn btn-primary full" type="submit">Create Account</button>
    <p class="form-note">Already have an account? <a href="login.php">Login here</a>.</p>
  </form>
</div>

<?php require_once __DIR__ . "/../includes/footer.php";
?>

<?php
require_once __DIR__ . "/auth.php";

if (!empty($_SESSION["authenticated"])) {
  header("Location: /assessment_beginner/index.php");
  exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"] ?? "";
  $password = $_POST["password"] ?? "";

  if (is_string($username) && is_string($password)
      && hash_equals("admin", $username) && hash_equals("admin", $password)) {
    session_regenerate_id(true);
    $_SESSION["authenticated"] = true;
    header("Location: /assessment_beginner/index.php");
    exit;
  }

  $error = "Invalid username or password.";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Assessment</title>
  <link rel="stylesheet" href="/assessment_beginner/style.css">
</head>
<body>
  <div class="login-shell">
    <div class="auth-card">
      <div class="auth-header">
        <h2>Welcome back</h2>
        <p>Sign in to continue to your dashboard</p>
      </div>

      <?php if ($error !== "") { ?>
        <p class="form-error" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?></p>
      <?php } ?>

      <form method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" autocomplete="username" placeholder="admin" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" autocomplete="current-password" placeholder="Enter password" required>
        </div>

        <div class="form-actions">
          <button type="submit">Log in</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
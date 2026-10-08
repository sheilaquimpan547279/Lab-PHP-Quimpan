<?php
require_once __DIR__ . "/../db.php";

$message = "";
$messageType = "error";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save"])) {
  $full_name = trim($_POST["full_name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $phone = trim($_POST["phone"] ?? "");
  $address = trim($_POST["address"] ?? "");

  if ($full_name === "" || $email === "") {
    $message = "Name and Email are required!";
  } else {
    $stmt = mysqli_prepare($conn, "INSERT INTO clients (full_name, email, phone, address) VALUES (?, ?, ?, ?)");

    if ($stmt === false) {
      $message = "Database error while preparing the insert query.";
    } else {
      mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $phone, $address);

      if (mysqli_stmt_execute($stmt)) {
        header("Location: clients_list.php");
        exit;
      }

      $message = "Failed to save the client. Please try again.";
      $messageType = "error";
      mysqli_stmt_close($stmt);
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Client</title>
  <link rel="stylesheet" href="/assessment_beginner/style.css">
</head>
<body>
<?php include "../nav.php"; ?>

<div class="container">
  <header class="page-header">
    <h1>Add Client</h1>
  </header>

  <?php if ($message !== "") { ?>
    <p class="form-error" role="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>
  <?php } ?>

  <form method="post" style="max-width: 520px;">
    <div class="form-group">
      <label for="full_name">Full Name*</label>
      <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($_POST["full_name"] ?? "", ENT_QUOTES, "UTF-8"); ?>" required>
    </div>

    <div class="form-group">
      <label for="email">Email*</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST["email"] ?? "", ENT_QUOTES, "UTF-8"); ?>" required>
    </div>

    <div class="form-group">
      <label for="phone">Phone</label>
      <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST["phone"] ?? "", ENT_QUOTES, "UTF-8"); ?>">
    </div>

    <div class="form-group">
      <label for="address">Address</label>
      <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST["address"] ?? "", ENT_QUOTES, "UTF-8"); ?>">
    </div>

    <div class="form-actions">
      <button type="submit" name="save">Save</button>
    </div>
  </form>
</div>
</body>
</html>
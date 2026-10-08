<?php
include "db.php";
 
$clients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM clients"))['c'];
$services = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM services"))['c'];
$bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM bookings"))['c'];
 
$revRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS s FROM payments"));
$revenue = $revRow['s'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="/assessment_beginner/style.css">
</head>
<body>
<?php include "nav.php"; ?>

<div class="container">
  <header class="page-header">
    <h1>Dashboard</h1>
  </header>

  <section class="welcome-bar">
    <div>
      <p>Welcome back</p>
      <h2><?php echo htmlspecialchars($_SESSION["username"] ?? "Admin", ENT_QUOTES, "UTF-8"); ?></h2>
    </div>

    <div class="quick-actions">
      <a class="action-btn" href="/assessment_beginner/pages/clients_add.php">Add Client</a>
      <a class="action-btn" href="/assessment_beginner/pages/bookings_create.php">Create Booking</a>
    </div>
  </section>

  <section class="stat-grid">
    <article class="stat-card">
      <span class="stat-label">Total Clients</span>
      <span class="stat-value"><?php echo $clients; ?></span>
    </article>

    <article class="stat-card">
      <span class="stat-label">Total Services</span>
      <span class="stat-value"><?php echo $services; ?></span>
    </article>

    <article class="stat-card">
      <span class="stat-label">Total Bookings</span>
      <span class="stat-value"><?php echo $bookings; ?></span>
    </article>

    <article class="stat-card">
      <span class="stat-label">Total Revenue</span>
      <span class="stat-value">₱<?php echo number_format($revenue, 2); ?></span>
    </article>
  </section>
</div>
</body>
</html>

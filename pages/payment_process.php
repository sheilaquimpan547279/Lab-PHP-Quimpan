<?php
include "../db.php";

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
$booking = null;
$message = "";

if ($booking_id > 0) {
  $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT b.*, c.full_name AS client_name, s.service_name FROM bookings b JOIN clients c ON b.client_id = c.client_id JOIN services s ON b.service_id = s.service_id WHERE b.booking_id = $booking_id"));
}

if (!$booking) {
  $booking_id = 0;
  $message = "Booking not found.";
}

$total_cost = $booking ? (float) $booking['total_cost'] : 0.00;
$total_paid = $booking ? (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS total_paid FROM payments WHERE booking_id = $booking_id"))['total_paid'] : 0.00;
$balance = max(0, $total_cost - $total_paid);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_payment'])) {
  $booking_id = (int) $_POST['booking_id'];
  $amount_paid = (float) $_POST['amount_paid'];
  $method = $_POST['method'];

  $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $booking_id"));
  if (!$booking) {
    $message = "Booking not found.";
  } elseif ($amount_paid <= 0) {
    $message = "Amount paid must be greater than zero.";
  } else {
    mysqli_query($conn, "INSERT INTO payments (booking_id, amount_paid, method) VALUES ($booking_id, $amount_paid, '$method')");

    $new_total_paid = (float) mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS total_paid FROM payments WHERE booking_id = $booking_id"))['total_paid'];
    if ($new_total_paid >= $booking['total_cost']) {
      mysqli_query($conn, "UPDATE bookings SET status = 'PAID' WHERE booking_id = $booking_id");
    } elseif ($new_total_paid > 0) {
      mysqli_query($conn, "UPDATE bookings SET status = 'PARTIAL' WHERE booking_id = $booking_id");
    }

    header("Location: payment_process.php?booking_id=$booking_id");
    exit;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Process Payment</title>
</head>
<body>
<?php include "../nav.php"; ?>

<h1>Process Payment (Booking #<?php echo $booking_id ?: 0; ?>)</h1>

<?php if (!empty($message)) { ?>
  <p><?php echo htmlspecialchars($message); ?></p>
<?php } ?>

<?php if ($booking) { ?>
  <p>Total Cost: ₱<?php echo number_format($total_cost, 2); ?></p>
  <p>Total Paid: ₱<?php echo number_format($total_paid, 2); ?></p>
  <p><strong>Balance: ₱<?php echo number_format($balance, 2); ?></strong></p>

  <form method="post">
    <input type="hidden" name="booking_id" value="<?php echo (int) $booking['booking_id']; ?>">

    <label>Amount Paid</label><br>
    <input type="number" name="amount_paid" min="0.01" step="0.01" value="<?php echo number_format(max(0, $balance), 2, '.', ''); ?>"><br><br>

    <label>Method</label><br>
    <select name="method">
      <option value="CASH" selected>CASH</option>
      <option value="GCASH">GCASH</option>
      <option value="CARD">CARD</option>
      <option value="BANK TRANSFER">BANK TRANSFER</option>
    </select><br><br>

    <button type="submit" name="save_payment">Save Payment</button>
  </form>
<?php } ?>
</body>
</html>

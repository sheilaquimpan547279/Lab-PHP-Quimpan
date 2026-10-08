<?php
include "../db.php";

$result = mysqli_query($conn, "
  SELECT p.*, b.booking_id, c.full_name, s.service_name
  FROM payments p
  JOIN bookings b ON p.booking_id = b.booking_id
  JOIN clients c ON b.client_id = c.client_id
  JOIN services s ON b.service_id = s.service_id
  ORDER BY p.payment_date DESC
");

$total_collected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount_paid),0) AS total FROM payments"))['total'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payments</title>
</head>
<body>
<?php include "../nav.php"; ?>

<h1>Payments</h1>
<p>Total Collected: ₱<?php echo number_format((float) $total_collected, 2); ?></p>

<table border="1" cellpadding="8">
  <tr>
    <th>Payment ID</th>
    <th>Booking</th>
    <th>Client</th>
    <th>Service</th>
    <th>Amount</th>
    <th>Method</th>
    <th>Date</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
      <td><?php echo (int) $row['payment_id']; ?></td>
      <td>#<?php echo (int) $row['booking_id']; ?></td>
      <td><?php echo htmlspecialchars($row['full_name']); ?></td>
      <td><?php echo htmlspecialchars($row['service_name']); ?></td>
      <td>₱<?php echo number_format((float) $row['amount_paid'], 2); ?></td>
      <td><?php echo htmlspecialchars($row['method']); ?></td>
      <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
    </tr>
  <?php } ?>
</table>
</body>
</html>

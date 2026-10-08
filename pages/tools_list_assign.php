<?php
include "../db.php";

$tools = mysqli_query($conn, "SELECT * FROM tools ORDER BY tool_name ASC");
$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
  $booking_id = (int) $_POST['booking_id'];
  $tool_id = (int) $_POST['tool_id'];
  $qty_used = max(1, (int) $_POST['qty_used']);

  $tool = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tools WHERE tool_id = $tool_id"));
  $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $booking_id"));

  if (!$booking) {
    $error = "Booking not found.";
  } elseif (!$tool) {
    $error = "Selected tool does not exist.";
  } elseif ($qty_used > (int) $tool['quantity_available']) {
    $error = "Only " . (int) $tool['quantity_available'] . " units available for this tool.";
  } else {
    mysqli_query($conn, "INSERT INTO booking_tools (booking_id, tool_id, qty_used) VALUES ($booking_id, $tool_id, $qty_used)");
    mysqli_query($conn, "UPDATE tools SET quantity_available = quantity_available - $qty_used WHERE tool_id = $tool_id");

    header("Location: tools_list_assign.php?booking_id=$booking_id&success=1");
    exit;
  }
}

$success_message = isset($_GET['success']) && $_GET['success'] == 1 ? "Tool assigned successfully." : "";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Tools / Inventory</title>
</head>
<body>
<?php include "../nav.php"; ?>

<h1>Tools / Inventory</h1>

<h2>Available Tools</h2>

<table border="1" cellpadding="8">
  <tr>
    <th>Name</th>
    <th>Total</th>
    <th>Available</th>
  </tr>
  <?php while ($tool = mysqli_fetch_assoc($tools)) { ?>
    <tr>
      <td><?php echo htmlspecialchars($tool['tool_name']); ?></td>
      <td><?php echo (int) $tool['quantity_total']; ?></td>
      <td><?php echo (int) $tool['quantity_available']; ?></td>
    </tr>
  <?php } ?>
</table>

<hr>

<h2>Assign Tool to Booking</h2>

<?php if (!empty($error)) { ?>
  <p><?php echo htmlspecialchars($error); ?></p>
<?php } ?>
<?php if (!empty($success_message)) { ?>
  <p><?php echo htmlspecialchars($success_message); ?></p>
<?php } ?>

<form method="post">
  <label>Booking ID</label><br>
  <input type="number" name="booking_id" min="1" value="<?php echo htmlspecialchars((string) $booking_id); ?>"><br><br>

  <label>Tool</label><br>
  <select name="tool_id">
    <?php $all_tools = mysqli_query($conn, "SELECT * FROM tools ORDER BY tool_name ASC"); ?>
    <?php while ($tool = mysqli_fetch_assoc($all_tools)) { ?>
      <option value="<?php echo (int) $tool['tool_id']; ?>"><?php echo htmlspecialchars($tool['tool_name']); ?> (Avail: <?php echo (int) $tool['quantity_available']; ?>)</option>
    <?php } ?>
  </select><br><br>

  <label>Qty Used</label><br>
  <input type="number" name="qty_used" min="1" value="1"><br><br>

  <button type="submit" name="assign">Assign</button>
</form>
</body>
</html>

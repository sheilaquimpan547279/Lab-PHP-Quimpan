<?php
require_once __DIR__ . "/auth.php";
require_login();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "assessment_db";
$port = 3306; // XAMPP's default MySQL port

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
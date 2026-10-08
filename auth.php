<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

function require_login(): void
{
  if (empty($_SESSION["authenticated"])) {
    header("Location: /assessment_beginner/login.php");
    exit;
  }
}

<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (empty($_SESSION['admin']) || empty($_SESSION['admin']['id'])) {
  header("Location: index.php");
  exit;
}

?>
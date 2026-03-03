<?php

require_once "_helpers/data_base.php";
require_once "auth.php";
// require_once "_helpers/helper_db.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CarVita - Event Backoffice</title>
  <link rel="stylesheet" href="style.css">
  <link rel="icon" href="images/carvita_logo.png">
</head>
<body>

  <div class="layout">  

    <header class="navbar">

      <img class="logo" src="images/logo_header.png" alt="">

      <div class="menu">
        <a href="event.php" class="<?= ($current_page == "event") ? "active" : "" ?>"><span class="emoj">🗒️</span>Event</a>
        <a href="communications.php" class="<?= ($current_page == "communications") ? "active" : "" ?>"><span class="emoj">🔈</span>Marketing</a>
        <a href="participants.php" class="<?= ($current_page == "participants") ? "active" : "" ?>"><span class="emoj">👥</span>Interessenten</a>
        <!-- <br><br><br> -->
        <a href="logout.php" class="logout_button">Ausloggen</a>
      </div>
    </header>




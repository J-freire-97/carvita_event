<?php

session_start();
if (isset($_SESSION['admin'])) {
  header('Location: event.php');
  exit;
}

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
<body class="login_page">

  <header class="navbar">
    <img src="images/logo_header.png" alt="">
  </header>


  <main class="backoffice_body">

    <h1>Backoffice-Anmeldung</h1>


    <form method="post" action="verify_login.php">
      <label>
        Email
        <br>
        <input type="email" name="email" required>
      </label>
      
      <br><br>

      <label>
        Passwort
        <br>
        <input type="password" name="password" required>
      </label>

      <br><br>

      <button type="submit">Anmeldung</button>
    </form>
  </main>

<?php

require_once "components/footer.php";

?>
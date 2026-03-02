<?php

require_once "components/header.php";

?>

  <main class="register">

    <a href="index.php">
      <img src="images/carvita_logo.png" alt="carvita_logo">
    </a>

    <h1>Registration Form</h1>
  
    <div id="participants-form"></div>
  
    <p id="success-message" style="display:none; color: green;">
      Registration successfully completed.
    </p>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
  <script>
    emailjs.init('4k5HeJ68jU3cTRph9');
  </script>

  <script type="module" src="register.js"></script>
  
<?php

require_once "components/footer.php";

?>
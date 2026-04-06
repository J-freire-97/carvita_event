<?php
ob_start();

$current_page = "event";
require_once 'components/header.php';

$form = ($_SERVER['REQUEST_METHOD'] === 'POST');

$msg_type = null; // success | error
$msg_text = null;

if ($form) {
  $name = trim($_POST['name'] ?? '');
  $date = trim($_POST['date'] ?? '');      // formato: YYYY-MM-DDTHH:MM (se usarmos datetime-local)
  $location = trim($_POST['location'] ?? '');

  if ($name === '' || $date === '' || $location === '') {
    $msg_type = 'error';
    $msg_text = 'Bitte füllen Sie alle Pflichtfelder aus.';
  } else {
    try {
      // Converter datetime-local -> "YYYY-MM-DD HH:MM:SS"
      $date_db = str_replace('T', ' ', $date) . ':00';

      $stmt = $pdo->prepare("INSERT INTO events (name, date, location, created_at, updated_at) VALUES (?, ?, ?, NOW(), NULL) ");
      $stmt->execute([$name, $date_db, $location]);

      header("Location: event.php?success=1");
      exit;

    } catch (Exception $e) {
      $msg_type = 'error';
      $msg_text = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
    }
  }
}

ob_end_flush();
?>

<div class="modal_overlay">
  <div class="modal_card">

    <h1 class="modal_title">Neues Event anlegen</h1>

    <?php if ($msg_text): ?>
      <br>
      <h5 class="<?= $msg_type === 'success' ? 'text_success' : 'text_danger' ?>">
        <?= htmlspecialchars($msg_text) ?>
      </h5>
    <?php endif; ?>

    <form class="modal_form" method="post">

      <div class="form_field">
        <label>Event Name</label>
        <input type="text" name="name" value="">
      </div>

      <div class="form_field">
        <label>Datum & Uhrzeit</label>
        <input type="datetime-local" name="date" value="">
      </div>

      <div class="form_field full">
        <label>Ort</label>
        <input type="text" name="location" value="">
      </div>

      <div class="modal_actions">
        <a class="btn_secondary" href="event.php">Abbrechen</a>
        <button class="btn_primary" type="submit">
          <span class="btn_icon">+</span>
          Hinzufügen
        </button>
      </div>

    </form>

  </div>
</div>

<?php require_once 'components/footer.php'; ?>
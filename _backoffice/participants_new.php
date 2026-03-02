<?php
$current_page = "participants";

$form = ($_SERVER['REQUEST_METHOD'] === 'POST');

$msg_type = null; // success | error
$msg_text = null;

if ($form) {
  $title = trim($_POST['title'] ?? '');
  $first_name = trim($_POST['first_name'] ?? '');
  $last_name = trim($_POST['last_name'] ?? '');
  $company = trim($_POST['company'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $group = trim($_POST['group'] ?? '');

  if ($title === '' || $first_name === '' || $last_name === '' || $email === '') {
    $msg_type = 'error';
    $msg_text = 'Bitte füllen Sie alle Pflichtfelder aus.'; // Preencha os campos obrigatórios
  } else {

    // Verificar se o email já existe
    $stmt = $pdo->prepare("SELECT id FROM participants WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
      $msg_type = 'error';
      $msg_text = 'Diese E-Mail-Adresse ist bereits registriert.'; // Este email já está registado
    } else {
      try {
        // Inserir participante
        $stmt = $pdo->prepare("INSERT INTO participants (title, first_name, last_name, company, email, `group`) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $first_name, $last_name, $company, $email, $group]);

        // Sucesso -> voltar à lista com flag
        header("Location: participants.php?success=1");
        exit;

      } catch (Exception $e) {
        $msg_type = 'error';
        $msg_text = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
      }
    }
  }
}

require_once 'components/header.php';

?>

<div class="modal_overlay">
  <div class="modal_card">

    <h1 class="modal_title">Neuen Interessenten anlegen</h1>

    <?php if ($msg_text): ?>
      <br>
      <h5 class="<?= $msg_type === 'success' ? 'text_success' : 'text_danger' ?>">
        <?= htmlspecialchars($msg_text) ?>
      </h5>
    <?php endif; ?>

    <form class="modal_form" method="post">

    <div class="form_field">
      <label>Titel</label>
      <select name="title" required>
        <option value="">-- Select --</option>
        <option value="Mr.">Mr.</option>
        <option value="Ms.">Ms.</option>
        <option value="Dr.">Dr.</option>
        <option value="Prof.">Prof.</option>
      </select>
    </div>

      <div class="form_field">
        <label>Vorname</label>
        <input type="text" name="first_name" value="">
      </div>

      <div class="form_field">
        <label>Nachname</label>
        <input type="text" name="last_name" value="">
      </div>

      <div class="form_field full">
        <label>Firma</label>
        <input type="text" name="company" value="">
      </div>

      <div class="form_field full">
        <label>Email Adresse</label>
        <input type="email" name="email" value="">
      </div>

      <div class="form_field full">
        <label>Gruppe</label>
        <input type="text" name="group" value="">
      </div>

      <div class="modal_actions">
        <a class="btn_secondary" href="participants.php">Abbrechen</a>
        <button class="btn_primary" type="submit">
          <span class="btn_icon">+</span>
          Hinzufügen
        </button>
      </div>

    </form>

  </div>
</div>

<?php require_once 'components/footer.php'; ?>
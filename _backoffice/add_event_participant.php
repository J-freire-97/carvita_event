<?php
ob_start();

$current_page = "event";
require_once 'components/header.php';
require_once '_helpers/mailer.php';
require_once '_helpers/qrcode.php';
$email_config = require '_helpers/config_email.php';

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

$form = ($_SERVER['REQUEST_METHOD'] === 'POST');
$msg_type = null;
$msg_text = null;

// 1) Select all participants
$all = $pdo->query("SELECT * FROM participants ORDER BY last_name, first_name")->fetchAll(PDO::FETCH_ASSOC);

// 2) Participansts who are already at the event
$stmt = $pdo->prepare("SELECT participant_id FROM event_participants WHERE event_id = ?");
$stmt->execute([$event_id]);
$in_event_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
$in_event_map = array_flip($in_event_ids); // para lookup rápido

// 3) Select participants to add to the event Se submeteu: inserir selecionados que ainda não estão no evento
if ($form) {
  $selected = $_POST['participant_ids'] ?? [];
  if (!is_array($selected)) $selected = [];

  $selected = array_map('intval', $selected);

  $to_insert = [];
  foreach ($selected as $pid) {
    if (!isset($in_event_map[(string)$pid]) && $pid > 0) {
      $to_insert[] = $pid;
    }
  }

  if (count($to_insert) === 0){
    $msg_type = 'error';
    $msg_text = 'Keine neuen Teilnehmer ausgewählt.'; // Nenhum novo selecionado
  } 
  else{
    // 1) Event name
    $stmtEv = $pdo->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
    $stmtEv->execute([$event_id]);
    $ev = $stmtEv->fetch(PDO::FETCH_ASSOC);
    $event_name = $ev['name'] ?? 'CarVita Event';
    $location = $ev['location'];
    $event_date = $ev['date'];


    // 2) statement para buscar participante
    $stmtP = $pdo->prepare("SELECT * FROM participants WHERE id = ? LIMIT 1");

    $sent = 0;
    $failed = 0;

    try {
      $pdo->beginTransaction();

      // INSERT com qr_code
      $stmtIns = $pdo->prepare("INSERT INTO event_participants (event_id, participant_id, status, qr_code, created_at) VALUES (?, ?, 1, ?, NOW())");

      // guardamos tokens por participante
      $tokensByPid = [];
      $epIdByPid = [];

      foreach ($to_insert as $pid) {
        $token = generate_qr_token();
        $tokensByPid[$pid] = $token;
        $stmtIns->execute([$event_id, $pid, $token]);
        $epIdByPid[$pid] = (int)$pdo->lastInsertId();
      }

      $pdo->commit();

      // 3) enviar emails (fora da transação)
      foreach ($to_insert as $pid){
        $stmtP->execute([$pid]);
        $p = $stmtP->fetch(PDO::FETCH_ASSOC);
        if (!$p || empty($p['email'])) continue;

        $token = $tokensByPid[$pid] ?? '';
        $checkin_url = build_checkin_url($token);
        $qr_path = build_qr_png_path($checkin_url);

        $html = build_invitation_html([
          'title' => $p['title'] ?? '',
          'first_name' => $p['first_name'] ?? '',
          'full_name' => trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')),
          'company' => $p['company'] ?? '',
          'email' => $p['email'] ?? '',
          'event_name'  => $event_name,
          'event_location' => $location,
          'event_date' => $event_date,
          'checkin_url' => $checkin_url,
        ]);

        $ok = send_email_outlook(
          $email_config,
          $p['email'],
          trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')),
          $event_name . 'Event Registration',
          $html,
          $qr_path
        );

        if ($ok) $sent++; else $failed++;
      }

      // 4) feedback + redirect
      header("Location: event_participants.php?event_id={$event_id}&success=1&sent={$sent}&failed={$failed}");
      exit;

    } 
    catch (Exception $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $msg_type = 'error';
      $msg_text = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';
    }
  }
}

ob_end_flush();

?>

  <div class="modal_overlay">
    <div class="modal_card modal_card_table">

      <h1 class="modal_title">Teilnehmer auswählen</h1>

      <?php if ($msg_text): ?>
      <br>
      <h5 class="<?= $msg_type === 'success' ? 'text_success' : 'text_danger' ?>">
        <?= htmlspecialchars($msg_text) ?>
      </h5>
      <?php endif; ?>

      <form method="post">

      <div class="table_card modal_table_scroll">
        <table class="table">
          <thead>
            <tr>
            <th style="width:40px;"></th>
            <th>Titel</th>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Firma</th>
            <th>Email</th>
            <th>Status</th>
            </tr>
          </thead>
          <tbody>

            <?php foreach ($all as $p): 
            $already = isset($in_event_map[(string)$p['id']]);
            ?>
            <tr>
              <td>
                <input type="checkbox" name="participant_ids[]" value="<?= (int)$p['id'] ?>" <?= $already ? 'checked disabled' : '' ?>>
              </td>
              <td><?= htmlspecialchars($p['title'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['first_name'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['last_name'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['company'] ?? '') ?></td>
              <td><?= htmlspecialchars($p['email'] ?? '') ?></td>
              <td>
              <?php if ($already): ?>
                <span class="badge badge-success">Bereits im Event</span>
                <!-- Já está no evento -->
              <?php else: ?>
                <span class="badge badge-secondary">Nicht im Event</span>
                <!-- Ainda não está no evento -->
              <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>

          </tbody>
        </table>
      </div>

      <div class="modal_actions">
        <a class="btn_secondary" href="event_participants.php?event_id=<?= $event_id ?>">Abbrechen</a>
        <!-- Cancelar -->
        <button class="btn_primary" type="submit">
        <span class="btn_icon">+</span>
        Hinzufügen
        </button>
        <!-- Adicionar -->
      </div>

      </form>
    </div>
  </div>

<?php require_once 'components/footer.php'; ?>

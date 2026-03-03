<?php
ob_start();

$current_page = "communications";
require_once 'components/header.php';

$email_id = isset($_GET['email_id']) ? (int)$_GET['email_id'] : 0;

// email + evento (para mostrar no título)
$mail = select_sql_unic("SELECT e.subject, ev.name AS event_name FROM email e JOIN events ev ON ev.id = e.event_id WHERE e.id = $email_id");

if (!$mail) {
  header("Location: communications.php");
  exit;
}

// lista de recipients deste email
$rows = select_sql("SELECT ep.id AS event_participant_id, p.title, p.first_name, p.last_name, p.company, p.email, ep.status FROM email_recipients er JOIN event_participants ep ON ep.id = er.event_participant_id JOIN participants p ON p.id = ep.participant_id WHERE er.mail_id = $email_id GROUP BY ep.id");

ob_end_flush();
?>

<div class="modal_overlay">
  <div class="modal_card_table">

    <h1 class="modal_title" style="margin-bottom:10px;">
      Erneut versenden: <?= htmlspecialchars($mail['subject']) ?>
    </h1>

    <form method="post" action="communications_resend.php">
      <input type="hidden" name="email_id" value="<?= (int)$email_id ?>">

      <div class="modal_actions" style="margin-top:0;">
        <a class="btn_secondary" href="communications_status.php?email_id=<?= (int)$email_id ?>">Abbrechen</a>
        <button class="btn_primary" type="submit">
          <span class="btn_icon">📨</span>
          Erneut versenden
        </button>
      </div>

      <div class="modal_table_scroll">
        <table class="table">
          <thead>
            <tr>
              <th>✓</th>
              <th>Titel</th>
              <th>Vorname</th>
              <th>Nachname</th>
              <th>Firma</th>
              <th>Email</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r):
              if ((int)$r['status'] === 2) { $label='Zugesagt'; $class='badge_success'; }
              else { $label='Ausstehend'; $class='badge_blue'; }
            ?>
              <tr>
                <td>
                  <input type="checkbox" name="event_participant_ids[]" value="<?= (int)$r['event_participant_id'] ?>">
                </td>
                <td><?= htmlspecialchars($r['title']) ?></td>
                <td><?= htmlspecialchars($r['first_name']) ?></td>
                <td><?= htmlspecialchars($r['last_name']) ?></td>
                <td><?= htmlspecialchars($r['company']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><span class="badge <?= $class ?>"><?= $label ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </form>

  </div>
</div>

<?php require_once 'components/footer.php'; ?>
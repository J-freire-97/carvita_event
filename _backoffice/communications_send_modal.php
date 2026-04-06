<?php
$current_page = "communications";
require_once 'components/header.php';

// events + emails 
$events = select_sql("SELECT * FROM events");
$templates = select_sql("SELECT * FROM email");
?>

<div class="modal_overlay">
  <div class="modal_card">

    <h1 class="modal_title">Einladung versenden</h1>

    <form class="modal_form" method="post" action="communications_send.php">

      <div class="form_field full">
        <label>Event</label>
        <select name="event_id" required>
          <option value="">-- Select --</option>
          <?php foreach ($events as $ev): ?>
            <option value="<?= (int)$ev['id'] ?>">
              <?= htmlspecialchars($ev['name']) ?> (<?= date("d.m.Y", strtotime($ev['date'])) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form_field full">
        <label>Email (Vorlage)</label>
        <select name="template_email_id" required>
          <option value="">-- Select --</option>
          <?php foreach ($templates as $t): ?>
            <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['subject']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="modal_actions">
        <a class="btn_secondary" href="communications.php">Abbrechen</a>
        <button class="btn_primary" type="submit">
          <span class="btn_icon">📨</span>
          Versenden
        </button>
      </div>

    </form>

  </div>
</div>

<?php require_once 'components/footer.php'; ?>
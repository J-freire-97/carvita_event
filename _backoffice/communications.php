<?php 

$current_page = "communications";
require_once 'components/header.php'; 

$filter = $_GET['filter'] ?? 'all';

$where = "";
if ($filter === 'future') {
  $where = "WHERE ev.date >= NOW()";
} elseif ($filter === 'past') {
  $where = "WHERE ev.date < NOW()";
}

$sql = "SELECT email.*, ev.name AS event_name, ev.date AS event_date,COUNT(email_recipients.id) AS total_recipients FROM email JOIN events ev ON ev.id = email.event_id LEFT JOIN email_recipients ON email.id = email_recipients.mail_id $where GROUP BY email.id";
$email = select_sql($sql);
$events = select_sql("SELECT * FROM events");
$templates = select_sql("SELECT * FROM email");

?>


    <main class="main_content">
      <h1>Mail Übersicht</h1>

      <div class="_submenu">

        <div class="filters">
          <a class="<?= ($filter==='all') ? 'active' : '' ?>" href="communications.php?filter=all">Alle</a>
          <a class="<?= ($filter==='future') ? 'active' : '' ?>" href="communications.php?filter=future">Zukünftige</a>
          <a class="<?= ($filter==='past') ? 'active' : '' ?>" href="communications.php?filter=past">Vergangene</a>
        </div>

        <div class="_add_new">
          <a href="communications_send_modal.php"><span class="add">📨</span>Einladung versenden</a>
        </div>

      </div>

      <div class="table_wrapper">

        <table class="table event_table">
          <thead>
            <tr>
              <th><span class="emoji">◌</span>Name der Email</th>
              <th>Versendet am</th>
              <th>Betreff</th>
              <th>Empfagen</th>
            </tr>
        </thead>

        <tbody>
          <?php foreach($email as $e): ?>

            <tr onclick="window.location='communications_status.php?email_id=<?= $e['id'] ?>'">
              <td><span class="emoji">◌</span><?= $e['subject']?></td>
              <td><?= date("d.m.Y", strtotime($e['sent_at'])) ?></td>
              <td><?= $e['body'] ?></td>
              <td><?= $e['total_recipients'] ?></td>
            </tr>

          <?php endforeach; ?>

          </tbody>
        </table>


        <div class="pagination">
     
        </div>

      </div>

    </main>

  </div>

<?php require_once 'components/footer.php'; ?>
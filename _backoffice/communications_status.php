<?php
$current_page = "communications";
require_once 'components/header.php';

$email_id = isset($_GET['email_id']) ? (int)$_GET['email_id'] : 0;
$status = $_GET['status'] ?? 'all'; 

// buscar email + evento
$sql_mail = "SELECT e.*, ev.name AS event_name FROM email e JOIN events ev ON ev.id = e.event_id WHERE e.id = $email_id";
$mail = select_sql_unic($sql_mail);

if (!$mail) {
  echo "<main class='main_content'><h1>Mail nicht gefunden</h1></main>";
  require_once 'components/footer.php';
  exit;
}

// filtro
$where = "WHERE er.mail_id = $email_id";

if ($status === 'accepted') {
  $where .= " AND ep.status = 2";
} elseif ($status === 'pending') {
  $where .= " AND ep.status != 2";
}

// paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;

$sql_total = "SELECT COUNT(*) as total FROM email_recipients er JOIN event_participants ep ON ep.id = er.event_participant_id JOIN participants p ON p.id = ep.participant_id $where";
$total_result = select_sql_unic($sql_total);
$total_rows = $total_result ? (int)$total_result['total'] : 0;

$total_pages = (int)ceil($total_rows / $limit);
if ($total_pages < 1) $total_pages = 1;
if ($page < 1) $page = 1;
if ($page > $total_pages) $page = $total_pages;

$offset = ($page - 1) * $limit;

// dados
$sql = "SELECT p.title, p.first_name, p.last_name, p.company, p.email, ep.status FROM email_recipients er JOIN event_participants ep ON ep.id = er.event_participant_id JOIN participants p ON p.id = ep.participant_id $where LIMIT $limit OFFSET $offset";
$rows = select_sql($sql);
?>

<main class="main_content">
  <h1>Be: <?= htmlspecialchars($mail['subject']) ?></h1>

  <div class="_submenu">

    <div class="filters">
      <a class="<?= ($status==='all') ? 'active' : '' ?>"
         href="communications_status.php?email_id=<?= $email_id ?>&status=all">Alle</a>

      <a class="<?= ($status==='accepted') ? 'active' : '' ?>"
         href="communications_status.php?email_id=<?= $email_id ?>&status=accepted">Zugesagt</a>

      <a class="<?= ($status==='pending') ? 'active' : '' ?>"
         href="communications_status.php?email_id=<?= $email_id ?>&status=pending">Ausstehend</a>
    </div>

    <div class="_add_new">
      <a href="communications.php"><span class="add">↩</span>Zurück</a>
      <a href="communications_resend_modal.php?email_id=<?= $email_id ?>"><span class="add">📨</span>Erneut versenden</a>
    </div>

  </div>

  <div class="table_wrapper">
    <table class="table">
      <thead>
        <tr>
          <th><span class="emoji">◌</span>Titel</th>
          <th>Vorname</th>
          <th>Nachname</th>
          <th>Firma</th>
          <th>Email</th>
          <th>Status</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($rows as $r):

          if ((int)$r['status'] === 2) {
            $label = 'Zugesagt';       // Confirmado
            $class = 'badge_success';
          } else {
            $label = 'Ausstehend';     // Pendente
            $class = 'badge_blue';
          }
        ?>
          <tr>
            <td><span class="emoji">◌</span><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['first_name']) ?></td>
            <td><?= htmlspecialchars($r['last_name']) ?></td>
            <td><?= htmlspecialchars($r['company']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><span class="badge <?= $class ?>"><?= $label ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>

    </table>

    <div class="pagination">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?email_id=<?= $email_id ?>&status=<?= urlencode($status) ?>&page=<?= $i ?>"
           class="<?= ($i == $page) ? 'active' : '' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </div>

  </div>
</main>

</div>

<?php require_once 'components/footer.php'; ?>
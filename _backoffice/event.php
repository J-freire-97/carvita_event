<?php 

$current_page = "event";
require_once 'components/header.php'; 

$filter = $_GET['filter'] ?? 'all'; // all | future | past

$where = "";
if ($filter === 'future') {
  $where = "WHERE date >= NOW()";
} elseif ($filter === 'past') {
  $where = "WHERE date < NOW()";
}

$sql = "SELECT * FROM events $where ORDER BY id ASC";
$events = select_sql($sql);

?>


    <main class="main_content">
      <h1>Event Übersicht </h1>

      <div class="_submenu">

        <div class="filters">
          <a class="<?= ($filter==='all') ? 'active' : '' ?>" href="event.php?filter=all">Alle</a>
          <a class="<?= ($filter==='future') ? 'active' : '' ?>" href="event.php?filter=future">Zukünftige</a>
          <a class="<?= ($filter==='past') ? 'active' : '' ?>" href="event.php?filter=past">Vergangene</a>
        </div>

        <div class="_add_new">
          <a href="event_new.php"><span class="add">+</span> Neues Event anlegen</a>
        </div>

      </div>

      <div class="table_wrapper">

        <table class="table event_table">
          <thead>
            <tr>
              <th><span class="emoji">◌</span>Event Name</th>
              <th>Date <span class="data_emoji">🗒</span></th>
              <th>Location</th>
              <th>Eingeladen</th>
              <th>Zugesagt</th>
            </tr>
        </thead>

        <tbody>
          <?php foreach($events as $e): ?>

            <?php
              // Contar convidados (todos os participantes desse evento)
              $sql_invited = "SELECT COUNT(*) as total FROM event_participants WHERE event_id = ".$e['id'];
              $invited = select_sql_unic($sql_invited);

              // Contar confirmados
              $sql_confirmed = "SELECT COUNT(*) as total FROM event_participants WHERE event_id = ".$e['id']." AND status = 2";
              $confirmed = select_sql_unic($sql_confirmed);
            ?>

            <tr onclick="window.location='event_participants.php?event_id=<?= $e['id'] ?>'">
              <td><span class="emoji">◌</span><?= $e['id'] ?>. <?= $e['name'] ?></td>
              <td><?= date("d.m.Y", strtotime($e['date'])) ?></td>
              <td><?= $e['location'] ?></td>
              <td><?= $invited['total'] ?></td>
              <td><?= $confirmed['total'] ?></td>
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
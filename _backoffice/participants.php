<?php 

$current_page = "participants";
require_once 'components/header.php'; 

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$group_filter = $_GET['group'] ?? 'all';

$where = "";
if ($group_filter !== 'all') {
  $g = addslashes($group_filter);
  $where = "WHERE `group` = '$g'";
}

$sql_total = "SELECT COUNT(*) as total FROM participants $where";
$total_result = select_sql_unic($sql_total);

$total_participants = $total_result['total'];
$total_pages = ceil($total_participants / $limit);


$sql = "SELECT * FROM participants $where LIMIT $limit OFFSET $offset";
$participants = select_sql($sql);

?>

    <main class="main_content">
      <h1>Interessenten Übersicht </h1>

      <?php if (!empty($_GET['success'])): ?>
        <div class="alert text_success">Interessent wurde erfolgreich hinzugefügt.</div>
      <?php endif; ?>

      <div class="_submenu">

        <div class="filters">
          <a class="<?= ($group_filter==='all') ? 'active' : '' ?>" href="participants.php?group=all">Alle</a>
          <a class="<?= ($group_filter==='Versicherung') ? 'active' : '' ?>" href="participants.php?group=Versicherung">Versicherung</a>
          <a class="<?= ($group_filter==='Sachverständiger') ? 'active' : '' ?>" href="participants.php?group=Sachverständiger">Sachverständiger</a>
        </div>

        <div class="_add_new">
          <a href="participants_new.php"><span class="add">+</span>Interessent hinzufügen </a>
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
              <th>Gruppe</th>
            </tr>
          </thead>

         <tbody>
            <?php foreach($participants as $p): ?>
              <tr>
                <td><span class="emoji">◌</span><?= $p['title'] ?></td>
                <td><?= $p['first_name'] ?></td>
                <td><?= $p['last_name'] ?></td>
                <td><?= $p['company'] ?></td>
                <td><?= $p['email'] ?></td>
                <td><?= $p['group'] ?></td>
              </tr>
            <?php endforeach; ?>

          </tbody>

        </table>

        <div class="pagination">
          <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?=$i?>" class="<?=($i == $page) ? 'active' : ''?>">
              <?=$i?>
            </a>
          <?php endfor; ?>        
        </div>

      </div>

    </main>

  </div>

<?php require_once 'components/footer.php'; ?>
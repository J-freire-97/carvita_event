<?php 

$current_page = "event";
require_once 'components/header.php'; 
require_once '_helpers/event_participants_helper.php'; 

?>
  <script src="https://unpkg.com/html5-qrcode"></script>

    <div id="qrModal" class="qr_modal" style="display:none;">
      <div class="qr_modal_content">
        <div id="qr-reader" style="width:300px;"></div>
        <button type="button" onclick="closeQR()">Schließen</button>
      </div>
    </div>

    <main class="main_content">
      <?php ?>
      <h1><?= $event['id'] ?>. <?= $event['name'] ?> – Teilnehmer</h1>

      <?php if (!empty($_GET['success'])): ?>
        <div class="alert text_success">
          Teilnehmer wurde erfolgreich hinzugefügt.
        </div>

        <?php if (isset($_GET['sent'])): ?>
          <div class="text_success">
            <?= (int)$_GET['sent'] ?> E-Mail(s) versendet.
          </div>
        <?php endif; ?>

      <?php endif; ?>



      <div class="_submenu">

        <div class="filters">
          <a class="<?= ($status == 'all') ? 'active' : '' ?>" href="event_participants.php?event_id=<?php echo $event_id; ?>">Alle</a>
          <a class="<?= ($status == 'confirmed') ? 'active' : '' ?>" href="event_participants.php?event_id=<?php echo $event_id; ?>&status=confirmed">Zukünftige</a>
          <a class="<?= ($status == 'checked') ? 'active' : '' ?>" href="event_participants.php?event_id=<?php echo $event_id; ?>&status=checked">Eingecheckt</a>
          <a class="<?= ($status == 'invited') ? 'active' : '' ?>" href="event_participants.php?event_id=<?php echo $event_id; ?>&status=invited">Eingeladen</a>
        </div>

        <div class="_add_new">
          <a href="event.php"><span class="add">↩</span>Zurück</a>
          <button onclick="openQR()"><span class="add">⛶</span>Ticket scannen</button>
          <a href="add_event_participant.php?event_id=<?= $event_id ?>"><span class="add">+</span>Teilnehmer hinzufügen</a>
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
          <?php foreach($participants as $p){
            
            switch($p['status']){
              case 2:
                  $label = '✓ Zugesagt';
                  $class = 'badge_success';
              break;
          
              case 3:
                  $label = '★ Eingecheckt';
                  $class = 'badge_purple';
              break;
          
              case 1:
                  $label = '◌ Eingeladen';
                  $class = 'badge_blue';
              break;
          
              default:
                  $label = 'Ausstehend';
                  $class = 'badge_warning';
            }
          
          ?>

            <tr>
              <td><span class="emoji">◌</span><?= $p['title'] ?></td>
              <td><?= $p['first_name'] ?></td>
              <td><?= $p['last_name'] ?></td>
              <td><?= $p['company'] ?></td>
              <td><?= $p['email'] ?></td>
              <td>
              <span class="badge <?= $class; ?>">
                <?= $label; ?>
              </span>
              </td>
            </tr>

          <?php }; ?>

          </tbody>
        </table>

        <div class="pagination">
          <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?event_id=<?= $event_id ?>&status=<?= $status ?>&page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
              <?=$i?>
            </a>
          <?php endfor; ?>
        </div>

      </div>

    </main>

  </div>

  <script>

    let qrScanner;

    function openQR() {
        document.getElementById("qrModal").style.display = "flex";

        qrScanner = new Html5Qrcode("qr-reader");

        qrScanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            qrCodeMessage => {

                qrScanner.stop();

                let url = new URL(qrCodeMessage);
                let code = url.searchParams.get("code");

                fetch("checkin.php?code=" + code)
                    .then(res => res.text())
                    .then(data => {
                        alert("Check-in realizado!");
                        location.reload();
                    });
            },
            errorMessage => {}
        );
    }

    function closeQR() {
      const modal = document.getElementById("qrModal");

      if (qrScanner) {
        qrScanner.stop()
          .then(() => {
            modal.style.display = "none";
          })
          .catch(() => {
            modal.style.display = "none";
          });
      } else {
        modal.style.display = "none";
      }
    }
    
  </script>

<?php require_once 'components/footer.php'; ?>
<?php
require_once '_helpers/data_base.php'; // garante que tens $pdo aqui

$code = trim($_GET['code'] ?? '');

if ($code === '') {
    // DE: Ungültiger QR-Code / PT-PT: QR inválido
    echo '<h2>Ungültiger QR-Code.</h2>';
    exit;
}

// Opcional: se quiseres impedir “re-check-in”, podes usar status <> 3
$stmt = $pdo->prepare("
    UPDATE event_participants
    SET status = 3
    WHERE qr_code = ?
    LIMIT 1
");
$stmt->execute([$code]);

$ok = ($stmt->rowCount() > 0);

// Resposta simples (boa para telemóvel / scanner)
?>

<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Check-in</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 24px;">
  <?php if ($ok): ?>
    <h2>Check-in erfolgreich!</h2>
    <!-- PT-PT: Check-in com sucesso -->
    <p>Der Teilnehmer wurde als <b>eingecheckt</b> markiert.</p>
    <!-- PT-PT: O participante foi marcado como check-in efetuado -->
  <?php else: ?>
    <h2>Ungültiger QR-Code.</h2>
    <!-- PT-PT: QR inválido -->
    <p>Dieser Code ist nicht gültig oder wurde bereits verwendet.</p>
    <!-- PT-PT: Este código não é válido ou já foi utilizado -->
  <?php endif; ?>
</body>
</html>
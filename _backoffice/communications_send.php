<?php
ob_start();

$current_page = "communications";
require_once 'components/header.php';

require_once '_helpers/mailer.php';
$cfg = require '_helpers/config_email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: communications.php");
  exit;
}

$event_id    = (int)($_POST['event_id'] ?? 0);
$template_id = (int)($_POST['template_email_id'] ?? 0);

if ($event_id <= 0 || $template_id <= 0) {
  header("Location: communications_send_modal.php");
  exit;
}

// 1) Buscar template (subject/body)
$template = select_sql_unic("SELECT subject, body FROM email WHERE id = $template_id");
if (!$template) {
  header("Location: communications_send_modal.php");
  exit;
}

// 2) Buscar evento (inclui localização)
$ev = select_sql_unic("SELECT id, name, date, location FROM events WHERE id = $event_id");
if (!$ev) {
  header("Location: communications_send_modal.php");
  exit;
}

$subject = $template['subject'];
$body    = $template['body']; // guardar na BD (mesmo que o HTML enviado seja gerado no mailer)

// 3) Criar registo de email para este evento
$stmt = $pdo->prepare("INSERT INTO email (event_id, subject, body, sent_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$event_id, $subject, $body]);
$mail_id = (int)$pdo->lastInsertId();

// 4) Buscar participantes do evento
$recipients = select_sql("SELECT ep.id AS event_participant_id, p.title, p.first_name, p.last_name, p.company, p.email FROM event_participants ep JOIN participants p ON p.id = ep.participant_id WHERE ep.event_id = $event_id ORDER BY p.last_name, p.first_name");

if (!$recipients) {
  header("Location: communications.php");
  exit;
}

// 5) Inserir histórico + enviar email (SEM QR)
$ins = $pdo->prepare("INSERT INTO email_recipients (mail_id, event_participant_id, sent_at, opened_at) VALUES (?, ?, NOW(), NULL)");

foreach ($recipients as $r) {
  $ep_id = (int)$r['event_participant_id'];

  // histórico
  $ins->execute([$mail_id, $ep_id]);

  // nome para o mailer
  $full_name = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));

  // HTML sem QR: só participant + event + location
  $v = [
    'title' => $r['title'] ?? '',
    'full_name' => $full_name,
    'company' => $r['company'] ?? '',
    'email' => $r['email'] ?? '',
    'event_name' => $ev['name'] ?? '',
    'event_location' => $ev['location'] ?? '',
  ];

  $html = build_event_summary_html($v);

  // envia SEM QR (último argumento null)
  send_email_outlook($cfg, $r['email'], $full_name, $subject, $html, null);
}

header("Location: communications_status.php?email_id=" . $mail_id);
exit;

ob_end_flush();
?>
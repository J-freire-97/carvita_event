<?php
$current_page = "communications";
require_once 'components/header.php';

require_once '_helpers/mailer.php';
$cfg = require '_helpers/config_email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: communications.php");
  exit;
}

$email_id = (int)($_POST['email_id'] ?? 0);
$ids = $_POST['event_participant_ids'] ?? [];

if ($email_id <= 0 || !is_array($ids) || count($ids) === 0) {
  header("Location: communications_resend_modal.php?email_id=" . $email_id);
  exit;
}

// 1) Buscar email + evento (para subject + location)
$mail = select_sql_unic("SELECT e.subject, e.body, ev.name AS event_name, ev.location AS event_location FROM email e JOIN events ev ON ev.id = e.event_id WHERE e.id = $email_id");

if (!$mail) {
  header("Location: communications.php");
  exit;
}

$subject = $mail['subject'];

// 2) Buscar destinatários selecionados
$clean_ids = array_map('intval', $ids);
$in = implode(',', $clean_ids);

$recipients = select_sql("SELECT ep.id AS event_participant_id, p.title, p.first_name, p.last_name, p.company, p.email FROM event_participants ep JOIN participants p ON p.id = ep.participant_id WHERE ep.id IN ($in) ORDER BY p.last_name, p.first_name");

if (!$recipients) {
  header("Location: communications_resend_modal.php?email_id=" . $email_id);
  exit;
}

// 3) Inserir histórico + reenviar (SEM QR)
$ins = $pdo->prepare("INSERT INTO email_recipients (mail_id, event_participant_id, sent_at, opened_at) VALUES (?, ?, NOW(), NULL)");

foreach ($recipients as $r) {
  $ep_id = (int)$r['event_participant_id'];

  // histórico
  $ins->execute([$email_id, $ep_id]);

  // nome
  $full_name = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));

  // HTML sem QR: participant + event + location
  $v = [
    'title' => $r['title'] ?? '',
    'full_name' => $full_name,
    'company' => $r['company'] ?? '',
    'email' => $r['email'] ?? '',
    'event_name' => $mail['event_name'] ?? '',
    'event_location' => $mail['event_location'] ?? '',
  ];

  $html = build_event_summary_html($v);

  // envia SEM QR
  send_email_outlook($cfg, $r['email'], $full_name, $subject, $html, null);
}

header("Location: communications_status.php?email_id=" . $email_id);
exit;

?>
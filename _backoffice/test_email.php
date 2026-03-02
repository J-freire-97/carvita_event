<?php
require_once '_helpers/mailer.php';
$email_config = require '_helpers/config_email.php';

$html = build_invitation_html([
  'title' => 'Mr.',
  'first_name' => 'Joao',
  'full_name' => 'Joao Freire',
  'company' => 'Test Company',
  'email' => 'joao.freire97@outlook.com',
  'event_name' => 'Test Event'
]);

$ok = send_email_outlook(
  $email_config,
  'joao.freire97@outlook.com',
  'Joao Freire',
  'Test email PHPMailer',
  $html
);

echo $ok ? "OK - enviado" : "FALHOU";

?>
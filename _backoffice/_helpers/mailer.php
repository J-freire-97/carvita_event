<?php

require_once '_helpers/phpmailer/src/Exception.php';
require_once '_helpers/phpmailer/src/PHPMailer.php';
require_once '_helpers/phpmailer/src/SMTP.php';

function send_email_outlook($cfg, $to_email, $to_name, $subject, $html, $qr_path = null){
  $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

  try {
    $mail->CharSet = 'UTF-8';

    $mail->isSMTP();
    $mail->Host = $cfg['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $cfg['username'];
    $mail->Password = $cfg['password'];
    $mail->Port = (int)$cfg['port'];
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

    $mail->setFrom($cfg['from_email'], $cfg['from_name']);
    $mail->addAddress($to_email, $to_name);

    $mail->isHTML(true);
    $mail->Subject = $subject;

    // QR no email
    if ($qr_path) {
      if (preg_match('~^https?://~i', $qr_path)) {
        // QR é URL público
        $html = str_replace('{{QR_IMAGE}}', $qr_path, $html);
      } elseif (file_exists($qr_path)) {
        // QR é ficheiro local -> embutir
        $mail->addEmbeddedImage($qr_path, 'qrcodecid', 'qrcode.png', 'base64', 'image/png');
        $html = str_replace('{{QR_IMAGE}}', 'cid:qrcodecid', $html);
      } else {
        $html = str_replace('{{QR_IMAGE}}', '', $html);
      }
    } else {
      $html = str_replace('{{QR_IMAGE}}', '', $html);
    }

    $mail->Body = $html;
    $mail->AltBody = strip_tags($html);

    $mail->send();
    return true;

  } catch (\PHPMailer\PHPMailer\Exception $e) {
      echo "<pre>MAIL ERROR: " . $e->getMessage() . "</pre>";
      return false;
  }
}

function build_invitation_html($v){
    $title = htmlspecialchars($v['title'] ?? '');
    $first = htmlspecialchars($v['first_name'] ?? '');
    $full = htmlspecialchars($v['full_name'] ?? '');
    $company = htmlspecialchars($v['company'] ?? '');
    $email = htmlspecialchars($v['email'] ?? '');
    $event = htmlspecialchars($v['event_name'] ?? 'Event');
    $location = htmlspecialchars($v['event_location'] ?? '');
    $event_date = htmlspecialchars($v['event_date'] ?? '');

    $checkin_url = $v['checkin_url'] ?? '';

    return "
      <h2>$event</h2>
      <p>Dear, $title $full,</p>
      <p>Thank you for registering for the $event event.</p>

      <p><b>Participant details:</b><br>
        Name: $full<br>
        Company: $company<br>
        Email: $email
      </p>

      <p><b>Event details:</b><br>
        Name: $event Event<br>
        Location: $location<br>
        Date: $event_date
      </p>


      <p><b>QR Code for entrance:</b></p>
        <p><a href='$checkin_url' target='_blank' rel='noopener noreferrer'>$checkin_url</a></p>
        <img src='{{QR_IMAGE}}' width='220' alt='QR Code'>
        <p>Best regards,<br>The CarVita Team</p>
    ";
}

function build_event_summary_html($v){
  $title = htmlspecialchars($v['title'] ?? '');
  $full = htmlspecialchars($v['full_name'] ?? '');
  $company = htmlspecialchars($v['company'] ?? '');
  $email = htmlspecialchars($v['email'] ?? '');
  $event = htmlspecialchars($v['event_name'] ?? 'Event');
  $location = htmlspecialchars($v['event_location'] ?? '');

  return "
    <h2>$event</h2>
    <p>Dear, $title $full,</p>

    <p><b>Participant details:</b><br>
      Name: $full<br>
      Company: $company<br>
      Email: $email
    </p>

    <p><b>Event details:</b><br>
      Event Name: $event<br>
      Location: $location<br>
    </p>

    <p>Best regards,<br>The CarVita Team</p>
  ";
}

?>
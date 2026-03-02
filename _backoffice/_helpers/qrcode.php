<?php

function generate_qr_token(): string {
  return bin2hex(random_bytes(16));
}

// URL que vai dentro do QR (abre o checkin.php)
function build_checkin_url(string $token): string {
  $base_url = rtrim(getenv('APP_URL') ?: 'https://carvita-event.onrender.com', '/');
  return $base_url . "/checkin.php?code=" . urlencode($token);
}

// Em vez de gerar PNG local, devolve um URL público para o QR
function build_qr_png_path(string $checkin_url, int $size = 220): string {
  $s = max(120, min(600, $size));
  return "https://api.qrserver.com/v1/create-qr-code/?size={$s}x{$s}&data=" . urlencode($checkin_url);
}

?>
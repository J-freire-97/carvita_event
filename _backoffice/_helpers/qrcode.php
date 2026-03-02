<?php

function generate_qr_token(): string {
    return bin2hex(random_bytes(16));
}

// URL que vai dentro do QR (abre o checkin.php)
function build_checkin_url(string $token): string {
    return "http://localhost/teste_1/_backoffice/checkin.php?code=" . urlencode($token);
}

// URL da imagem QR (Google Chart)
function build_qr_image_url(string $checkin_url, int $size = 220): string {
    return "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl=" . urlencode($checkin_url);
}

?>
<?php

function generate_qr_token(): string {
    return bin2hex(random_bytes(16));
}

// URL que vai dentro do QR (abre o checkin.php)
function build_checkin_url(string $token): string {
    $base_url = rtrim(getenv('APP_URL') ?: 'https://carvita-event.onrender.com', '/');
    return $base_url . "/checkin.php?code=" . urlencode($token);
}

// Gera QR Code em PNG local e devolve o caminho do ficheiro
function build_qr_png_path(string $checkin_url, int $size = 8): string {
    // precisa do qrlib.php (o teu ficheiro)
    require_once __DIR__ . '/qrlib.php';

    // /tmp é writeable no Render
    $tmp = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    $file = $tmp . DIRECTORY_SEPARATOR . 'qrcode_' . bin2hex(random_bytes(8)) . '.png';

    // ECC L (low), size=8 costuma ficar bom em email
    QRcode::png($checkin_url, $file, QR_ECLEVEL_L, $size, 2);

    return $file;
}

?>
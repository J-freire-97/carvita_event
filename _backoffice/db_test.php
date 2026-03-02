<?php
header('Content-Type: text/plain; charset=utf-8');

echo "DB_HOST=" . var_export(getenv('DB_HOST'), true) . PHP_EOL;
echo "DB_PORT=" . var_export(getenv('DB_PORT'), true) . PHP_EOL;
echo "DB_NAME=" . var_export(getenv('DB_NAME'), true) . PHP_EOL;
echo "DB_USER=" . var_export(getenv('DB_USER'), true) . PHP_EOL;

echo PHP_EOL . "PDO drivers: " . implode(", ", PDO::getAvailableDrivers()) . PHP_EOL;

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_NAME');

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
echo "DSN=" . $dsn . PHP_EOL;

try {
  $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
  echo "CONNECTED OK" . PHP_EOL;
} catch (Throwable $e) {
  echo "CONNECT ERROR: " . $e->getMessage() . PHP_EOL;
}

?>
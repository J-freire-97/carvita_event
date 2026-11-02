<?php

$base_dados = [
  "host" => getenv('DB_HOST') ?: "HOST",
  "port" => getenv('DB_PORT') ?: "PORT",
  "dbname" => getenv('DB_NAME') ?: "DB_NAME",
  "user" => getenv('DB_USER') ?: "USER",
  "pass" => getenv('DB_PASS') ?: "PASS",
];

$dsn = "mysql:host={$base_dados['host']};port={$base_dados['port']};dbname={$base_dados['dbname']};charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $base_dados["user"], $base_dados["pass"], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  
    // SSL (Railway costuma usar TLS; certificado pode ser self-signed)
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    PDO::MYSQL_ATTR_SSL_CA => null,
  ]);
} catch (PDOException $e) {
  die("DB connection failed: " . $e->getMessage());
}

function select_sql($sql){
  global $pdo;
  $query = $pdo->query($sql);
  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

function select_sql_unic($sql){
  global $pdo;
  $query = $pdo->query($sql);
  $result = $query->fetch(PDO::FETCH_ASSOC);
  return $result;
}

function idu_sql($sql){
  global $pdo;
  $query = $pdo->query($sql);
}

?>

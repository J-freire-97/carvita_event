<?php

$base_dados = [
  "host" => "localhost",
  "dbname" => "car_vita_event",
  "user" => "root",
  "pass" => "",
];

$pdo = new PDO("mysql:host=$base_dados[host];dbname=$base_dados[dbname];charset=utf8mb4;", $base_dados["user"], $base_dados["pass"]);

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
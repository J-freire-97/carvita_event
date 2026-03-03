<?php

session_start();
require_once __DIR__ . '/_helpers/data_base.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM _backoffice WHERE login = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || !password_verify($password, $admin['senha'])) {
  die('Login inválido');
}

session_regenerate_id(true);

$_SESSION['admin'] = [
  'id' => $admin['ID'],
  'name' => $admin['name']
];

header('Location: event.php');
exit;

?>

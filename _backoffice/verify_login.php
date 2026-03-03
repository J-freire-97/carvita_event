<?php

session_start();
require_once '../_helpers/data_base.php';

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

session_start();

function fazer_login($login, $senha){
  global $pdo;

  $query = $pdo->prepare("SELECT * FROM colaboradores WHERE login=?");
  $query->execute([$login]);
  $usuario = $query->fetch(PDO::FETCH_ASSOC);

  if(!empty($usuario) && password_verify($senha, $usuario["senha"])){
    $_SESSION["usuario"] = $usuario;
    return true;
  }
  else{
    return false;
  }
}

function verificar_login(){
  if(!empty($_SESSION["usuario"])){return $_SESSION["usuario"];}
  else{
    header("Location: index.php");
  }
}

function logout(){
  session_destroy();
  header("Location: sair.php");
}
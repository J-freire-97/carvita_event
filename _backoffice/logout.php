<?php

session_start();

// limpa a sessão
$_SESSION = [];
session_destroy();

// redireciona
header("Location: index.php");
exit;

?>
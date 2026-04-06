<?php

session_start();

// clean session
$_SESSION = [];
session_destroy();

// redirect
header("Location: index.php");
exit;

?>
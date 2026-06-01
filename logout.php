<?php
require_once 'conexao.php';
$_SESSION = array();
session_destroy();
header("Location: login.php");
exit;
?>
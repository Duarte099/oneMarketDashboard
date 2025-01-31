<?php
session_start();
include '../db/conexao.php';
include '../db/functions.php';

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
    $idAdministrador = $_SESSION['id'];
    $username = $_SESSION['name'];
    
    $mensagem = "Administrador " . $username . " (" . $idAdministrador . ") Saiu (fechou a página).";
    registrar_log($mensagem);
}
?>

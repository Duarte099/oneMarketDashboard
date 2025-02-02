<?php
    include("./db/conexao.php");
    
    $idAdministrador = $_SESSION['id'];
    $username = $_SESSION['name'];
    
    $mensagem = "Administrador " . $username . "(" . $idAdministrador . ") Saiu.";
    registrar_log($mensagem);
    session_destroy();
    //Redirect to the login page:
    header('Location: index.php');
?>
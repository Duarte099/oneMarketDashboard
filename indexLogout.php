<?php
    //Inclui a base de dados e segurança da pagina
    include("./db/conexao.php");

    //Obtem os dados do administrador, id e nome
    $idAdministrador = $_SESSION['id'];
    $username = $_SESSION['name'];
    
    $mensagem = "Administrador " . $username . "(" . $idAdministrador . ") Saiu.";
    registrar_log($mensagem);
    //Destroi a sessão (logout)
    session_destroy();
    //Redirect to the login page:
    header('Location: index.php');
?>
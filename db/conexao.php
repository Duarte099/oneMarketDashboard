<?php
    global $auxLogin; // Permite acesso à variável externa

    // Configurações de conexão ao banco de dados
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'onemarket';
    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    // Verificação de erro de conexão (opcional)
    if (mysqli_connect_errno()) {
        exit('Erro ao conectar ao MySQL: ' . mysqli_connect_error());
    }

    // Bloco de verificação de sessão (só executa se $auxLogin for false ou não definido)
    if (!isset($auxLogin) || $auxLogin === false) {
        session_start();
        
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header('Location: index.php');
            exit();
        }

        // Verifica se a senha do admin ainda é válida
        $sql = "SELECT * FROM administrator WHERE id = " . $_SESSION['id'] . " AND pass = '" . $_SESSION['password'] . "';";
        $result = $con->query($sql);
        if ($result->num_rows <= 0) {
            header('Location: index.php');
            exit();
        }
    }

    include_once 'functions.php';
?>
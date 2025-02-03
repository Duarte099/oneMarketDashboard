<?php
    global $auxLogin; // Permite acesso à variável externa

    // Configurações de conexão ao banco de dados
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'onemarket';
    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    //codificação
    //$con->set_charset("utf8mb4");

    // Verificação de erro de conexão (opcional)
    if (mysqli_connect_errno()) {
        exit('Erro ao conectar ao MySQL: ' . mysqli_connect_error());
    }

    // Bloco de verificação de sessão (só executa se $auxLogin for false ou não definido)
    if (!isset($auxLogin) || $auxLogin === false) {
        //Cria uma sessão
        session_start();
        
        //Se não tiver logado não deixa entrar na página
        if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
            header('Location: index.php');
            exit();
        }

        // Verificação da password para segurança das paginas
        $sql = "SELECT * FROM administrator WHERE id = " . $_SESSION['id'] . " AND pass = '" . $_SESSION['password'] . "';";
        $result = $con->query($sql);
        if ($result->num_rows <= 0) {
            header('Location: index.php');
            exit();
        }
    }

    //Chama as funções para serem usadas em todas as paginas
    include_once 'functions.php';
?>
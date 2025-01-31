<?php
    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php');

    
    if (adminPermissions("adm_004", "inserir") == 0) {
        header('Location: dashboard.php');
        exit();
    }
        $idClient= $_GET['id'];
        $email = "";
        $nif = "";
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $contacto = $_POST['contacto'];
        $nif = $_POST['nif'];
        $status = $_POST['status'];

        $query = "INSERT INTO client (name, email, contact, nif, active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);

        if ($stmt) {
            $stmt->bind_param("ssssi", $nome, $email, $contacto, $nif, $status);
            if ($stmt->execute()) {
                $idClient = $con->insert_id;
                //funcao log
                $username = $_SESSION['name'];
                $mensagem = "Cliente '$nome' (ID: $idClient) criado pelo administrador de ID $username.";
                registrar_log($mensagem);

                header('Location: cliente.php');
                exit();
            }
        }
?>
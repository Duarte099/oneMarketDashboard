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
            $idClient = $con->insert_id;
            if ($stmt->execute()) {


                header('Location: cliente.php');
                exit();
            }
        }
?>
<?php
    session_start();
    include('../db/conexao.php'); 

    $permission = adminPermissions("adm_004", "inserir");

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $permission == 0) {
        header('Location: index.php');
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

        if ($stmt->execute()) {
            header('Location: cliente.php');
            exit();
        }
    }
?>
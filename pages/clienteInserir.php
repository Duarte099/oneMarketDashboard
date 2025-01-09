<?php
    session_start();
    include('../db/conexao.php'); 

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
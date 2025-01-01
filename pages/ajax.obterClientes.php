<?php 
    session_start();

    include('../db/conexao.php'); 
    $estouEm = 2;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
    
    $sql = "SELECT 
            client.id as idCliente,
            client.name as nomeCliente,
            client.email as emailCliente,
            client.contact as contactoCliente
        FROM client;";

    $data = [];
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'idCliente' => $row['idCliente'], 
                'nomeCliente' => $row['nomeCliente'], 
                'emailCliente' => $row['emailCliente'],
                'contactoCliente' => $row['contactoCliente'],
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
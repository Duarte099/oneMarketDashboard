<?php

    include('../db/conexao.php'); 
    $estouEm = 6;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $id = $_GET['id'];

    $sql = "DELETE FROM administrator WHERE id = $id;";
    $result = $con->prepare($sql);
    $result->execute();
?>

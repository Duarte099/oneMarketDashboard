<?php

    include('../db/conexao.php'); 
    $estouEm = 2;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $idWorksheet = $_GET['idWorksheet'];

    $sql = "DELETE FROM worksheet WHERE id = $idWorksheet;";
    $result = $con->prepare($sql);
    $result->execute();
?>

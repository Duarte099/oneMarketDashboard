<?php

    session_start();
    include('../db/conexao.php'); 
    $estouEm = 3;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    if (adminPermissions("adm_002", "delete") == 0) {
        header('Location: index.php');
        exit();
    }

    $idWorksheet = $_GET['idWorksheet'];

    $sql = "DELETE FROM worksheet WHERE id = $idWorksheet;";
    $result = $con->prepare($sql);
    $result->execute();
?>

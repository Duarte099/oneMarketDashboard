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
    $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
    $result = $con->query($sql);
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }

    $sql = "DELETE FROM worksheet WHERE id = $idWorksheet;";
    $result = $con->prepare($sql);
    $result->execute();
?>

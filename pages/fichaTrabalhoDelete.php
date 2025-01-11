<?php

    session_start();
    include('../db/conexao.php'); 
    $estouEm = 3;

    session_start();

    $permission = adminPermissions("adm_002", "delete");

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $permission == 0) {
        header('Location: index.php');
        exit();
    }

    $idWorksheet = $_GET['idWorksheet'];

    $sql = "DELETE FROM worksheet WHERE id = $idWorksheet;";
    $result = $con->prepare($sql);
    $result->execute();
?>

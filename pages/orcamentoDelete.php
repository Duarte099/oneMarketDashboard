<?php
    session_start();
    include('../db/conexao.php'); 
    $estouEm = 2;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    if (adminPermissions("adm_001", "delete") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idBudget = $_GET['idBudget'];

    $sql = "DELETE FROM budget WHERE id = $idBudget;";
    $result = $con->prepare($sql);
    $result->execute();
?>

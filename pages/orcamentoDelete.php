<?php
    session_start();
    include('../db/conexao.php'); 
    $estouEm = 2;

    $permission = adminPermissions("adm_001", "delete");

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $permission == 0) {
        header('Location: index.php');
        exit();
    }

    $idBudget = $_GET['idBudget'];

    $sql = "DELETE FROM budget WHERE id = $idBudget;";
    $result = $con->prepare($sql);
    $result->execute();
?>

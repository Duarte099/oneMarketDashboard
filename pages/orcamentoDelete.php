<?php
    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php'); 

    if (adminPermissions("adm_001", "delete") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idBudget = $_GET['idBudget'];

    $sql = "SELECT * FROM budget WHERE id = '$idBudget'";
    $result = $con->query($sql);
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }

    $sql = "DELETE FROM budget WHERE id = $idBudget;";
    $result = $con->prepare($sql);
    $result->execute();
?>

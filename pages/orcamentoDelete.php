<?php
    include('../db/conexao.php'); 

    if (adminPermissions($con, "adm_001", "delete") == 0) {
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

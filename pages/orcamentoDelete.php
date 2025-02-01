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
    else {
        $sql = "DELETE FROM budget WHERE id = '$idBudget';";
        $result = $con->prepare($sql);
        $result->execute();
        $sql = "DELETE FROM budget_sections_products WHERE id = '$idBudget';";
        $result = $con->prepare($sql);
        $result->execute();
        $sql = "DELETE FROM budget_versions WHERE idBudget = '$idBudget';";
        $result = $con->prepare($sql);
        $result->execute();
        header('Location: ../pages/orcamento.php');
    }
?>

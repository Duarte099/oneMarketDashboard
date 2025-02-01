<?php
    include('../db/conexao.php'); 

    $estouEm = 3;

    if (adminPermissions($con, "adm_002", "delete") == 0) {
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
    else {
        $sql = "DELETE FROM worksheet WHERE id = $idWorksheet;";
        $result = $con->prepare($sql);
        $result->execute();
        $sql = "DELETE FROM worksheet_versions WHERE idWorksheet = $idWorksheet;";
        $result = $con->prepare($sql);
        $result->execute();
        header('Location: fichaTrabalho.php');
    }
?>

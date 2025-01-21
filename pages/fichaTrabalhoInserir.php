<?php

session_start();
include('../db/conexao.php');
$estouEm = 2;

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}

if (adminPermissions("adm_002", "inserir") == 0 || adminPermissions("adm_002", "update") == 0) {
    header('Location: dashboard.php');
    exit();
}

$idAdmin = $_SESSION['id'];

$readyStorage = $_POST['prontoArmazem'];
$joinWork = $_POST['entradaObra'];
$exitWork = $_POST['saidaObra'];
$observacaoWorksheet = $_POST['observation'];

$op = $_GET['op'];

if ($op == "save") {
    $idBudget = $_GET['idBudget'];

    $anoAtual = date("Y");

    $sql = "SELECT MAX(num) AS maior_numero FROM worksheet WHERE year = $anoAtual;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $proximo_numero = $row['maior_numero'] + 1;
    } else {
        $proximo_numero = 1;
    }

    $sql = "SELECT idClient FROM budget WHERE id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
    }

    $sql = "INSERT INTO worksheet (idBudget, idClient, num, year, readyStorage, joinWork, exitWork, observation, createdBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $result = $con->prepare($sql);

    if ($result) {
        $result->bind_param("iiiissssi", $idBudget, $idClient, $proximo_numero, $anoAtual, $readyStorage, $joinWork, $exitWork, $observacaoWorksheet, $idAdmin);
    }

    $result->execute();
    $idWorksheet = $con->insert_id;

    $sql = "UPDATE `budget` SET idWorksheet = '$idWorksheet' WHERE id = $idBudget";
    $result = $con->prepare($sql);
    $result->execute();

    //secções e produtos
    $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
    $resultSection = $con->query($sqlSection);
    if ($resultSection->num_rows > 0) {
        while ($rowSection = $resultSection->fetch_assoc()) {
            $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $rowSection['orderSection']]);

            $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
            $resultProducts = $con->query($sqlProducts);
            if ($resultProducts->num_rows > 0) {
                while ($rowProducts = $resultProducts->fetch_assoc()){
                    if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']] == "on") {
                        $check = 1;
                    } else {
                        $check = 0;
                    }

                    if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']] == "on") {
                        $storage = 1;
                    } else {
                        $storage = 0;
                    }
                    $ref = $_POST['secao_' . $rowSection['orderSection'] . '_produto_ref_' . $rowProducts['orderProduct']];
                    $observacao = $_POST['secao_' . $rowSection['orderSection'] . '_produto_observacao_' . $rowProducts['orderProduct']];
                    $tamanho = $_POST['secao_' . $rowSection['orderSection'] . '_produto_tamanho_' . $rowProducts['orderProduct']];
                    if (!empty($descricao) || !empty($tamanho)) {
                        $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND orderSection = {$rowSection['orderSection']} AND orderProduct = '{$rowProducts['orderProduct']}';";
                        $result = $con->prepare($sql);
                        $result->execute();

                        $sql = "INSERT INTO worksheet_version (idVersion, idWorksheet, readyStorage, joinWork, exitWork, observation, checkProduct, storageProduct, observationProduct, sizeProduct) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $result = $con->prepare($sql);
                        if ($result) {
                            $idVersion = 1;
                            $result->bind_param("iissssssss", $idVersion, $idWorksheet, $readyStorage, $joinWork, $exitWork, $observacaoWorksheet, $check, $storage, $observacao, $tamanho);
                        }
                        $result->execute();
                    }
                }
            }
        }
    }
    header('Location: ../pages/fichaTrabalhoEdit.php?idWorksheet=' . $idWorksheet);
} elseif ($op == "edit") {
    $idWorksheet = $_GET['idWorksheet'];

    $sql = "SELECT idBudget FROM worksheet WHERE worksheet.id = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idBudget =  $row['idBudget'];
    }

    //cabeçalho
    $sql = "UPDATE `worksheet` SET readyStorage = '$readyStorage', joinWork = '$joinWork', exitWork = '$exitWork' WHERE id = $idWorksheet";
    $result = $con->prepare($sql);
    $result->execute();

    //secções e produtos
    $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
    $resultSection = $con->query($sqlSection);
    if ($resultSection->num_rows > 0) {
        while ($rowSection = $resultSection->fetch_assoc()) {
            $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $rowSection['orderSection']]);

            $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
            $resultProducts = $con->query($sqlProducts);
            if ($resultProducts->num_rows > 0) {
                while ($rowProducts = $resultProducts->fetch_assoc()) {
                    if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']] == "on") {
                        $check = 1;
                    } else {
                        $check = 0;
                    }

                    if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']] == "on") {
                        $storage = 1;
                    } else {
                        $storage = 0;
                    }
                    $ref = $_POST['secao_' . $rowSection['orderSection'] . '_produto_ref_' . $rowProducts['orderProduct']];
                    $observacao = $_POST['secao_' . $rowSection['orderSection'] . '_produto_observacao_' . $rowProducts['orderProduct']];
                    $tamanho = $_POST['secao_' . $rowSection['orderSection'] . '_produto_tamanho_' . $rowProducts['orderProduct']];
                    if (!empty($check) || !empty($storage) || !empty($observacao) || !empty($tamanho)) {
                        $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                        $result = $con->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $idProduct = $row['id'];
                        }
                        $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND idProduct = $idProduct AND orderSection = '{$rowSection['orderSection']}' AND orderProduct = '{$rowProducts['orderProduct']}';";
                        $result = $con->prepare($sql);
                        $result->execute();

                        $sql = "INSERT INTO worksheet_version (idVersion, idWorksheet, readyStorage, joinWork, exitWork, observation, checkProduct, storageProduct, observationProduct, sizeProduct) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $result = $con->prepare($sql);
                        if ($result) {
                            $idVersion = 1;
                            $result->bind_param("iissssssss", $idVersion, $idWorksheet, $readyStorage, $joinWork, $exitWork, $observacaoWorksheet, $check, $storage, $observacao, $tamanho);
                        }
                        $result->execute();
                    }
                }
            }
        }
    }
    header('Location: ../pages/fichaTrabalho.php');
}

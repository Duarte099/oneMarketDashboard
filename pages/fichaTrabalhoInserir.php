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

    $op = $_GET['op'];

    if ($op == "save") {
        print_r($_POST);
        $idBudget = $_GET['idBudget'];

        $anoAtual = date("Y");

        $sql = "SELECT MAX(num) AS maior_numero FROM worksheet WHERE year = $anoAtual;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $proximo_numero = $row['maior_numero'] + 1;
        }
        else {
            $proximo_numero = 1;
        }

        $sql = "SELECT idClient FROM budget WHERE id = $idBudget;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $idClient =  $row['idClient'];
        }

        $sql = "INSERT INTO worksheet (idBudget, idClient, num, year, readyStorage, joinWork, exitWork, createdBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $result = $con->prepare($sql);

        if ($result) {
            $result->bind_param("iiiisssi", $idBudget, $idClient, $proximo_numero, $anoAtual, $readyStorage, $joinWork, $exitWork, $idAdmin);
        }

        $result->execute();
        $idWorksheet = $con->insert_id;

        $sql = "UPDATE `budget` SET idWorksheet = '$idWorksheet' WHERE id = $idBudget";
        $result = $con->prepare($sql);
        $result->execute();

        $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $numSections =  $row['numSections'];
        }

        //secções e produtos
        for ($i=1; $i <= $numSections; $i++) {
            $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $i]);

            $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $numProducts = $row['numProducts'];
            }

            for ($j=1; $j <= $numProducts; $j++) {
                if (isset($_POST['secao_' . $i . '_produto_check_' . $j]) && $_POST['secao_' . $i . '_produto_check_' . $j] == "on") {
                    $check = 1;
                } else {
                    $check = 0;
                }
                
                if (isset($_POST['secao_' . $i . '_produto_armazem_' . $j]) && $_POST['secao_' . $i . '_produto_armazem_' . $j] == "on") {
                    $storage = 1;
                } else {
                    $storage = 0;
                }
                $ref = $_POST['secao_' . $i . '_produto_ref_' . $j];
                $observacao = $_POST['secao_' . $i . '_produto_observacao_' . $j];
                $tamanho = $_POST['secao_' . $i . '_produto_tamanho_' . $j];
                if(!empty($descricao) || !empty($tamanho)) {
                    $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                    $result = $con->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $idProduct = $row['id'];
                    }
                    $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND idProduct = $idProduct AND orderSection = $i AND orderProduct = $j;";
                    $result = $con->prepare($sql);
                    $result->execute();
                }
            }
        }
    }
    elseif ($op == "edit") {
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

        $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $numSections =  $row['numSections'];
        }

        //secções e produtos
        for ($i=1; $i <= $numSections; $i++) {
            $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $i]);

            $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $numProducts = $row['numProducts'];
            }

            for ($j=1; $j <= $numProducts; $j++) {
                if (isset($_POST['secao_' . $i . '_produto_check_' . $j]) && $_POST['secao_' . $i . '_produto_check_' . $j] == "on") {
                    $check = 1;
                } else {
                    $check = 0;
                }
                
                if (isset($_POST['secao_' . $i . '_produto_armazem_' . $j]) && $_POST['secao_' . $i . '_produto_armazem_' . $j] == "on") {
                    $storage = 1;
                } else {
                    $storage = 0;
                }
                $ref = $_POST['secao_' . $i . '_produto_ref_' . $j];
                $observacao = $_POST['secao_' . $i . '_produto_observacao_' . $j];
                $tamanho = $_POST['secao_' . $i . '_produto_tamanho_' . $j];
                if(!empty($check) || !empty($storage) || !empty($observacao) || !empty($tamanho)) {
                    $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                    $result = $con->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $idProduct = $row['id'];
                    }
                    $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND idProduct = $idProduct AND orderSection = $i AND orderProduct = $j;";
                    $result = $con->prepare($sql);
                    $result->execute();
                }
            }
        }
    }
    header('Location: ../pages/fichasTrabalho.php');
?>
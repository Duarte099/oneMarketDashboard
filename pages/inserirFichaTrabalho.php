<?php

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    // print_r($_POST);

    $estouEm = 2;

    include("../db/conexao.php");

    $idAdmin = $_SESSION['id'];

    $readyStorage = $_POST['prontoArmazem'];
    $joinWork = $_POST['entradaObra'];
    $exitWork = $_POST['saidaObra'];

    $op = $_GET['op'];

    if ($op == "save") {
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

        $sql = "INSERT INTO worksheet (idBudget, num, year, readyStorage, joinWork, exitWork, createdBy) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $result = $con->prepare($sql);

        if ($result) {
            $result->bind_param("iiiisssi", $idBudget, $proximo_numero, $anoAtual, $readyStorage, $joinWork, $exitWork, $idAdmin);
        }

        $result->execute();
        $idWorksheet = $con->insert_id;

        $sql = "UPDATE `budget` SET idWorksheet = '$idWorksheet' WHERE id = $idBudget";
        $result = $con->prepare($sql);
        $result->execute();

        header('Location: ../pages/fichasTrabalho.php');
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

        $sql = "SELECT COUNT(*) AS numSections FROM budget_sections_products WHERE budget_sections_products.idBudget = $idBudget AND idProduct = 0;";
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
            for ($j=1; $j <= 10; $j++) {
                if ($j <= $numProducts) {
                    $ref = $_POST['secao_' . $i . '_produto_ref_' . $j];
                    $descricao = $_POST['secao_' . $i . '_produto_descricao_' . $j];
                    $tamanho = $_POST['secao_' . $i . '_produto_tamanho_' . $j];
                    if(!empty($descricao) || !empty($tamanho)) {
                        $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                        $result = $con->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $idProduct = $row['id'];
                        }
                        $sql = "UPDATE budget_sections_products SET descriptionProduct = '$descricao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND idProduct = $idProduct AND orderSection = $i AND orderProduct = $j;";
                        $result = $con->prepare($sql);
                        $result->execute();
                    }
                }
            }
        }
        header('Location: ../pages/fichasTrabalho.php');
    }
?>
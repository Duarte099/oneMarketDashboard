<?php

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    // print_r($_POST);

    $estouEm = 2;

    include("../db/conexao.php");
    $numProjeto = $_POST['numOrcamento'];
    $nameBudget = $_POST['nomeProjeto'];
    $idAdmin = $_SESSION['id'];

    $op = $_GET['op'];

    if ($op == "save") {
        $anoAtual = date("Y");

        $sql = "SELECT MAX(num) AS maior_numero FROM budget WHERE year = $anoAtual;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $proximo_numero = $row['maior_numero'] + 1;
        }
        else {
            $proximo_numero = 1;
        }

        $idClient = $_GET['idClient'];

        $sql = "INSERT INTO budget (idClient, name, num, year, createdBy) VALUES (?, ?, ?, ?, ?)";

        $result = $con->prepare($sql);

        if ($result) {
            $result->bind_param("issii", $idClient, $nameBudget, $proximo_numero, $anoAtual, $idAdmin);
        }

        $result->execute();
        $idBudget = $con->insert_id;

        //Inserir secções
        for ($i=1; $i <= 20; $i++) {
            $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $i]);
            if (!empty($secao)) {
                $sqlSelect = "SELECT budget_sections.id, budget_sections.name FROM budget_sections WHERE budget_sections.name = '$secao'";
                $result = $con->query($sqlSelect);
                if (!($result->num_rows > 0)) {
                    $sqlInsert = "INSERT INTO budget_sections (name) VALUES ('$secao');";
                    $result = $con->prepare($sqlInsert);
                    $result->execute();
                }
                $result = $con->query($sqlSelect);
                $row = $result->fetch_assoc();
                $idSecao = $row['id'];
                $sql = "INSERT INTO budget_sections_products (idBudget, idSection, orderSection) VALUES ($idBudget, $idSecao, $i);";
                $result = $con->prepare($sql);
                $result->execute();
            }
        }

        header('Location: ../pages/orcamentos.php');
    }
    elseif ($op == "edit") {
        $idBudget = $_GET['idBudget'];
        //cabeçalho
        $sql = "UPDATE `budget` SET name = '$nameBudget' WHERE id = $idBudget";
        $result = $con->prepare($sql);
        $result->execute();

        //secções e produtos
        for ($i=1; $i <= 20; $i++) {
            $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $i]);
            if (!empty($secao)) {
                $sqlSelect = "SELECT budget_sections.id, budget_sections.name FROM budget_sections WHERE budget_sections.name = '$secao'";
                $result = $con->query($sqlSelect);
                if (!($result->num_rows > 0)) {
                    $sqlInsert = "INSERT INTO budget_sections (name) VALUES ('$secao');";
                    $result = $con->prepare($sqlInsert);
                    $result->execute();
                }
                $result = $con->query($sqlSelect);
                $row = $result->fetch_assoc();
                $idSecao = $row['id'];
                for ($j=1; $j <= 10; $j++) {
                    $ref = trim($_POST['secao_' . $i . '_produto_ref_' . $j]);
                    $designacao = $_POST['secao_' . $i . '_produto_designacao_' . $j];
                    $quantidade = $_POST['secao_' . $i . '_produto_quantidade_' . $j];
                    $descricao = $_POST['secao_' . $i . '_produto_descricao_' . $j];
                    $precoUnitario = trim($_POST['secao_' . $i . '_produto_preco_unitario_' . $j]);
                    if(!empty($ref) && !empty($designacao) && !empty($descricao) && !empty($precoUnitario)) {
                        $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                        $result = $con->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $idProduct = $row['id'];
                        }
                        $sql = "INSERT INTO budget_sections_products (idBudget, idSection, orderSection, idProduct, orderProduct, refProduct, nameProduct, amountProduct, descriptionProduct, valueProduct) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
                        $result = $con->prepare($sql);
                        $result->bind_param("iiiiissisd", $idBudget, $idSecao, $i, $idProduct, $j, $ref, $designacao, $quantidade, $descricao, $precoUnitario);
                        $result->execute();
                    }
                }
            }
        }
        header('Location: ../pages/orcamentos.php');
    }
?>
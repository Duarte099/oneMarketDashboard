<?php
    session_start();

    include('../db/conexao.php'); 

    $op = $_GET['op'];

    $imagem = '';
    $nome = $_POST['name'];
    $ref = $_POST['ref'];
    $valor = $_POST['value'];
    $stock = $_POST['quantity'];
    $status = $_POST['status'];

    if ($op=="save") {
        if (!empty($nome) && !empty($ref) && !empty($valor)) {
            $query = "INSERT INTO product (img, name, reference, value, active) VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            if ($stmt) {
                $stmt->bind_param("sssdi", $imagem, $nome, $ref, $valor, $status);
    
                $stmt->execute();
                $idProduct = $con->insert_id;
            }
            $query = "INSERT INTO product_stock (idProduct, quantity) VALUES (?, ?)";
            $stmt = $con->prepare($query);
            if ($stmt) {
                $stmt->bind_param("ii", $idProduct, $stock);
    
                if ($stmt->execute()) {
                    header('Location: ../pages/produto.php');
                    exit();
                }
            }
        }
    }
    else if ($op=="edit") {
        if (!empty($id)) {
            // Atualiza os dados na base de dados
            $updateQuery = "UPDATE product SET img = ?, name = ?, reference = ?, value = ?, active = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("sssdis", $img, $name, $reference, $valor, $status, $id);

            if ($stmt->execute()) {
                // Atualiza os dados na tabela product_stock
                $updateStockQuery = "UPDATE product_stock SET quantity = ? WHERE idProduct = ?";
                $stockStmt = $con->prepare($updateStockQuery);
                $stockStmt->bind_param("ii", $stock, $id);
                
                if ($stmtStock->execute()) {
                    header('Location: ../pages/produto.php');
                    exit();
                } else {
                    $error = "Erro ao atualizar o produto.";
                }
            }   
        }
    }
?>
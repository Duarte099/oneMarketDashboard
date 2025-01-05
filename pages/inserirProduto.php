<?php
    include('../db/conexao.php'); 
    session_start();

    $imagem = $_POST['img'];
    $nome = $_POST['name'];
    $ref = $_POST['reference'];
    $valor = $_POST['value'];
    $stock = $_POST['stock'];

    if (!empty($nome) && !empty($ref) && !empty($valor)) {
        $query = "INSERT INTO product (img, name, reference, value) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        if ($stmt) {
            $stmt->bind_param("sssd", $imagem, $nome, $ref, $valor);

            $stmt->execute();
            $idProduct = $con->insert_id;
        }
        $query = "INSERT INTO product_stock (idProduct, quantity) VALUES (?, ?)";
        $stmt = $con->prepare($query);
        if ($stmt) {
            $stmt->bind_param("id", $idProduct, $stock);

            if ($stmt->execute()) {
                header('Location: ../pages/stock.php');
                exit();
            }
        }
    }
?>
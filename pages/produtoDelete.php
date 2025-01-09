<?php
    session_start();

    include('../db/conexao.php'); 
    $estouEm = 4;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $id = $_GET['idProduct'];

    $sql = "DELETE FROM product WHERE id = $id;";
    $result = $con->prepare($sql);
    $result->execute();
    $sql = "DELETE FROM product_stock WHERE idProduct = $id;";
    $result = $con->prepare($sql);
    $result->execute();
?>
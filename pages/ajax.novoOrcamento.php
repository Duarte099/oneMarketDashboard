<?php
    include('../db/conexao.php');
    
    $nomeProduto = '';
    $valorProduto = '';

    if (isset($_GET['referencia'])) {
        $referencia = $_GET['referencia'];
    }
    else{
        header('Location: dashboard.php');
        exit();
    }
    if (!empty($referencia)) {
        $sql = "SELECT product.name, product.value FROM product WHERE product.reference = '$referencia' LIMIT 1";
        $result = $con->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nomeProduto = $row['name'];
            $valorProduto = $row['value'];
        }
    }

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }
    else{
        header('Location: dashboard.php');
        exit();
    }
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if ($action === 'getName') {
            echo $nomeProduto;
        } elseif ($action === 'getValue') {
            echo $valorProduto;
        }
        exit;
    }
?>

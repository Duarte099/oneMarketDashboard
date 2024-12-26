<?php 

    session_start();

    include('../db/conexao.php'); 
    $estouEm = 2;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
    
    $referencia = isset($_GET['referencia']) ? $con->real_escape_string($_GET['referencia']) : '';
    $nomeProduto = '';
    $valorProduto = '';

    if (!empty($referencia)) {
        $sql = "SELECT product.name, product.value FROM product WHERE product.reference = '$referencia' LIMIT 1";
        $result = $con->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nomeProduto = $row['name'];
            $valorProduto = $row['value'];
        }
    }

    $action = isset($_GET['action']) ? $_GET['action'] : '';
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        if ($action === 'getName') {
            echo $nomeProduto;
        } elseif ($action === 'getValue') {
            echo $valorProduto;
        }
        exit;
    }
?>

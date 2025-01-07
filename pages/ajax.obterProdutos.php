<?php 
    session_start();

    include('../db/conexao.php'); 

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
    
    $sql = "SELECT id, img, reference, name, value, quantity FROM product
            INNER JOIN product_stock ON id = idProduct;";
    $data = [];
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'img' => $row['img'],
                'id' => $row['id'], 
                'reference' => $row['reference'], 
                'name' => $row['name'],
                'value' => $row['value'],
                'stock' => $row['quantity'],
            ];
        }
    }
    echo json_encode($data);
?>
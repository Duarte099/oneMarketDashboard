<?php
    //Inclui a base de dados e a segurança da pagina 
    include('./db/conexao.php'); 
    
    //query sql para obter todos os clientes e os respetivos dados
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
    
    //Envia os dados via json 
    echo json_encode($data);
?>
<?php
    //Inclui a base de dados e a segurança da pagina 
    include('./db/conexao.php');
    
    //query sql para obter todas as logs e os respetivos dados
    $sql = "SELECT dataLog, logFile FROM administrator_logs;";

    $data = [];
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'data' => $row['dataLog'], 
                'log' => $row['logFile'],
            ];
        }
    }

    //Envia os dados via json 
    header('Content-Type: application/json');
    echo json_encode($data);
?>
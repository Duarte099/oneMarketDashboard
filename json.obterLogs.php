<?php
    include('./db/conexao.php');
    
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

    header('Content-Type: application/json');
    echo json_encode($data);
?>
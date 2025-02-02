<?php
    include('./db/conexao.php');
    
    $sql = "SELECT 
        client.name as nomeCliente, 
        client.contact as contactoCliente, 
        budget.id as idBudget,
        budget.num as numBudget,
        budget.year as yearBudget, 
        worksheet.id as idWorksheet,
        worksheet.num as numWorksheet,
        worksheet.year as yearWorksheet, 
        worksheet.readyStorage, 
        worksheet.joinWork, 
        worksheet.exitWork, 
        administrator.name as nomeAdministrador
    FROM worksheet
    LEFT JOIN 
        client ON worksheet.idClient = client.id
    LEFT JOIN 
        administrator ON worksheet.createdBy = administrator.id
    LEFT JOIN 
        budget ON worksheet.idBudget = budget.id
    ORDER BY idWorksheet DESC;";

    $data = [];
    $result = $con->query($sql);



    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'idWorksheet' => $row['idWorksheet'],
                'idBudget' => $row['idBudget'],
                'numWorksheet' => $row['numWorksheet'] . "/" . $row['yearWorksheet'],
                'nomeCliente' => $row['nomeCliente'],
                'contactoCliente' => $row['contactoCliente'],
                'numBudget' => $row['numBudget'] . "/" . $row['yearBudget'],
                'readyStorage' => $row['readyStorage'],
                'joinWork' => $row['joinWork'],
                'exitWork' => $row['exitWork'],
                'responsavel' => $row['nomeAdministrador'],
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
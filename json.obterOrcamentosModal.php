<?php
    //Inclui a base de dados e a segurança da pagina 
    include('./db/conexao.php');
    
    //query sql para obter todos os orçamentos e os respetivos dados
    $sql = "SELECT 
                budget.id as idBudget,
                budget.num as numBudget,
                budget.year as yearBudget,
                client.name as nomeCliente, 
                client.contact as contactoCliente, 
                worksheet.num as numWorksheet, 
                worksheet.year as yearWorksheet, 
                budget.created as dataCriacao,
                administrator.name as responsavel
            FROM budget
            LEFT JOIN 
                client ON budget.idClient = client.id
            LEFT JOIN 
                administrator ON budget.createdBy = administrator.id
            LEFT JOIN 
                worksheet ON budget.idWorksheet = worksheet.id
            WHERE idWorksheet IS NULL 
            ORDER BY idbudget DESC;";

    $data = [];
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if (isset($row['numWorksheet'])) {
                $numWorksheet = $row['numWorksheet'] . "/" . $row['yearWorksheet'];
            }
            else{
                $numWorksheet = '';
            }
            $data[] = [
                'idBudget' => $row['idBudget'],
                'numBudget' => $row['numBudget'] . "/" . $row['yearBudget'], 
                'nomeCliente' => $row['nomeCliente'],
                'contactoCliente' => $row['contactoCliente'],
                'numWorksheet' => $numWorksheet, 
                'dataCriacao' => $row['dataCriacao'],
                'responsavel' => $row['responsavel'],
            ];
        }
    }

    //Envia os dados via json 
    header('Content-Type: application/json');
    echo json_encode($data);
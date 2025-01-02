<?php 
    session_start();

    include('../db/conexao.php'); 
    $estouEm = 2;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
    
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

    header('Content-Type: application/json');
    echo json_encode($data);
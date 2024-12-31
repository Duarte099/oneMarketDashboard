<?php 
    session_start();

    include('../db/conexao.php'); 
    $estouEm = 2;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
    
    $sql = "SELECT 
            budget.num as numBudget,
            budget.year as yearBudget,
            client.name as nomeCliente,  
            administrator.name as responsavel
        FROM budget
        LEFT JOIN 
            client ON budget.idClient = client.id
        LEFT JOIN 
            administrator ON budget.createdBy = administrator.id
        ORDER BY budget.num DESC, budget.year DESC;";

    $data = [];
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'numBudget' => $row['numBudget'] . "/" . $row['yearBudget'], 
                'nomeCliente' => $row['nomeCliente'],
                'responsavel' => $row['responsavel'],
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
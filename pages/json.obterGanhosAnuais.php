<?php
    include('../db/conexao.php');

    $anoAtual = date('Y');
    $ano = isset($_GET['ano']) ? (int) $_GET['ano'] : $anoAtual;

    // Verifica se o ano é válido (apenas ano atual ou ano anterior)
    if ($ano < $anoAtual - 1 || $ano > $anoAtual) {
        echo json_encode(['error' => 'Ano inválido.']);
        exit;
    }

    $sql = "
    SELECT
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 1 AND YEAR(pe.created) = $ano) AS JANE,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 2 AND YEAR(pe.created) = $ano) AS FEVE,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 3 AND YEAR(pe.created) = $ano) AS MARC,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 4 AND YEAR(pe.created) = $ano) AS ABRI,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 5 AND YEAR(pe.created) = $ano) AS MAIO,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 6 AND YEAR(pe.created) = $ano) AS JUNH,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 7 AND YEAR(pe.created) = $ano) AS JULH,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 8 AND YEAR(pe.created) = $ano) AS AGOS,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 9 AND YEAR(pe.created) = $ano) AS SETE,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 10 AND YEAR(pe.created) = $ano) AS OUTR,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 11 AND YEAR(pe.created) = $ano) AS NOVE,
        (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
            INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 12 AND YEAR(pe.created) = $ano) AS DEZE;
    ";

    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['ano' => $ano, 'data' => $data]);
    } else {
        echo json_encode(['ano' => $ano, 'data' => []]);
    }
?>

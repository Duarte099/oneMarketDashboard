<?php
include('../db/conexao.php'); 

// Pega o ano atual
$anoAtual = date('Y'); 


$sql = "
SELECT
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 1 AND YEAR(pe.created) = $anoAtual) AS JANE,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 2 AND YEAR(pe.created) = $anoAtual) AS FEVE,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 3 AND YEAR(pe.created) = $anoAtual) AS MARC,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 4 AND YEAR(pe.created) = $anoAtual) AS ABRI,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 5 AND YEAR(pe.created) = $anoAtual) AS MAIO,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 6 AND YEAR(pe.created) = $anoAtual) AS JUNH,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 7 AND YEAR(pe.created) = $anoAtual) AS JULH,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 8 AND YEAR(pe.created) = $anoAtual) AS AGOS,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 9 AND YEAR(pe.created) = $anoAtual) AS SETE,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 10 AND YEAR(pe.created) = $anoAtual) AS OUTR,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 11 AND YEAR(pe.created) = $anoAtual) AS NOVE,
    (SELECT IFNULL(SUM(valueProduct * amountProduct),0) FROM budget_sections_products p 
        INNER JOIN budget pe ON p.idBudget = pe.id WHERE MONTH(pe.created) = 12 AND YEAR(pe.created) = $anoAtual) AS DEZE;
";

$result = $con->query($sql);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode(['ano' => $anoAtual, 'data' => $data]);
} else {
    echo json_encode(['ano' => $anoAtual, 'data' => []]);
}
?>

<?php 

    session_start();

    include('../db/conexao.php'); 
    $estouEm = 2;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
    
    $sql = "SELECT name FROM budget_sections;";
    $data = [];
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row['name'];
        }
    }
    echo json_encode($data);
?>
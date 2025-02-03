<?php
    //Inclui a conexão à base de dados nesta página
    include('./db/conexao.php'); 

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_002", "delete") == 0) {
        header('Location: index.php');
        exit();
    }

    //Obtem o id da ficha de trabalho a ser eliminada via GET
    $idWorksheet = $_GET['idWorksheet'];
    
    //Query sql para selecionar a ficha de trabalho com o id recebido
    $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
    $result = $con->query($sql);
    //Se não houver ficha de trabalho com esse id redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    //Caso haja uma ficha de trabalho com esse id então elimina a ficha de trabalho e as suas versões
    else {
        $sql = "DELETE FROM worksheet WHERE id = $idWorksheet;";
        $result = $con->prepare($sql);
        $result->execute();
        $sql = "DELETE FROM worksheet_versions WHERE idWorksheet = $idWorksheet;";
        $result = $con->prepare($sql);
        $result->execute();
        header('Location: fichaTrabalho.php');
    }
?>

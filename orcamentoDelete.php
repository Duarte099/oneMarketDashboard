<?php
    //Inclui a conexão à base de dados nesta página
    include('./db/conexao.php'); 

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_001", "delete") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Obtem o id do orçamento a ser eliminada via GET
    $idBudget = $_GET['idBudget'];

    //Query sql para selecionar o orçamento com o id recebido
    $sql = "SELECT * FROM budget WHERE id = '$idBudget'";
    $result = $con->query($sql);
    //Se não houver orçamentos com esse id redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    //Caso haja um orçamento com esse id então elimina a orçamento, as secções, os produtos e as suas versões
    else {
        $sql = "DELETE FROM budget WHERE id = '$idBudget';";
        $result = $con->prepare($sql);
        $result->execute();
        $sql = "DELETE FROM budget_sections_products WHERE id = '$idBudget';";
        $result = $con->prepare($sql);
        $result->execute();
        $sql = "DELETE FROM budget_versions WHERE idBudget = '$idBudget';";
        $result = $con->prepare($sql);
        $result->execute();
        header('Location: orcamento.php');
    }
?>

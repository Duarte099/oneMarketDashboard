<?php
    //inclui a base de dados nesta pagina
    include('./db/conexao.php');
    
    //inicia as variaveis
    $nomeProduto = '';
    $valorProduto = '';

    //Pega a referencia via GET, caso não tenha referencia redireciona para a dashboard
    if (isset($_GET['referencia'])) {
        $referencia = $_GET['referencia'];
    }
    else{
        header('Location: dashboard.php');
        exit();
    }
    //Se a referencia tiver não tiver vazia então
    if (!empty($referencia)) {
        $sql = "SELECT product.name, product.value FROM product WHERE product.reference = '$referencia' LIMIT 1";
        $result = $con->query($sql);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            //Pega o nome e o valor do produto com a referencia recebida
            $nomeProduto = $row['name'];
            $valorProduto = $row['value'];
        }
    }

    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }
    else{
        header('Location: dashboard.php');
        exit();
    }
    // Verifica se a requisição foi feita via AJAX
    // A variável HTTP_X_REQUESTED_WITH é definida pelos navegadores quando uma requisição AJAX é feita
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        //envia os dados
        if ($action === 'getName') {
            echo $nomeProduto;
        } elseif ($action === 'getValue') {
            echo $valorProduto;
        }
        exit;
    }
?>

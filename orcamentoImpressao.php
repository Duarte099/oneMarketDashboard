<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php');

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_001", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Obtem o id do orçamento a ser imprimido via GET
    $idBudget = $_GET['idBudget'];

    //Seleciona o orçamento cujo id é igual ao inserido
    $sql = "SELECT * FROM budget WHERE budget.id = $idBudget;";
    $result = $con->query($sql);
    //Se retornar resultados obtem os dados do orçamento
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
        $nameBudget = $row['name'];
        $maoObraBudget = $row['laborPercent'];
        $descontoBudget = $row['discountPercent'];
        $observacao = $row['observation'];
        $numOrçamento = "$numBudget/$yearBudget";
    } 
    //Caso não retorne resultados redireciona para a dashboard
    else {
        header('Location: dashboard.php');
        exit();
    }

    //Obtem o numero total de secções do orçamento a ser imprimido
    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }

    //Inicia a variavel que irá conter o total do orçamento como 0 para poder calcular
    $totalBudget = 0;

    //Seleciona todos os produtos, valor e quantidade deste orçamento
    $sql = "SELECT valueProduct, amountProduct FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    //Percorre todos produtos, multiplica o valor do produto pela quantidade e adiciona o resultado ao valor total
    while ($row = $result->fetch_assoc()) {
        $totalBudget = $totalBudget + $row['valueProduct'] * $row['amountProduct'];
    }

    //Obtem os dados do cliente associado ao orçamento a ser imprimido
    $sql = "SELECT name, contact FROM client WHERE client.id = $idClient;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clientName = $row['name'];
        $clientContact = $row['contact'];
    }
?>
    <title>OneMarket | Impressão Orçamento</title>
    <link rel="stylesheet" href="./css/orcamentoImpressao.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>ORÇAMENTO n. <?php echo $numOrçamento?></h1>
        </div>

        <div class="info">
            <div>
                <p><strong>Cliente:</strong> <?php echo $clientName?></p>
                <p><strong>Projeto:</strong> <?php echo $nameBudget?></p>
            </div>
            <div>
                <p><strong>Data:</strong> <?php echo date('Y-m-d');?></p>
                <p><strong>Contato:</strong> <?php echo $clientContact?></p>
            </div>
        </div>

        <?php 
            //Inicia a variavel produtosIndex como 0 para ser incrementada
            $produtosIndex = 0; 

            //Ciclo for que itera pelo numero de secções existentes
            for ($i=1; $i <= $numSections; $i++) {
                //Obtem o numero de produtos da secção atual 
                $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $numProducts = $row['numProducts'];
                }

                //Obtem o nome da secção atual
                $sql = "SELECT nameSection FROM budget_sections_products WHERE orderSection = $i AND idBudget = $idBudget;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $nomeSecao = $row['nameSection'];
                }?>
                <div class="section">
                    <div class="section-title"><?php echo $nomeSecao; ?></div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>N/REF</th>
                                <th>Designação</th>
                                <th>Quant</th>
                                <th>Descrição</th>
                                <th>P. Unit</th>
                                <th>P. Total</th>
                            </tr>
                        </thead>
                        <?php 
                            //Percorre todos os produtos desta secção 
                            for ($j=1; $j <= $numProducts; $j++) { 
                                //Incrementa o index dos produtos
                                $produtosIndex++;
                                ?>
                                    <tbody>
                                        <tr>
                                            <?php 
                                                //Obtem os dados do produto atual
                                                $sql = "SELECT refProduct, nameProduct, amountProduct, descriptionProduct, valueProduct FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderProduct = '$j' AND orderSection = '$i';";
                                                $result = $con->query($sql);
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $refProduct = $row['refProduct'];
                                                        $nameProduct = $row['nameProduct'];
                                                        $amountProduct = $row['amountProduct'];
                                                        $descriptionProduct = $row['descriptionProduct'];
                                                        $valueProduct = $row['valueProduct'];
                                                    }
                                                }
                                            ?>
                                            <td><?php echo $produtosIndex;?></td>
                                            <td><?php echo $refProduct;?></td>
                                            <td><?php echo $nameProduct;?></td>
                                            <td><?php echo $amountProduct;?></td>
                                            <td><?php echo $descriptionProduct;?></td>
                                            <td><?php echo $valueProduct;?></td>
                                            <td><?php echo $amountProduct * $valueProduct . "€";?></td>
                                        </tr>
                                    </tbody>
                                <?php 
                            }
                        ?>
                    </table>
                </div>
            <?php } 
        ?>

        <div class="footer">
            <div class="footer-row">
                <div class="footer-cell label">Deslocações e montagens <span style="padding-left: 100px;"><?php echo $maoObraBudget . "%"; ?></span></div>
                <div class="footer-cell value"><?php echo $totalBudget * ($maoObraBudget / 100) . "€"; ?></div>
            </div>
            <div class="footer-row">
                <div class="footer-cell label">TOTAL</div>
                <div class="footer-cell value"><?php echo $totalBudget . "€";?></div>
            </div>
            <div class="footer-row">
                <div class="footer-cell label" style="margin-bottom: 10px;">C/ desconto 5%</div>
                <div class="footer-cell value" style="margin-bottom: 10px;"><?php echo $totalBudget * ($descontoBudget / 100) . "€";?></div>
            </div>
            <strong>Observações:</strong>
            <div class="observacoes">
                <?php echo $observacao; ?>
            </div>
        </div>
    </div>
</body>
</html>
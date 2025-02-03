<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php');
    
    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 3;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_002", "inserir") == 0) {
        header('Location: index.php');
        exit();
    }

    //Obtem o id do orçamento a ser editado via GET
    $idBudget = $_GET['idBudget'];

    //Obtem o id do adminitrador que esta logado
    $idAdmin = $_SESSION['id'];

    //Seleciona o orçamento cujo id é igual ao que recebemos via GET
    $sql = "SELECT * FROM budget WHERE id = '$idBudget'";
    $result = $con->query($sql);
    //Se não houver 1 administrador com esse id então redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    //Caso contrario obtem os dados do orçamento
    else {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
    }

    //Seleciona o orçamento cujo id é igual ao que recebemos via GET e o id da ficha de trabalho é nulo
    $sql = "SELECT idWorksheet FROM budget WHERE id = '$idBudget' AND idWorksheet IS NULL;";
    $result = $con->query($sql);
    //Se retonar não retornar resultados significa que este orçamento ja tem uma ficha de trabalho associada então redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    
    //forma o numero do orçamento que vem da junção do numero dele com o ano em que foi criado
    $numOrcamento = "$numBudget/$yearBudget";
    
    //Obtem os dados do cliente 
    $sql = "SELECT name, contact FROM client WHERE id = $idClient;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameClient =  $row['name'];
        $contactClient = $row['contact'];
    }

    //Obtem os dados do administrador
    $sql = "SELECT name FROM administrator WHERE id = $idAdmin;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameAdministrator =  $row['name'];
    }

    //Obtem o ano atual
    $anoAtual = date('Y');

    //Obtem o maior numero de todas as fichas de trabalho cujo ano é o atual para incrementar 1 e obter o numero seguinte
    $sql = "SELECT MAX(num) AS maior_numero FROM worksheet WHERE year = $anoAtual;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $proximo_numero = $row['maior_numero'] + 1;
    }
    //Se não houver ainda fichas de trabalho este ano então o numero é 1 
    else {
        $proximo_numero = 1;
    }
    //forma o numero previsto para a ficha de trabalho que vem da junção do maior numero +1 com o ano atual
    $numFicha = "$proximo_numero/$anoAtual";
?>
    <link rel="stylesheet" href="./css/novaFichaTrabalho.css">
    <title>OneMarket | Nova Ficha de Trabalho</title>
</head>

<body>

    <?php 
        //inclui a sideBar na página
        include('sideBar.php'); 
    ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php 
            //Inclui o header na página
            include('header.php'); 
        ?>          
        <!-- End of Navbar -->
        <main>
            <div class="header">
                <div class="left">
                    <h1>Nova Ficha de Trabalho</h1>
                </div>
            </div>

            <form action="fichaTrabalhoInserir.php?idBudget=<?= $idBudget ?>&op=save" method=post>
                <div class="bottom-data">
                    <div class="worksheet">
                        <section>
                            <h2>Dados da Ficha de Trabalho</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Ficha Numero:</label>
                                    <input type="text" name="numFicha" required readonly value="<?php echo $numFicha; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Cliente:</label>
                                    <input type="text" name="cliente" id="new-budget" required readonly value="<?php echo $nameClient; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="text" name="contacto" required readonly value="<?php echo $contactClient; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Orçamento:</label>
                                    <input type="text" name="orcamento" required readonly value="<?php echo $numOrcamento; ?>">
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Pronto em armazém:</label>
                                    <input type="date" name="prontoArmazem">
                                </div>
                                <div class="section-group">
                                    <label>Entrada em obra:</label>
                                    <input type="date" name="entradaObra">
                                </div>
                                <div class="section-group">
                                    <label>Saída de obra:</label>
                                    <input type="date" name="saidaObra">
                                </div>
                                <div class="section-group">
                                    <label>Elaborado por:</label>
                                    <input type="text" name="createdby" readonly required value="<?php echo $nameAdministrator; ?>">
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Observações:</label>
                                    <textarea id="overlay-textarea" class="autoExpand" name="observation" rows="1"></textarea>
                                </div>
                            </div>
                        </section>
                    </div>
                    <?php 
                        //Inicia a variavel produtosIndex como 0 para ser incrementada
                        $produtosIndex = 0;
                        //Seleciona todas as secções que pertencem ao orçamento associado
                        $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
                        $resultSection = $con->query($sqlSection);
                        if ($resultSection->num_rows > 0) {
                            //Percorre todas as secções
                            while ($rowSection = $resultSection->fetch_assoc()) {
                                //query sql para obter o nome da secção que esta a ser corrida agora no while
                                $sql = "SELECT nameSection FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = {$rowSection['orderSection']} AND nameSection != '';";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $nomeSecao = $row['nameSection'];
                                }
                                ?>
                                <div class="worksheet">
                                    <section id="secoes">
                                        <div class="secao">
                                            <h3> Secção <?php echo $rowSection['orderSection']; ?> </h3>
                                            <input type="text" 
                                                id="search-box-<?php echo $rowSection['orderSection']; ?>" 
                                                name="seccao_nome_<?php echo $rowSection['orderSection']; ?>" 
                                                placeholder="Nome da secção" 
                                                value="<?php echo $row['nameSection']; ?>" readonly/>
                                            <table id="table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 55px;">Check</th>
                                                        <th style="width: 55px;">Armazém</th>
                                                        <th style="width: 65px; text-align: center;">Nº</th>
                                                        <th style="width: 150px; text-align: center;">N/REF</th>
                                                        <th style="width: 300px; text-align: center;">Designação</th>
                                                        <th style="width: 65px;">Quantidade</th>
                                                        <th class="inputs-th">Observações</th>
                                                    </tr>
                                                </thead>
                                                <?php 
                                                    //Seleciona todos os produtos que estão associado à secção em que estamos e ao orçamento associado
                                                    $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
                                                    $resultProducts = $con->query($sqlProducts);
                                                    if ($resultProducts->num_rows > 0) {
                                                        //Percorre todos os produtos
                                                        while ($rowProducts = $resultProducts->fetch_assoc()){
                                                            //incrementa o index dos produtos que é o numero que aparece antes de cada produto, para os contar
                                                            $produtosIndex++; 

                                                            //Reinicia o valor das variaveis que contêm os dados do produto
                                                            $refProduct = '';
                                                            $nameProduct = '';
                                                            $amountProduct = 0;
                                                            $descriptionProduct = '';
                                                            $valueProduct = 0;
                                                            $sizeProduct = '';?>
                                                            <tbody class="produtos">
                                                                <tr>
                                                                    <?php 
                                                                        //query sql para selecionar todos os dados do produto para os mostrar depois 
                                                                        $sql = "SELECT refProduct, nameProduct, amountProduct, descriptionProduct, sizeProduct FROM budget_sections_products WHERE idbudget = $idBudget AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                                        $result = $con->query($sql);
                                                                        if ($result->num_rows > 0) {
                                                                            while ($row = $result->fetch_assoc()) {
                                                                                $refProduct = $row['refProduct'];
                                                                                $nameProduct = $row['nameProduct'];
                                                                                $amountProduct = $row['amountProduct'];
                                                                                $descriptionProduct = $row['descriptionProduct'];
                                                                                $sizeProduct = $row['sizeProduct'];
                                                                            }
                                                                        }
                                                                    ?>
                                                                    <td><input type="checkbox" style="margin-left 20px;" class="check" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_check_<?php echo $rowProducts['orderProduct']; ?>"></td>
                                                                    <td><input type="checkbox" style="margin-left 20px;" class="armazem" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_armazem_<?php echo $rowProducts['orderProduct']; ?>"></td>
                                                                    <td><input type="text" class="id" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_index_<?php echo $rowProducts['orderProduct']; ?>" readonly></td>
                                                                    <td><input type="text" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_ref_<?php echo $rowProducts['orderProduct']; ?>" value = "<?php echo $refProduct; ?>" readonly></td>
                                                                    <td><input type="text" class="designacao" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_designacao_<?php echo $rowProducts['orderProduct']; ?>" value = "<?php echo $nameProduct; ?>" readonly></td>
                                                                    <td><input type="text" class="quantidade" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_quantidade_<?php echo $rowProducts['orderProduct']; ?>" value = "<?php if (!isset($amountProduct)) {echo $amountProduct;} else {echo 1;} ?>" readonly></td>
                                                                    <td class="inputs-td">
                                                                        <input type="text" class="descricao" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_observacao_<?php echo $rowProducts['orderProduct']; ?>" value = "<?php echo $descriptionProduct; ?>">
                                                                        <input type="text" class="tamanho" name="secao_<?php echo $rowSection['orderSection']; ?>_produto_tamanho_<?php echo $rowProducts['orderProduct']; ?>">
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        <?php } ?>
                                                    <?php } 
                                                ?>
                                            </table>
                                        </div>
                                    </section>  
                                </div>
                            <?php } ?>
                        <?php } 
                    ?>
                    <script>
                        //Evento para quando a pagina for carregada atualizar o indice dos produtos
                        document.addEventListener("DOMContentLoaded", function() {
                            atualizarIndicesGlobais();
                        });

                        //Função para atualizar o indice dos produtos todos 
                        function atualizarIndicesGlobais() {
                            const secoes = document.querySelectorAll(".secao");
                            let produtoGlobalIndex = 0; // Reinicia o índice global de produtos

                            // Itera por cada secção
                            secoes.forEach((secao, secaoIdx) => {
                                const produtos = secao.querySelectorAll(".produtos"); // Seleciona os produtos dentro da secção

                                // Atualiza o número da secção (caso necessário)
                                const secaoTitulo = secao.querySelector("h3");
                                if (secaoTitulo) {
                                    secaoTitulo.textContent = `Secção ${secaoIdx + 1}:`;
                                }

                                // Itera pelos produtos dentro da secção
                                produtos.forEach((produto) => {
                                    if (!(produto.style.display === "none")) {
                                        produtoGlobalIndex++; // Incrementa o índice global
                                        // Atualiza o número do produto
                                        const numeroProdutoInput = produto.querySelector(".id");
                                        if (numeroProdutoInput) {
                                            numeroProdutoInput.value = produtoGlobalIndex;
                                        }
                                    }
                                });
                            });
                        }
                    </script>
                </div>
                <button id=botSaveWorksheet type="submit">Criar Ficha de Trabalho</button>
            </form>
        </main>
    </div>
</body>
</html>
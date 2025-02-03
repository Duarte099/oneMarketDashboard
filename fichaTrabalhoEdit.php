<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 3;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_002", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }
    
    //Obtem o id da ficha de trabalho a ser editada via GET
    $idWorksheet = $_GET['idWorksheet'];
    //query sql para obter a ficah de trabalho cujo id é igual ao recebido por GET
    $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
    $result = $con->query($sql);
    //Se não houver ficha de trabalho com este id então redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    //Se existir uma ficha obtem os dados dela e forma o numero da ficha 
    else {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
        $idBudget =  $row['idBudget'];
        $numWorksheet = $row['num'];
        $yearWorksheet = $row['year'];
        $numFicha = "$numWorksheet/$yearWorksheet";
    }

    //Seleciona o maximo do idVersão para obter a ultima versão feita(atual)
    $sql = "SELECT MAX(idVersion) AS idVersion FROM worksheet_version WHERE idWorksheet = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxVersao =  $row['idVersion'];
    }

    //Caso a versão estaja no GET atribui essa versão à variavel $versao, caso contrario atribiu a maior versão(atual)
    $versao = isset($_GET['versao']) ? (int)$_GET['versao'] : $maxVersao;

    //Obtem a versão da ficha de trabalgo cujo idVersão é igual ao recebido e o id da ficha é igual ao recebido
    $sql = "SELECT * FROM worksheet_version WHERE idVersion = '$versao' AND idWorksheet = '$idWorksheet'";
    $result = $con->query($sql);
    //Caso essa versão não exista redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Obtem o id do administrador que está logado
    $idAdmin = $_SESSION['id'];

    //Se a versão que esta a ser editada for a versão atual
    if ($versao == $maxVersao) {
        //Seleciona os dados da ficha de trabalho atual
        $sql = "SELECT observation, readyStorage, joinWork, exitWork FROM worksheet WHERE id = $idWorksheet;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $readyStorage = $row['readyStorage'];
            $joinWork = $row['joinWork'];
            $exitWork = $row['exitWork'];
            $observation = $row['observation'];
        }
        $numFicha = "$numWorksheet/$yearWorksheet";
    }
    else {
        //Caso a versão que esta a ser editada seja outra obtem os dados da ficha de trabalho dessa versão
        $sql = "SELECT observation, readyStorage, joinWork, exitWork FROM worksheet_version WHERE idWorksheet = $idWorksheet AND idVersion = $versao;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $readyStorage = $row['readyStorage'];
            $joinWork = $row['joinWork'];
            $exitWork = $row['exitWork'];
            $observation = $row['observation'];
        }
    }

    //Obtem os dados todos do cliente associado a esta ficha de trabalho
    $sql = "SELECT name, contact FROM client WHERE client.id = $idClient;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameClient = $row['name'];
        $contactClient = $row['contact'];
    }

    //Obtem os dados todos do orçamento associado a esta ficha de trabalho
    $sql = "SELECT num, year FROM budget WHERE idWorksheet = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
    }
    //Forma o numero do orçamento
    $numOrcamento = "$numBudget/$yearBudget";

    //Obtem o nome do administrador que esta a editar o admin
    $sql = "SELECT name FROM administrator WHERE id = $idAdmin;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameAdmin = $row['name'];
    }

    //Conta o numero de secções que o orçamento associado tem
    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }

    //Obtem a versão atual da ficha de trabalho
    $sql = "SELECT MAX(idVersion) AS idVersion FROM worksheet_version WHERE idWorksheet = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxVersao =  $row['idVersion'];
    }
?>
    <link rel="stylesheet" href="./css/novaFichaTrabalho.css">
    <title>OneMarket | <?php echo $numFicha ?></title>
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
                    <h1><?php echo $numFicha; ?></h1>
                </div>
                <form action="" method="GET">
                    <div class="select-container">
                        <input type="hidden" name="idWorksheet" value="<?= $idWorksheet ?>">
                        <label for="versao" class="select-label">Versão:</label>
                        <select name="versao" id="versao" onchange="confirmSubmit(this, <?php echo $maxVersao; ?>)">
                            <?php
                                //query SQl para selecionar todas as versões que esta ficha de trabalho tem
                                $sql = "SELECT DISTINCT idVersion, created FROM worksheet_version WHERE idWorksheet = $idWorksheet ORDER BY idVersion DESC;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    //percorre todas as versões e acrescenta-as ao select
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value=\"{$row['idVersion']}\" " . ($row['idVersion'] == $versao ? 'selected' : '') . ">{$row['idVersion']} | {$row['created']}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <form action="fichaTrabalhoInserir.php?idWorksheet=<?= $idWorksheet ?>&op=edit" method=post>
                <div class="bottom-data">
                    <div class="worksheet">
                        <section>
                            <div class="header-container">
                                <h2>Dados da Ficha de Trabalho</h2>
                                <a href="fichaTrabalhoGaleria.php?idWorksheet=<?= $idWorksheet ?>" id="galeria" class="galeria">
                                    <i class='bx bx-image'></i>
                                    <span>Galeria</span>
                                </a>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Ficha Numero:</label>
                                    <input type="text" name="numFicha" required readonly value="<?php echo $numFicha; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Cliente:</label>
                                    <input type="text" name="cliente" required readonly value="<?php echo $nameClient; ?>">
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
                                    <input type="date" name="prontoArmazem" value="<?php echo $readyStorage; ?>" <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "readonly";}?>>
                                </div>
                                <div class="section-group">
                                    <label>Entrada em obra:</label>
                                    <input type="date" name="entradaObra" value="<?php echo $joinWork; ?>" <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "readonly";}?>>
                                </div>
                                <div class="section-group">
                                    <label>Saída de obra:</label>
                                    <input type="date" name="saidaObra" value="<?php echo $exitWork; ?>" <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "readonly";}?>>
                                </div>
                                <div class="section-group">
                                    <label>Elaborado por:</label>
                                    <input type="text" name="createdby" readonly required value="<?php echo $nameAdmin; ?>">
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Observações:</label>
                                    <textarea id="overlay-textarea" class="autoExpand" name="observation" rows="1"><?php echo $observation; ?></textarea>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Secções e Produtos -->
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
                                else {
                                    $nomeSecao = ' ';
                                }
                                ?>
                                <div class="worksheet">
                                    <section id="secoes">
                                        <div class="secao">
                                            <h3>Secção <?php echo $rowSection['orderSection'] ?>:</h3>
                                            <input type="text"
                                                id="search-box-<?php echo $rowSection['orderSection'] ?>" 
                                                name="seccao_nome_<?php echo $rowSection['orderSection'] ?>" 
                                                placeholder="Nome da secção" 
                                                value="<?php echo $nomeSecao ?>" readonly/>
                                            <table id = "table">
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
                                                                        $sql = "SELECT refProduct, nameProduct, amountProduct FROM budget_sections_products WHERE idbudget = $idBudget AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                                        $result = $con->query($sql);
                                                                        if ($result->num_rows > 0) {
                                                                            $row = $result->fetch_assoc();
                                                                            $refProduct = $row['refProduct'];
                                                                            $nameProduct = $row['nameProduct'];
                                                                            $amountProduct = $row['amountProduct'];
                                                                        }
                                                                        //Se a versão que esta a ser editada for a atual, obtem os dados da ficha de trabalho cuja versão é a atual
                                                                        if ($versao == $maxVersao) {
                                                                            $sql = "SELECT checkProduct, storageProduct, observationProduct, sizeProduct FROM budget_sections_products WHERE idbudget = $idBudget AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                                            $result = $con->query($sql);
                                                                        }
                                                                        //Caso contrario obtem os dados da versão especifica
                                                                        else {
                                                                            $sql = "SELECT checkProduct, storageProduct, observationProduct, sizeProduct FROM worksheet_version WHERE idWorksheet = $idWorksheet AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                                            $result = $con->query($sql);
                                                                        }
                                                                        if ($result->num_rows > 0) {
                                                                            $row = $result->fetch_assoc();
                                                                            $checkProduct = "";
                                                                            $storageProduct = "";
                                                                            if (isset($row['checkProduct']) && $row['checkProduct'] == 1) {
                                                                                $checkProduct = "checked";
                                                                            }
                                                                            if (isset($row['storageProduct']) && $row['storageProduct'] == 1) {
                                                                                $storageProduct = "checked";
                                                                            }
                                                                            $observationProduct = $row['observationProduct'];
                                                                            $sizeProduct = $row['sizeProduct'];
                                                                        }
                                                                    //Mostra os detalhes de cada produto, com os valores obtidos e apenas pode ser alterado se o administrador tiver permissão para tal
                                                                    ?>
                                                                    <td><input type="checkbox" class="check" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_check_<?php echo $rowProducts['orderProduct']; ?>" <?php if ($checkProduct == "checked") {echo $checkProduct;} ?> <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "disabled";}?>></td>
                                                                    <td><input type="checkbox" class="armazem" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_armazem_<?php echo $rowProducts['orderProduct']; ?>" <?php if ($storageProduct == "checked") {echo $storageProduct;} ?> <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "disabled";}?>></td>
                                                                    <td><input type="text" class="id" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_index_<?php echo $rowProducts['orderProduct']; ?>" readonly></td>
                                                                    <td><input type="text" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_ref_<?php echo $rowProducts['orderProduct']; ?>" value = "<?php echo $refProduct; ?>" readonly></td>
                                                                    <td><input type="text" class="designacao" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_designacao_<?php echo $rowProducts['orderProduct']; ?>" value="<?php echo $nameProduct; ?>" readonly></td>
                                                                    <td><input type="text" class="quantidade" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_quantidade_<?php echo $rowProducts['orderProduct']; ?>" value="<?php if (!isset($amountProduct)) {echo $amountProduct;} else {echo 1;} ?>" readonly></td>
                                                                    <td class="inputs-td">
                                                                        <input type="text" class="descricao" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_observacao_<?php echo $rowProducts['orderProduct']; ?>" value="<?php echo $observationProduct; ?>" <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "readonly";}?>>
                                                                        <input type="text" class="tamanho" name="secao_<?php echo $rowSection['orderSection'] ?>_produto_tamanho_<?php echo $rowProducts['orderProduct']; ?>" value="<?php echo $sizeProduct; ?>" <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "readonly";}?>>
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
                        <?php } ?>
                    <script>
                        //Função para confirmar a troca de versão
                        function confirmSubmit(selectElement, maxVersao) {
                            //Obtem a versão selecionada
                            const selectedVersion = selectElement.value;
                            //Inicia a variavel com a resposta do administrador como true
                            const userConfirmed = true
                            //Se a versão selecionada for a atual então pergunta ao administrador se pretende continuar
                            if (selectedVersion == maxVersao ) {
                                const userConfirmed = confirm("Ao mudar de versão perderá as alterações feitas. Deseja prosseguir?");
                            }
                            //Se o administrador tiver respondido que sim 
                            if (userConfirmed) {
                                // Envia o formulário
                                selectElement.form.submit();
                            } 
                            //Caso contrario
                            else {
                                // Restaura a seleção anterior
                                const previousValue = selectElement.getAttribute('data-previous');
                                if (previousValue !== null) {
                                    selectElement.value = previousValue;
                                }
                            }
                        }

                        //Função para imprimir a ficha de trabalho
                        function worksheetPrint(idWorksheet) {
                            // Abre a outra página em uma nova janela
                            const printWindow = window.open('fichaTrabalhoImpressao.php?idWorksheet=' + idWorksheet, '_blank');

                            // Aguarda a página carregar completamente
                            printWindow.onload = () => {
                                // Chama o método de impressão da nova página
                                printWindow.print();
                            };
                        }

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
                <div class="button-container">
                    <?php
                        //Se o administrador tiver permissões de edição mostra o botão de guardar alterações
                        if (adminPermissions($con, "adm_002", "update") == 1) {
                            echo '<button id="botSaveWorksheet" type="submit">Guardar alterações</button>';
                        }
                    ?>
                    <button id="botPrintWorksheet" type="button" onclick="worksheetPrint(<?php echo $idWorksheet; ?>)">Imprimir</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
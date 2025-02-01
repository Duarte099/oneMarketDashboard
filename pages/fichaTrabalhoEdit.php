<?php 
    include('../pages/head.php'); 

    $estouEm = 3;

    if (adminPermissions($con, "adm_002", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $produtosIndex = 0;
    
    $idWorksheet = $_GET['idWorksheet'];
    $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
    $result = $con->query($sql);
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }

    $sql = "SELECT MAX(idVersion) AS idVersion FROM worksheet_version WHERE idWorksheet = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxVersao =  $row['idVersion'];
    }

    $versao = isset($_GET['versao']) ? (int)$_GET['versao'] : $maxVersao;
    $sql = "SELECT * FROM worksheet_version WHERE idVersion = '$versao' AND idWorksheet = '$idWorksheet'";
    $result = $con->query($sql);
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idAdmin = $_SESSION['id'];

    $sql = "SELECT idClient, idBudget, num, year FROM worksheet WHERE id = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
        $idBudget =  $row['idBudget'];
        $numWorksheet = $row['num'];
        $yearWorksheet = $row['year'];
    }
    $numFicha = "$numWorksheet/$yearWorksheet";

    if ($versao == $maxVersao) {
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

    $sql = "SELECT name, contact FROM client WHERE client.id = $idClient;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameClient = $row['name'];
        $contactClient = $row['contact'];
    }

    $sql = "SELECT num, year FROM budget WHERE idWorksheet = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
    }
    $numOrcamento = "$numBudget/$yearBudget";

    $sql = "SELECT name FROM administrator WHERE id = $idAdmin;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameAdmin = $row['name'];
    }

    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }

    $sql = "SELECT MAX(idVersion) AS idVersion FROM worksheet_version WHERE idWorksheet = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxVersao =  $row['idVersion'];
    }
?>
    <link rel="stylesheet" href="../css/novaFichaTrabalho.css">
    
    <title>OneMarket | <?php echo $numFicha ?></title>
</head>

<body>

    <?php 
        include('../pages/sideBar.php'); 
    ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php 
            include('../pages/header.php'); 
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
                                $sql = "SELECT DISTINCT idVersion, created FROM worksheet_version WHERE idWorksheet = $idWorksheet ORDER BY idVersion DESC;";
                                $result = $con->query($sql);

                                if ($result->num_rows > 0) {
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
                        $produtosIndex = 0; 
                        $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
                        $resultSection = $con->query($sqlSection);
                        if ($resultSection->num_rows > 0) {
                            while ($rowSection = $resultSection->fetch_assoc()) {
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
                                                id="search-box-<?php echo $nomeSecao ?>" 
                                                name="seccao_nome_<?php echo $nomeSecao ?>" 
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
                                                    $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
                                                    $resultProducts = $con->query($sqlProducts);
                                                    if ($resultProducts->num_rows > 0) {
                                                        while ($rowProducts = $resultProducts->fetch_assoc()){
                                                            $produtosIndex++; 
                                                            $refProduct = '';
                                                            $nameProduct = '';
                                                            $amountProduct = 0;
                                                            $descriptionProduct = '';
                                                            $valueProduct = 0;
                                                            $sizeProduct = '';?>
                                                            <tbody class="produtos">
                                                                <tr>
                                                                    <?php
                                                                        $sql = "SELECT refProduct, nameProduct, amountProduct FROM budget_sections_products WHERE idbudget = $idBudget AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                                        $result = $con->query($sql);
                                                                        if ($result->num_rows > 0) {
                                                                            $row = $result->fetch_assoc();
                                                                            $refProduct = $row['refProduct'];
                                                                            $nameProduct = $row['nameProduct'];
                                                                            $amountProduct = $row['amountProduct'];
                                                                        }

                                                                        if ($versao == $maxVersao) {
                                                                            $sql = "SELECT checkProduct, storageProduct, observationProduct, sizeProduct FROM budget_sections_products WHERE idbudget = $idBudget AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                                            $result = $con->query($sql);
                                                                        }
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
                                                                    ?>
                                                                    <td><input type="checkbox" class="check" name="secao_<?php echo $nomeSecao ?>_produto_check_<?php echo $rowProducts['orderProduct']; ?>" <?php if ($checkProduct == "checked") {echo $checkProduct;} ?> <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "disabled";}?>></td>
                                                                    <td><input type="checkbox" class="armazem" name="secao_<?php echo $nomeSecao ?>_produto_armazem_<?php echo $rowProducts['orderProduct']; ?>" <?php if ($storageProduct == "checked") {echo $storageProduct;} ?> <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "disabled";}?>></td>
                                                                    <td><input type="text" class="id" name="secao_<?php echo $nomeSecao ?>_produto_index_<?php echo $rowProducts['orderProduct']; ?>" readonly></td>
                                                                    <td><input type="text" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $nomeSecao ?>_produto_ref_<?php echo $rowProducts['orderProduct']; ?>" value = "<?php echo $refProduct; ?>" readonly></td>
                                                                    <td><input type="text" class="designacao" name="secao_<?php echo $nomeSecao ?>_produto_designacao_<?php echo $rowProducts['orderProduct']; ?>" value="<?php echo $nameProduct; ?>" readonly></td>
                                                                    <td><input type="text" class="quantidade" name="secao_<?php echo $nomeSecao ?>_produto_quantidade_<?php echo $rowProducts['orderProduct']; ?>" value="<?php if (!isset($amountProduct)) {echo $amountProduct;} else {echo 1;} ?>" readonly></td>
                                                                    <td class="inputs-td">
                                                                        <input type="text" class="descricao" name="secao_<?php echo $nomeSecao ?>_produto_observacao_<?php echo $rowProducts['orderProduct']; ?>" value="<?php echo $observationProduct; ?>" <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "readonly";}?>>
                                                                        <input type="text" class="tamanho" name="secao_<?php echo $nomeSecao ?>_produto_tamanho_<?php echo $rowProducts['orderProduct']; ?>" value="<?php echo $sizeProduct; ?>" <?php if (adminPermissions($con, "adm_002", "update") == 0) {echo "readonly";}?>>
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
                        function confirmSubmit(selectElement, maxVersao) {
                            const selectedVersion = selectElement.value;
                            const userConfirmed = true
                            if (selectedVersion == maxVersao ) {
                                const userConfirmed = confirm("Ao mudar de versão perderá as alterações feitas. Deseja prosseguir?");
                            }
                            if (userConfirmed) {
                                selectElement.form.submit(); // Envia o formulário
                            } else {
                                // Restaura a seleção anterior
                                const previousValue = selectElement.getAttribute('data-previous');
                                if (previousValue !== null) {
                                    selectElement.value = previousValue;
                                }
                            }
                        }

                        function worksheetPrint(idWorksheet) {
                            // Abre a outra página em uma nova janela
                            const printWindow = window.open('fichaTrabalhoImpressao.php?idWorksheet=' + idWorksheet, '_blank');

                            // Aguarda a página carregar completamente
                            printWindow.onload = () => {
                                // Chama o método de impressão da nova página
                                printWindow.print();
                            };
                        }

                        document.addEventListener("DOMContentLoaded", function() {
                            atualizarIndicesGlobais();
                        });

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
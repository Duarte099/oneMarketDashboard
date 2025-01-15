<?php 
    session_start();

    $estouEm = 2;

    include('../db/conexao.php');

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    if (adminPermissions("adm_001", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idBudget = $_GET['idBudget'];

    $sql = "SELECT MAX(idVersion) AS idVersion FROM budget_version WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxVersao =  $row['idVersion'];
    }

    $versao = isset($_GET['versao']) ? (int)$_GET['versao'] : $maxVersao;
    $inputValue = '';
    $produtosIndex = 0;

    $sql = "SELECT budget.idClient FROM budget WHERE budget.id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
    }

    if ($versao == $maxVersao) {
        $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $numSections =  $row['numSections'];
        }
    }
    else {
        $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_version WHERE idBudget = $idBudget AND idVersion <= $versao;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $numSections =  $row['numSections'];
        }
    }

    $sql = "SELECT num, year, name, laborPercent , discountPercent, observation FROM budget WHERE id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
        $nameBudget = $row['name'];
        $maoObraBudget = $row['laborPercent'];
        $descontoBudget = $row['discountPercent'];
        $observacao = $row['observation'];
    }
    $numOrçamento = "$numBudget/$yearBudget";

    $totalBudget = 0;
    $sql = "SELECT valueProduct, amountProduct FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    while ($row = $result->fetch_assoc()) {
        $totalBudget = $totalBudget + $row['valueProduct'] * $row['amountProduct'];
    }

    $sql = "SELECT name, contact FROM client WHERE client.id = $idClient;";
    $result = $con->query($sql);
    $row = $result->fetch_assoc();
        $clientName = $row['name'];
        $clientContact = $row['contact'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/editBudget.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | <?php echo $numOrçamento; ?> </title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <h1><?php echo($numOrçamento) ?></h1>
                </div>
                <form action="" method="GET">
                    <div class="select-container">
                        <input type="hidden" name="idBudget" value="<?= $idBudget ?>">
                        <label for="versao" class="select-label">Versão:</label>
                        <select name="versao" id="versao" onchange="confirmSubmit(this, <?php echo $maxVersao; ?>)">
                            <?php
                                $sql = "SELECT DISTINCT idVersion, created FROM budget_version WHERE idBudget = $idBudget ORDER BY idVersion DESC;";
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
            <form action="orcamentoInserir.php?idBudget=<?= $idBudget ?>&op=edit" method=post>
                <div class="bottom-data">
                    <div class="budget">
                        <section>
                            <h2>Dados do Orçamento</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Orçamento Numero:</label>
                                    <input type="text" name="numOrcamento" required readonly value="<?php
                                        echo $numOrçamento;
                                    ?>">
                                </div>
                                <div class="section-group">
                                    <label>Projeto:</label>
                                    <input type="text" name="nomeProjeto" required value="<?php echo $nameBudget; ?>" 
                                    <?php 
                                        if (adminPermissions("adm_001", "update") == 0) {
                                            echo "readonly";
                                        }
                                    ?>>
                                </div>
                                <div class="section-group">
                                    <label>Cliente:</label>
                                    <input type="text" name="cliente" id="new-budget" required readonly value="<?php echo $clientName; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="text" name="contacto" required readonly value="<?php echo $clientContact; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Ficha de Trabalho:</label>
                                    <input type="text" name="fichaTrabalho" required readonly value="<?php 
                                        $numFichaTrabalho = "";
                                        $sql = "SELECT num, year FROM worksheet WHERE idBudget = $idBudget;";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $numWorksheet = $row['num'];
                                            $yearWorksheet = $row['year'];
                                            $numFichaTrabalho = "$numWorksheet/$yearWorksheet";
                                        }
                                        echo $numFichaTrabalho;
                                    ?>">
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Deslocações e Montagem:</label>
                                    <input class="percent" type="text" value="<?php echo $maoObraBudget . "%"; ?>" maxlength="6" pattern="[0-9]*">
                                    <input type="text" name="totalDeslocacoesMontagens" readonly required value="<?php echo $totalBudget * ($maoObraBudget / 100); ?>">
                                </div>
                                <div class="section-group">
                                    <label>Total:</label>
                                    <input type="text" name="total" readonly required value="<?php echo $totalBudget; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Desconto:</label>
                                    <input class="percent" type="text" value="<?php echo $descontoBudget . "%"; ?>" maxlength="6" pattern="[0-9]*">
                                    <input type="text" name="totalDesconto" readonly required value="<?php echo $totalBudget * ($descontoBudget / 100); ?>">
                                </div>
                                <script>
                                    //Get human input: 
                                    document.querySelector('.percent').addEventListener('input', function(e){
                                        //Separate the percent sign from the number:
                                        let int = e.target.value.slice(0, e.target.value.length - 1);
                                        
                                        /* If there is no number (just the percent sign), rewrite
                                            it so it persists and move the cursor just before it.*/
                                        if (int.includes('%')) {
                                            e.target.value = '%';
                                        }
                                        /* If the whole has been written and we are starting the
                                            fraction rewrite to include the decimal point and percent 
                                            sign. The fraction is a sigle digit. Cursor is moved to 
                                            just ahead of this digit.*/
                                        else if(int.length >= 3 && int.length <= 4 && !int.includes('.')){
                                            e.target.value = int.slice(0,2) + '.' + int.slice(2,3) + '%';
                                            e.target.setSelectionRange(4, 4);
                                        }
                                        /* If the we have a double digit fraction we split up, format it
                                            and print that. Cursor is already in the right place.*/
                                        else if(int.length >= 5 & int.length <= 6){
                                            let whole = int.slice(0, 2);
                                            let fraction = int.slice(3, 5);
                                            e.target.value = whole + '.' + fraction + '%';
                                        }
                                        /* If we're still just writing the whole (first 2 digits), just 
                                            print that with the percent sign. Also if the element has just
                                            been clicked on we want the cursor before the percent sign.*/
                                        else {
                                            e.target.value = int + '%';
                                            e.target.setSelectionRange(e.target.value.length-1, e.target.value.length-1);
                                        }
                                        });

                                        /* For consuption by robots, the number is best written as an 
                                        interger, so we do that remembering it contains 2 or less
                                        decimal places*/
                                        function getInt(val){
                                        //So as not to breakup a potential fraction
                                        let v = parseFloat(val);
                                        //If we only have the whole:
                                        if(v % 1 === 0){
                                            return v;  
                                        //If the numberis a fraction  
                                        }else{
                                            let n = v.toString().split('.').join('');
                                            return parseInt(n);
                                        }
                                    }
                                </script>
                                <div class="section-group">
                                    <label>Observações:</label>
                                    <input type="text" name="observacoes" required value="<?php echo $observacao; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Data de Criação:</label>
                                    <input type="text" name="dataCriacao" readonly required value="<?php 
                                        $sql = "SELECT budget.created FROM budget WHERE budget.id = $idBudget;";
                                        $result = $con->query($sql);
                                        $row = $result->fetch_assoc();
                                        echo $row['created'];
                                    ?>">
                                </div>
                            </div>
                        </section>
                    </div>
                    <?php 
                        $produtosIndex = 0; 
                        for ($i=1; $i <= $numSections + 5; $i++) {
                            if ($versao == $maxVersao) {
                                $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $numProducts = $row['numProducts'];
                                }

                                $sql = "SELECT nameSection FROM budget_sections_products WHERE orderSection = $i AND idBudget = $idBudget;";
                                $result = $con->query($sql);
                            
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $nomeSecao = htmlspecialchars($row['nameSection']);
                                    $displayStyle = '';
                                } else {
                                    $nomeSecao = '';
                                    $displayStyle = 'display: none;';
                                }
                            }
                            else {
                                $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_version WHERE idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0 AND idVersion = $versao;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $numProducts = $row['numProducts'];
                                }

                                $sql = "SELECT nameSection FROM budget_sections_products WHERE orderSection = $i AND idBudget = $idBudget;";
                                $result = $con->query($sql);
                            
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $nomeSecao = htmlspecialchars($row['nameSection']);
                                    $displayStyle = '';
                                } else {
                                    $nomeSecao = '';
                                    $displayStyle = 'display: none;';
                                }
                            }
                            ?>
                            <div class="budget" style="<?php echo $displayStyle; ?>">
                                <section id="secoes">
                                    <div class="secao">
                                        <h3>Secção <?php echo $i; ?>:</h3>
                                        <input type="search"
                                            id="search-box-<?php echo $i; ?>" 
                                            name="seccao_nome_<?php echo $i; ?>" 
                                            list="datalistSection"
                                            placeholder="Nome da secção" 
                                            oninput="performSearchSecoes(this, <?php echo $i; ?>)" 
                                            value="<?php echo $nomeSecao; ?>" 
                                            <?php if (adminPermissions("adm_001", "update") == 0) {echo "readonly";}?>
                                        />
                                        <datalist id='datalistSection'>
                                            <?php
                                                $sql = "SELECT DISTINCT nameSection FROM budget_sections_products;";
                                                $result = $con->query($sql);

                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option>$row[nameSection]</option>";
                                                    }
                                                }
                                            ?>
                                        </datalist>
                                        <table id = "table">
                                            <thead>
                                                <tr>
                                                    <th>Nº</th>
                                                    <th>N/REF</th>
                                                    <th>Designação</th>
                                                    <th>Quantidade</th>
                                                    <th>Descrição</th>
                                                    <th>Preço Unitário</th>
                                                    <th>Preço Total</th>
                                                </tr>
                                            </thead>
                                            <?php 
                                                for ($j=1; $j <= $numProducts + 10; $j++) { 
                                                    $produtosIndex++;
                                                    $refProduct = '';
                                                    $nameProduct = '';
                                                    $amountProduct = 0;
                                                    $descriptionProduct = '';
                                                    $valueProduct = 0;?>
                                                    <?php if ($j == 1 || $j <= $numProducts) { ?>
                                                        <tbody class="produtos">
                                                            <tr>
                                                                <?php 
                                                                    if ($versao == $maxVersao) {
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
                                                                    }
                                                                    else {
                                                                        $sql = "SELECT refProduct, nameProduct, amountProduct, descriptionProduct, valueProduct FROM budget_version WHERE budget_version.idbudget = $idBudget AND orderProduct = '$j' AND orderSection = '$i' AND idVersion = $versao;";
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
                                                                    }
                                                                ?>
                                                                <td><input type="number" class="id" name="secao_<?php echo $i; ?>_produto_index_<?php echo $j; ?>" readonly></td>
                                                                <td><input type="search" list="datalistProduct" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $i; ?>_produto_ref_<?php echo $j; ?>" value = "<?php echo $refProduct; ?>" oninput="atualizarCampos(this); performSearchProdutos(this, <?php echo $produtosIndex; ?>);" <?php if (adminPermissions("adm_001", "update") == 0 || $versao < $maxVersao) {echo "readonly";}?>></td>
                                                                <td><input type="text" class="designacao" name="secao_<?php echo $i; ?>_produto_designacao_<?php echo $j; ?>" value = "<?php echo $nameProduct; ?>" readonly></td>
                                                                <td><input type="number" class="quantidade" name="secao_<?php echo $i; ?>_produto_quantidade_<?php echo $j; ?>" value = "<?php if (!isset($amountProduct)) {echo $amountProduct;} else {echo 1;} ?>" oninput="atualizarPrecoTotal(this)" <?php if (adminPermissions("adm_001", "update") == 0 || $versao < $maxVersao) {echo "readonly";}?>></td>
                                                                <td><input type="text" class="descricao" name="secao_<?php echo $i; ?>_produto_descricao_<?php echo $j; ?>" value = "<?php echo $descriptionProduct; ?>" <?php if (adminPermissions("adm_001", "update") == 0 || $versao < $maxVersao) {echo "readonly";}?>></td>
                                                                <td><input type="text" class="valor" name="secao_<?php echo $i; ?>_produto_preco_unitario_<?php echo $j; ?>" value = "<?php echo $valueProduct; ?>" readonly></td>
                                                                <td><input type="text" class="valorTotal" name="secao_<?php echo $i; ?>_produto_preco_total_<?php echo $j; ?>" value = "<?php echo $amountProduct * $valueProduct;?>" readonly></td>
                                                            </tr>
                                                        </tbody>
                                                        <datalist id='datalistProduct'>
                                                            <?php
                                                                $sql = "SELECT DISTINCT refProduct FROM budget_sections_products;";
                                                                $result = $con->query($sql);

                                                                if ($result->num_rows > 0) {
                                                                    while ($row = $result->fetch_assoc()) {
                                                                        echo "<option>$row[refProduct]</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </datalist>
                                                    <?php } else { ?>
                                                        <tbody class="produtos" style="display: none;">
                                                            <tr>
                                                                <td><input type="number" class="id" name="secao_<?php echo $i; ?>_produto_index_<?php echo $j; ?>" readonly></td>
                                                                <td><input type="text" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $i; ?>_produto_ref_<?php echo $j; ?>" oninput="atualizarCampos(this); performSearchProdutos(this, <?php echo $produtosIndex; ?>);"></td>
                                                                <td><input type="text" class="designacao" name="secao_<?php echo $i; ?>_produto_designacao_<?php echo $j; ?>" readonly></td>
                                                                <td><input type="number" class="quantidade" name="secao_<?php echo $i; ?>_produto_quantidade_<?php echo $j; ?>" value="1" oninput="atualizarPrecoTotal(this)"></td>
                                                                <td><input type="text" class="descricao" name="secao_<?php echo $i; ?>_produto_descricao_<?php echo $j; ?>" ></td>
                                                                <td><input type="text" class="valor" name="secao_<?php echo $i; ?>_produto_preco_unitario_<?php echo $j; ?>" readonly></td>
                                                                <td><input type="text" class="valorTotal" name="secao_<?php echo $i; ?>_produto_preco_total_<?php echo $j; ?>" readonly></td>
                                                            </tr>
                                                        </tbody>
                                                        <div id="produtosModal-<?php echo $produtosIndex; ?>" class="modal">
                                                            <div id="results-container-<?php echo $produtosIndex; ?>" class="results-container"></div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } 
                                                if (adminPermissions("adm_001", "update") == 1 && $versao == $maxVersao) {
                                                    echo "<button type=\"button\" onclick=\"adicionarProduto(" . ($i - 1) . ")\">Adicionar Produto</button>";
                                                }
                                            ?>
                                        </table>
                                    </div>
                                </section>
                            </div>
                    <?php } ?>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            atualizarIndicesGlobais();
                        });

                        function confirmSubmit(selectElement, maxVersao) {
                            const selectedVersion = selectElement.value;
                            const userConfirmed = true
                            if (selectedVersion != maxVersao) {
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

                        // Salva o valor inicial ao carregar a página
                        document.getElementById('versao').addEventListener('focus', function() {
                            this.setAttribute('data-previous', this.value);
                        });

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

                        function adicionarProduto(secaoIdx) {
                            // Seleciona a seção com base no índice fornecido
                            const secao = document.querySelectorAll(".secao")[secaoIdx];
                            if (secao) {
                                // Localiza todos os conjuntos de produtos dentro da seção
                                const produtos = secao.querySelectorAll(".produtos");
                                if (produtos) {
                                    // Itera sobre os produtos para encontrar o próximo que está oculto
                                    for (let i = 0; i < produtos.length; i++) {
                                        if (produtos[i].style.display === "none") {
                                            produtos[i].style.display = "table-row-group"; // Torna visível
                                            atualizarIndicesGlobais();
                                            return; // Encerra a função após exibir o próximo produto
                                        }
                                    }
                                    alert("Atingiu o máximo de produtos criados por alteração."); // Mensagem caso todos os produtos já estejam visíveis
                                }
                            }
                        }

                        function adicionarSecao() {
                            // Seleciona a seção com base no índice fornecido
                            const secao = document.querySelectorAll(".budget");
                            if (secao) {
                                // Itera sobre os produtos para encontrar o próximo que está oculto
                                for (let i = 0; i < secao.length; i++) {
                                    if (secao[i].style.display === "none") {
                                        secao[i].style.display = ""; // Torna visível
                                        atualizarIndicesGlobais();
                                        return; // Encerra a função após exibir o próximo produto
                                    }
                                }
                                alert("Atingiu o máximo de secções criadas por alteração."); // Mensagem caso todos os produtos já estejam visíveis
                            }
                        }

                        function atualizarCampos(campoReferencia) {
                            const referencia = campoReferencia.value;
                            const linha = campoReferencia.closest("tr"); // A linha atual onde o campo está
                            const designacaoCampo = linha.querySelector(".designacao");
                            const valorCampo = linha.querySelector(".valor");

                            if (referencia.length > 0) {
                                // Buscar o nome do produto
                                $.ajax({
                                    url: 'ajax.novoOrcamento.php',
                                    type: 'GET',
                                    data: { referencia: referencia, action: 'getName' },
                                    success: function(data) {
                                        designacaoCampo.value = data;
                                        atualizarPrecoTotal(campoReferencia);
                                    },
                                    error: function() {
                                        console.error('Erro ao buscar o nome.');
                                    }
                                });

                                // Buscar o valor do produto
                                $.ajax({
                                    url: 'ajax.novoOrcamento.php',
                                    type: 'GET',
                                    data: { referencia: referencia, action: 'getValue' },
                                    success: function(data) {
                                        valorCampo.value = data;
                                        atualizarPrecoTotal(campoReferencia);
                                    },
                                    error: function() {
                                        console.error('Erro ao buscar o valor.');
                                    }
                                });

                            } else {
                                designacaoCampo.value = '';
                                valorCampo.value = '';
                            }
                            atualizarPrecoTotal(campoReferencia);
                        }

                        function atualizarPrecoTotal(elemento) {
                            const linha = elemento.closest("tr");
                            const quantidadeCampo = linha.querySelector(".quantidade");
                            const valorTotalCampo = linha.querySelector(".valorTotal");
                            const valorCampo = linha.querySelector(".valor");

                            const quantidade = parseFloat(quantidadeCampo.value) || 0;
                            const valor = parseFloat(valorCampo.value) || 0;

                            valorTotalCampo.value = (quantidade * valor).toFixed(2);
                        }
                    </script>
                </div>
                <?php 
                    if (adminPermissions("adm_001", "update") == 1 && $versao == $maxVersao) {
                        echo "
                            <button id=bottomButton type=\"button\" onclick=\"adicionarSecao()\">Adicionar Secção</button>
                            <button id=botSaveBudget type=\"submit\">Guardar Alterações</button>
                        ";
                    }
                ?>
            </main>
        <!-- <input type=text id=numSeccao name=numSeccao value=1> -->
        </form>
    </div>
</body>
</html>
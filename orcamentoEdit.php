<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //inclui a base de dados e segurança da página
    $estouEm = 2;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_001", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Obtem o id do orçamento a ser alterado via GET
    $idBudget = $_GET['idBudget'];

    //Seleciona o orçamento cujo id é igual ao recebido por GET
    $sql = "SELECT * FROM budget WHERE id = '$idBudget'";
    $result = $con->query($sql);
    //Se não retornar nenhum resultado redireciona para a página dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    //Se retornar obtem os dados do orçamento
    else {
        $row = $result->fetch_assoc();
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
        $nameBudget = $row['name'];
        $maoObraBudget = $row['laborPercent'];
        $descontoBudget = $row['discountPercent'];
        $observacao = $row['observation'];
        $idClient =  $row['idClient'];
        $createdBudget = $row['created'];
    }
    $numOrçamento = "$numBudget/$yearBudget";

    //Seleciona a maior versão, ou seja, a atual
    $sql = "SELECT MAX(idVersion) AS idVersion FROM budget_version WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxVersao =  $row['idVersion'];
    }

    //Obtem a versão enviada via GET caso não tenha atribui a versão atual
    $versao = isset($_GET['versao']) ? (int)$_GET['versao'] : $maxVersao;

    //Se obteu uma versão via GET seleciona a versão cujo id é igual ao recebido
    $sql = "SELECT * FROM budget_version WHERE idVersion = '$versao' AND idBudget = '$idBudget'";
    $result = $con->query($sql);
    //Se não retornar resultados redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Se a versão for a atual conta o numero de versões da versão atual
    if ($versao == $maxVersao) {
        $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $numSections =  $row['numSections'];
        }
    }
    //Caso contrário conta o numero de secções da versão escolhida
    else {
        $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_version WHERE idBudget = $idBudget AND idVersion <= $versao;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $numSections =  $row['numSections'];
        }
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

    //Obter dados do cliente associado ao orçamento
    $sql = "SELECT name, contact FROM client WHERE client.id = $idClient;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clientName = $row['name'];
        $clientContact = $row['contact'];
    }

    //Inicia a variavel sem valor porque pode não ter ficha de trabalho associada
    $numFichaTrabalho = "";

    //Obtem dados da ficha de trabalho associado caso tenha
    $sql = "SELECT num, year FROM worksheet WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numWorksheet = $row['num'];
        $yearWorksheet = $row['year'];
        $numFichaTrabalho = "$numWorksheet/$yearWorksheet";
    }
?>
    <link rel="stylesheet" href="./css/editBudget.css">
    <title>OneMarket | <?php echo $numOrçamento; ?> </title>
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
                    <h1><?php echo($numOrçamento) ?></h1>
                </div>
                <form action="" method="GET">
                    <div class="select-container">
                        <input type="hidden" name="idBudget" value="<?= $idBudget ?>">
                        <label for="versao" class="select-label">Versão:</label>
                        <select name="versao" id="versao" onchange="confirmSubmit(this, <?php echo $maxVersao; ?>)">
                            <?php
                                //Seleciona todos os ids das versões e a respetiva data
                                $sql = "SELECT DISTINCT idVersion, created FROM budget_version WHERE idBudget = $idBudget ORDER BY idVersion DESC;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    //Percorre todas as versões e adiciona a opção à select box
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value=\"{$row['idVersion']}\" " . ($row['idVersion'] == $versao ? 'selected' : '') . ">{$row['idVersion']} | {$row['created']}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <?php 
                //Obtem a permissão do administrador, se não tiver permissões de editar declara a variavel como readonly para colocar nos inputs
                $perm = "";
                if (adminPermissions($con, "adm_001", "update") == 0) {
                    $perm = "readonly";
                }
            ?>
            <form action="orcamentoInserir.php?idBudget=<?= $idBudget ?>&op=edit" method=post>
                <div class="bottom-data">
                    <div class="budget">
                        <section>
                            <h2>Dados do Orçamento</h2>
                            <div class="section-row">
                                <div class="section-group" >
                                    <label>Orçamento Numero:</label>
                                    <input type="text" name="numOrcamento" required readonly value="<?php echo $numOrçamento; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Projeto:</label>
                                    <input type="text" name="nomeProjeto" required value="<?php echo $nameBudget; ?>" <?php echo $perm; ?>>
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
                                    <input type="text" name="fichaTrabalho" required readonly value="<?php echo $numFichaTrabalho; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Data de Criação:</label>
                                    <input type="text" name="dataCriacao" readonly required value="<?php echo $createdBudget; ?>">
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Deslocações e Montagem:</label>
                                    <input name="laborPercent" class="percent" type="text" value="<?php echo $maoObraBudget . "%"; ?>" maxlength="6" pattern="[0-9]+(\.[0-9]{1,2})?%" <?php echo $perm; ?>>
                                </div>
                                <div class="section-group">
                                    <label>Total Deslocações e Montagem:</label>
                                    <input type="text" name="totalDeslocacoesMontagens" readonly required value="<?php echo $totalBudget * ($maoObraBudget / 100) . "€"; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Total:</label>
                                    <input type="text" name="total" readonly required value="<?php echo $totalBudget . "€"; ?>">
                                </div>
                                <div class="section-group">
                                    <label>Desconto:</label>
                                    <input name="discountPercent" class="percent" type="text" value="<?php echo $descontoBudget . "%"; ?>" maxlength="6" pattern="[0-9]+(\.[0-9]{1,2})?%" <?php echo $perm; ?>>
                                </div>
                                <div class="section-group">
                                    <label>Total com Desconto:</label>
                                    <input type="text" name="totalDesconto" readonly required value="<?php echo $totalBudget * ($descontoBudget / 100) . "€"; ?>">
                                </div>
                                <script>
                                    //evento para colocar o % nos campos de percentagem
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
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Observações:</label>
                                    <textarea id="overlay-textarea" class="autoExpand" name="observation" rows="1" <?php echo $perm; ?>><?php echo $observacao; ?></textarea>
                                </div>
                            </div>
                        </section>
                    </div>
                    <?php 
                        //Inicia a variavel produtosIndex como 0 para ser incrementada
                        $produtosIndex = 0; 
                        //Ciclo for que percorre todas as secções e adiciona mais 5 que são as que podem ser adicionas por edição
                        for ($i=1; $i <= $numSections + 5; $i++) {
                            //Se a versão for a atual
                            if ($versao == $maxVersao) {
                                //Obtem o numero de produtos que a secção atual tem
                                $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $numProducts = $row['numProducts'];
                                }
                                
                                //obtem o nome da secção atual
                                $sql = "SELECT nameSection FROM budget_sections_products WHERE orderSection = $i AND idBudget = $idBudget;";
                                $result = $con->query($sql);
                                //Se a secção exitir guarda o nome da secção 
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $nomeSecao = $row['nameSection'];
                                    $displayStyle = '';
                                } 
                                //Se não existir secção nessa ordem então declara a variavel displayStyle como display:none para colocar na secção e não a mostrar
                                else {
                                    $nomeSecao = '';
                                    $displayStyle = 'display: none;';
                                }
                            }
                            //Se a versão não for a atual vai buscar os dados à tabela que tem as versões
                            else {
                                $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_version WHERE idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0 AND idVersion = $versao;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $numProducts = $row['numProducts'];
                                }

                                //Obtem o nome da secção da versão selecionada
                                $sql = "SELECT nameSection FROM budget_version WHERE orderSection = $i AND idBudget = $idBudget AND idVersion = $versao;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $nomeSecao = $row['nameSection'];
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
                                            value="<?php echo $nomeSecao; ?>"
                                            style="width: 100%;" 
                                            <?php echo $perm; ?>
                                        />
                                        <datalist id='datalistSection'>
                                            <?php
                                                //Obtem todos os nomes diferentes de secções para a datalist ao inserir o nome da secção
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
                                                    <th style="width: 65px; text-align: center">Nº</th>
                                                    <th style="width: 150px; text-align: center">N/REF</th>
                                                    <th style="width: 300px; text-align: center">Designação</th>
                                                    <th style="width: 65px;">Quantidade</th>
                                                    <th  style="text-align: center">Descrição</th>
                                                    <th style="width: 100px;">Preço Unitário</th>
                                                    <th style="width: 100px;">Preço Total</th>
                                                </tr>
                                            </thead>
                                            <?php 
                                                //Percorre todos os produtos masi 10 que são os que podem ser adicionados
                                                for ($j=1; $j <= $numProducts + 10; $j++) { 
                                                    //incrementa o index dos produtos que é o numero que aparece antes de cada produto, para os contar
                                                    $produtosIndex++;
                                                    //Reinicia o valor das variaveis que contêm os dados do produto
                                                    $refProduct = '';
                                                    $nameProduct = '';
                                                    $amountProduct = 0;
                                                    $descriptionProduct = '';
                                                    $valueProduct = 0;
                                                    //Se for o primeiro produto ou o numero do produto for menor ou igual ao numero total de produtos desta secção então mostra o produto
                                                    if ($j == 1 || $j <= $numProducts) { ?>
                                                        <tbody class="produtos">
                                                            <tr>
                                                                <?php 
                                                                    //Se a versão selecionada for a atual
                                                                    if ($versao == $maxVersao) {
                                                                        //Obtem os dados atuais do produto
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
                                                                    //Se for de outra versão
                                                                    else {
                                                                        //Obtem os dados dos produtos da versão selecionada
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
                                                                //Mostra os dados dos produtos, proibindo de editar se não for a versão atual ou se não tiver permissões
                                                                ?>
                                                                <td><input type="text" class="id" name="secao_<?php echo $i; ?>_produto_index_<?php echo $j; ?>" value="100" readonly></td>
                                                                <td><input type="search" list="datalistProduct" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $i; ?>_produto_ref_<?php echo $j; ?>" value="<?php echo $refProduct; ?>" oninput="atualizarCampos(this);" <?php if (adminPermissions($con, "adm_001", "update") == 0 || $versao < $maxVersao) {echo "readonly";}?>></td>
                                                                <td><input type="text" class="designacao" name="secao_<?php echo $i; ?>_produto_designacao_<?php echo $j; ?>" value="<?php echo $nameProduct; ?>" <?php if (adminPermissions($con, "adm_001", "update") == 0 || $versao < $maxVersao) {echo "readonly";}?>></td>
                                                                <td><input type="number" class="quantidade" name="secao_<?php echo $i; ?>_produto_quantidade_<?php echo $j; ?>" min="0" value="<?php if (!isset($amountProduct)) {echo $amountProduct;} else {echo 1;} ?>" oninput="atualizarPrecoTotal(this)" <?php if (adminPermissions($con, "adm_001", "update") == 0 || $versao < $maxVersao) {echo "readonly";}?>></td>
                                                                <td><textarea rows="1" class="autoExpand" name="secao_<?php echo $i; ?>_produto_descricao_<?php echo $j; ?>" <?php if (adminPermissions($con, "adm_001", "update") == 0 || $versao < $maxVersao) {echo "readonly";}?>><?php echo $descriptionProduct; ?></textarea></td>
                                                                <td><input type="text" class="valor" name="secao_<?php echo $i; ?>_produto_preco_unitario_<?php echo $j; ?>" value="<?php echo $valueProduct; ?>" readonly></td>
                                                                <td><input type="text" class="valorTotal" name="secao_<?php echo $i; ?>_produto_preco_total_<?php echo $j; ?>" value="<?php echo $amountProduct * $valueProduct . "€";?>" readonly></td>
                                                            </tr>
                                                        </tbody>
                                                        <datalist id='datalistProduct'>
                                                            <?php
                                                                //Obtem todas as referencias dos produtos que estao ativos
                                                                $sql = "SELECT reference FROM product WHERE product.active = 1 ;";
                                                                $result = $con->query($sql);
                                                                if ($result->num_rows > 0) {
                                                                    //Percorre todos os produtos e adiciona-os como opção na dataList
                                                                    while ($row = $result->fetch_assoc()) {
                                                                        echo "<option>$row[reference]</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </datalist>
                                                    <?php //Se não for o primeiro produto ou o numero do produto não for menor ou igual ao numero total de produtos desta secção então não mostra o produto
                                                    } else { 
                                                        ?>
                                                        <tbody class="produtos" style="display: none;">
                                                            <tr>
                                                                <td><input type="text" class="id" name="secao_<?php echo $i; ?>_produto_index_<?php echo $j; ?>" value="100" readonly></td>
                                                                <td><input type="search" list="datalistProduct" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $i; ?>_produto_ref_<?php echo $j; ?>" oninput="atualizarCampos(this);"></td>
                                                                <td><input type="text" class="designacao" name="secao_<?php echo $i; ?>_produto_designacao_<?php echo $j; ?>"></td>
                                                                <td><input type="number" class="quantidade" name="secao_<?php echo $i; ?>_produto_quantidade_<?php echo $j; ?>" min="0" value="1" oninput="atualizarPrecoTotal(this)"></td>
                                                                <td><textarea rows="1" style="height: 42px;" class="autoExpand" name="secao_<?php echo $i; ?>_produto_descricao_<?php echo $j; ?>"></textarea></td>
                                                                <td><input type="text" class="valor" name="secao_<?php echo $i; ?>_produto_preco_unitario_<?php echo $j; ?>" readonly></td>
                                                                <td><input type="text" class="valorTotal" name="secao_<?php echo $i; ?>_produto_preco_total_<?php echo $j; ?>" readonly></td>
                                                            </tr>
                                                        </tbody>
                                                        <datalist id='datalistProduct'>
                                                            <?php
                                                                //Obtem todas as referencias dos produtos que estao ativos
                                                                $sql = "SELECT reference FROM product WHERE product.active = 1 ;";
                                                                $result = $con->query($sql);
                                                                if ($result->num_rows > 0) {
                                                                    //Percorre todos os produtos e adiciona-os como opção na dataList
                                                                    while ($row = $result->fetch_assoc()) {
                                                                        echo "<option>$row[reference]</option>";
                                                                    }
                                                                }
                                                            ?>
                                                        </datalist>
                                                    <?php } ?>
                                                <?php } 
                                                //Se o administrador tiver permissões de edição e a versão for a atual mostra o botão de adicionar produtos
                                                if (adminPermissions($con, "adm_001", "update") == 1 && $versao == $maxVersao) {
                                                    echo "<button type=\"button\" onclick=\"adicionarProduto(" . ($i - 1) . ")\">Adicionar Produto</button>";
                                                }
                                            ?>
                                        </table>
                                    </div>
                                </section>
                            </div>
                    <?php } ?>
                    <script>
                        //Atualiza o numero dos produtos quando a página é carregada
                        document.addEventListener("DOMContentLoaded", function() {
                            atualizarIndicesGlobais();
                        });

                        //Obtem a versão selecionada
                        function confirmSubmit(selectElement, maxVersao) {
                            //Obtem a versão selecionada
                            const selectedVersion = selectElement.value;
                            //Inicia a variavel com a resposta do administrador como true
                            const userConfirmed = true
                            //Se a versão selecionada for a atual então pergunta ao administrador se pretende continuar
                            if (selectedVersion != maxVersao) {
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

                        // Salva o valor inicial ao carregar a página
                        document.getElementById('versao').addEventListener('focus', function() {
                            this.setAttribute('data-previous', this.value);
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

                        //Função para tornar visivel o proximo produto ao clicar no botão
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

                        //Função para tornar visivel a proxima secção ao clicar no botão
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

                        //Função para atualizar os campos do produto de acordo com a referencia inserida
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

                        //Atualiza o preço total, multiplica o preço da unidade pela quantidade
                        function atualizarPrecoTotal(elemento) {
                            const linha = elemento.closest("tr");
                            const quantidadeCampo = linha.querySelector(".quantidade");
                            const valorTotalCampo = linha.querySelector(".valorTotal");
                            const valorCampo = linha.querySelector(".valor");

                            const quantidade = parseFloat(quantidadeCampo.value) || 0;
                            const valor = parseFloat(valorCampo.value) || 0;

                            valorTotalCampo.value = (quantidade * valor).toFixed(2);
                        }

                        //Função para imprimir o orçamento
                        function budgetPrint(idBudget) {
                            // Abre a outra página em uma nova janela
                            const printWindow = window.open('orcamentoImpressao.php?idBudget=' + idBudget, '_blank');

                            // Aguarda a página carregar completamente
                            printWindow.onload = () => {
                                // Chama o método de impressão da nova página
                                printWindow.print();
                            };
                        }
                    </script>
                </div>
                <?php 
                    //Se o administrador tiver permissão para editar e a versão for a atual mostra o botão de adicionar secção e de salvar alterações
                    if (adminPermissions($con, "adm_001", "update") == 1 && $versao == $maxVersao) {
                        echo "
                            <button id=bottomButton type=\"button\" onclick=\"adicionarSecao()\">Adicionar Secção</button>
                            <button id=botSaveBudget type=\"submit\">Guardar Alterações</button>
                        ";
                    }
                ?>
                <button id=botPrintBudget type="button" onclick="budgetPrint(<?php echo $idBudget; ?>)">Imprimir</button>
            </form>
        </main>
    </div>
</body>
</html>
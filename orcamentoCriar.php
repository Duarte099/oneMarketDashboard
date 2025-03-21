<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 2;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard    
    if (adminPermissions($con, "adm_001", "inserir") == 0) {
        header('Location: dashboard.php');
        exit();
    }
    
    //Obtem o id do cliente seleciona para ser associado ao orçamento via GET
    $idCliente = $_GET['idClient'];

    //Obtem o cliente cujo id é igual ao recebido via GET
    $sql = "SELECT * FROM client WHERE id = '$idCliente'";
    $result = $con->query($sql);
    //Se não encontrar nenhum cliente redireciona para a dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    //Caso contrário pega as informações do cliente
    else {
        $row = $result->fetch_assoc();
        $nameClient = $row['name'];
        $contactClient = $row['contact'];
    }

    //Obtem o ano atual
    $anoAtual = date('Y');

    //Obtem o numero maior de todos os orçamentos do ano atual
    $sql = "SELECT MAX(num) AS maior_numero FROM budget WHERE year = $anoAtual;";
    $result = $con->query($sql);
    //Se houver orçamento no ano atual soma mais um ao numero maior
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $proximo_numero = $row['maior_numero'] + 1;
    }
    //senão é o primeiro orçamento, numero 1
    else {
        $proximo_numero = 1;
    }
    //Forma o numero do orçamento, junção entre o numero e o ano atual
    $numOrcamento = "$proximo_numero/$anoAtual";
?>
    <link rel="stylesheet" href="./css/novoOrcamento.css">
    <title>OneMarket | Novo Orçamento</title>
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

        <form action="orcamentoInserir.php?idClient=<?= $idCliente ?>&op=save" method=post>
            <main>
                <div class="header">
                    <div class="left">
                        <h1>Novo Orçamento</h1>
                    </div>
                </div>
                <div class="bottom-data">
                    <div class="budget">
                        <section>
                                <h2>Dados do Orçamento</h2>
                                <div class="section-row">
                                    <div class="section-group" >
                                        <label>Orçamento Numero:</label>
                                        <input type="text" name="numOrcamento" required readonly value="<?php echo $numOrcamento; ?>">
                                    </div>
                                    <div class="section-group">
                                        <label>Projeto:</label>
                                        <input type="text" name="nomeProjeto">
                                    </div>
                                    <div class="section-group">
                                        <label>Cliente:</label>
                                        <input type="text" name="cliente" id="new-budget" readonly value="<?php echo $nameClient; ?>">
                                    </div>
                                    <div class="section-group">
                                        <label>Contacto:</label>
                                        <input type="text" name="contacto" required readonly value="<?php echo $contactClient; ?>">
                                    </div>
                                    <div class="section-group">
                                        <label>Ficha de Trabalho:</label>
                                        <input type="text" name="fichaTrabalho" readonly>
                                    </div>
                                    <div class="section-group">
                                        <label>Data de Criação:</label>
                                        <input type="text" name="dataCriacao" readonly>
                                    </div>
                                </div>
                                <div class="section-row">
                                    <div class="section-group">
                                        <label>Deslocações e Montagem:</label>
                                        <input name="laborPercent" class="percent" type="text" maxlength="6" pattern="[0-9]+(\.[0-9]{1,2})?%">
                                    </div>
                                    <div class="section-group">
                                        <label>Total Deslocações e Montagem:</label>
                                        <input type="text" name="totalDeslocacoesMontagens" readonly>
                                    </div>
                                    <div class="section-group">
                                        <label>Total:</label>
                                        <input type="text" name="total" readonly>
                                    </div>
                                    <div class="section-group">
                                        <label>Desconto:</label>
                                        <input name="discountPercent" class="percent" type="text" maxlength="6" pattern="[0-9]+(\.[0-9]{1,2})?%">
                                    </div>
                                    <div class="section-group">
                                        <label>Total com Desconto:</label>
                                        <input type="text" name="totalDesconto" readonly>
                                    </div>
                                    <script>
                                        //evento para colocar o % nos respetivos campos
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
                                        <textarea id="overlay-textarea" class="autoExpand" name="observation" rows="1"></textarea>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="budget">
                            <!-- Secções e Produtos -->
                            <section id="secoes">
                                <h2>Secções</h2>
                                <?php //Ciclo for para gerar 20 campos de secções 
                                for ($i=1; $i <= 20; $i++) { 
                                    //Se for a primeira secção torna-a visivel
                                    if ($i == 1) { ?>
                                        <div class="secao" style="position: relative; width: 100%;">
                                            <h3>Secção <?php echo $i; ?>: </h3>
                                            <input type="search" id="datalistSecao" name="seccao_nome_<?php echo $i; ?>" placeholder="Nome da secção">
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
                                        </div>
                                    <?php // Se não for a primeira secção torna-a invisivel
                                    } else { ?>
                                        <div class="secao" style="position: relative; width: 100%; display: none">
                                            <h3>Secção <?php echo $i; ?>: </h3>
                                            <input type="search" id="datalistSecao" name="seccao_nome_<?php echo $i; ?>" placeholder="Nome da secção">
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
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <script>
                                    //Função para tornar visivel a proxima secção ao clicar no botão
                                    function adicionarSecao() {
                                        // Seleciona a seção com base no índice fornecido
                                        const secao = document.querySelectorAll(".secao");
                                        if (secao) {
                                            // Itera sobre os produtos para encontrar o próximo que está oculto
                                            for (let i = 0; i < secao.length; i++) {
                                                if (secao[i].style.display === "none") {
                                                    secao[i].style.display = ""; // Torna visível
                                                    return; // Encerra a função após exibir o próximo produto
                                                }
                                            }
                                            alert("Atingiu o máximo de secções criadas por alteração."); // Mensagem caso todos os produtos já estejam visíveis
                                        }
                                    }
                                </script>
                                <button id=bottomButton type="button" onclick="adicionarSecao()">Adicionar Secção</button>
                                <button id=botSaveBudget type="submit" class="botao-salvar-orcamento">Criar Orçamento</button>         
                            </section>
                        </div>
                    </div>
                </div>
            </main>
        </form>
    </div>
</body>
</html>
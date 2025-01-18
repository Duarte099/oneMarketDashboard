<?php 
    session_start();

    $estouEm = 2;

    include('../db/conexao.php');

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    if (adminPermissions("adm_001", "inserir") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    // $op = '';
    $idCliente = $_GET['idClient'];

    // if ($op == 'save') {
    //     $numSeccao  = 10;
    //     for ($i=1; $i<=$numSeccao; $i++) {
    //         if (isset($_POST['produto_ref_' . $i])) {
    //             $produto_ref = $_POST['produto_ref_' . $i];
    //             echo "produto_ref = $produto_ref";
    //         }
    //     }
    // }

    $anoAtual = date('Y');
    $sql = "SELECT MAX(num) AS maior_numero FROM budget WHERE year = $anoAtual;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $proximo_numero = $row['maior_numero'] + 1;
    }
    else {
        $proximo_numero = 1;
    }
    $numOrcamento = "$proximo_numero/$anoAtual";

    $sql = "SELECT name, contact FROM client WHERE client.id = $idCliente;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameClient = $row['name'];
        $contactClient = $row['contact'];
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/novoOrcamento.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Novo Orçamento</title>
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
                                <?php for ($i=1; $i <= 20; $i++) { 
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
                                    <?php } else { ?>
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
        <!-- <input type=text id=numSeccao name=numSeccao value=1> -->
        </form>
    </div>
</body>
</html>
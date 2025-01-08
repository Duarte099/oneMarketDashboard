<?php 

    $estouEm = 3;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $produtosIndex = 0;
    
    // $op = '';
    $idBudget = $_GET['idBudget'];

    $idAdmin = $_SESSION['id'];

    $sql = "SELECT budget.idClient FROM budget WHERE budget.id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
    }

    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/novaFichaTrabalho.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Nova Ficha de Trabalho</title>
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

        <form action="inserirFichaTrabalho.php?idBudget=<?= $idBudget ?>&op=save" method=post>
            <main>
                <div class="header">
                    <div class="left">
                        <h1>Nova Ficha de Trabalho</h1>
                    </div>
                </div>
                <div class="bottom-data">
                    <div class="budget">
                        <section>
                            <h2>Dados da Ficha de Trabalho</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Ficha Numero:</label>
                                    <input type="text" name="numFicha" required readonly value="<?php
                                        $anoAtual = date('Y');
                                        $sql = "SELECT MAX(num) AS maior_numero FROM worksheet WHERE year = $anoAtual;";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $proximo_numero = $row['maior_numero'] + 1;
                                        }
                                        else {
                                            $proximo_numero = 1;
                                        }
                                        $numFicha = "$proximo_numero/$anoAtual";
                                        echo $numFicha;
                                    ?>">
                                </div>
                                <div class="section-group">
                                    <label>Cliente:</label>
                                    <input type="text" name="cliente" id="new-budget" required readonly value="<?php 
                                        $sql = "SELECT client.name FROM client WHERE client.id = $idClient;";
                                        $result = $con->query($sql);
                                        $row = $result->fetch_assoc();
                                        echo $row['name'];
                                    ?>">
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="text" name="contacto" required readonly value="<?php 
                                        $sql = "SELECT client.contact FROM client WHERE client.id = $idClient;";
                                        $result = $con->query($sql);
                                        $row = $result->fetch_assoc();
                                        echo $row['contact'];
                                    ?>">
                                </div>
                                <div class="section-group">
                                    <label>Orçamento:</label>
                                    <input type="text" name="orcamento" required readonly value="<?php 
                                        $sql = "SELECT num, year FROM budget WHERE id = $idBudget;";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $numBudget = $row['num'];
                                            $yearBudget = $row['year'];
                                        }
                                        $numOrçamento = "$numBudget/$yearBudget";
                                        echo $numOrçamento;
                                    ?>">
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
                                    <input type="text" name="createdby" readonly required value="<?php 
                                        $sql = "SELECT administrator.name FROM administrator WHERE id = $idAdmin;";
                                        $result = $con->query($sql);
                                        $row = $result->fetch_assoc();
                                        echo $row['name'];
                                    ?>">
                                </div>
                            </div>
                        </section>

                        <!-- Secções e Produtos -->
                        <section id="secoes">
                            <h2>Secções</h2>
                            <?php 
                                $produtosIndex = 0;
                                for ($i=1; $i <= $numSections; $i++) { 
                                    $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0;";
                                    $result = $con->query($sql);
                                    if ($result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        $numProducts = $row['numProducts'];
                                    }

                                    $sql = "SELECT budget_sections.name
                                            FROM budget_sections 
                                            JOIN budget_sections_products 
                                            ON budget_sections.id = budget_sections_products.idSection 
                                            AND budget_sections_products.orderSection = $i
                                            AND budget_sections_products.idBudget = $idBudget;";
                                    $result = $con->query($sql);
                                
                                    if ($result->num_rows > 0) {
                                        $row = $result->fetch_assoc();
                                        $nomeSecao = htmlspecialchars($row['name']);
                                    }?>
                                    <div class="secao" style="position: relative; width: 100%;">
                                        <h3>Secção <?php echo $i; ?>:</h3>
                                        <input type="text" 
                                            id="search-box-<?php echo $i; ?>" 
                                            name="seccao_nome_<?php echo $i; ?>" 
                                            placeholder="Nome da secção" 
                                            value="<?php echo $nomeSecao; ?>" readonly/>
                                        <div id="secoesModal-<?php echo $i; ?>" class="modal">
                                            <div id="results-container-<?php echo $i; ?>" class="results-container"></div>
                                        </div>
                                        <table id = "table">
                                            <thead>
                                                <tr>
                                                    <th>Check</th>
                                                    <th>Armazém</th>
                                                    <th>Nº</th>
                                                    <th>N/REF</th>
                                                    <th>Designação</th>
                                                    <th>Quantidade</th>
                                                    <th class="inputs-th">Observações</th>
                                                </tr>
                                            </thead>
                                            <?php 
                                                for ($j=1; $j <= $numProducts; $j++) { 
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
                                                                $sql = "SELECT refProduct, nameProduct, amountProduct, descriptionProduct, sizeProduct FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderProduct = '$j' AND orderSection = '$i';";
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
                                                            <td><input type="checkbox" class="check" name="secao_<?php echo $i; ?>_produto_check_<?php echo $j; ?>"></td>
                                                            <td><input type="checkbox" class="armazem" name="secao_<?php echo $i; ?>_produto_armazem_<?php echo $j; ?>"></td>
                                                            <td><input type="number" class="id" name="secao_<?php echo $i; ?>_produto_index_<?php echo $j; ?>" readonly></td>
                                                            <td><input type="text" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $i; ?>_produto_ref_<?php echo $j; ?>" value = "<?php echo $refProduct; ?>" readonly></td>
                                                            <td><input type="text" class="designacao" name="secao_<?php echo $i; ?>_produto_designacao_<?php echo $j; ?>" value = "<?php echo $nameProduct; ?>" readonly></td>
                                                            <td><input type="number" class="quantidade" name="secao_<?php echo $i; ?>_produto_quantidade_<?php echo $j; ?>" value = "<?php if (!isset($amountProduct)) {echo $amountProduct;} else {echo 1;} ?>" readonly></td>
                                                            <td class="inputs-td">
                                                                <input type="text" class="descricao" name="secao_<?php echo $i; ?>_produto_observacao_<?php echo $j; ?>" value = "<?php echo $descriptionProduct; ?>">
                                                                <input type="text" class="tamanho" name="secao_<?php echo $i; ?>_produto_tamanho_<?php echo $j; ?>">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <div id="produtosModal-<?php echo $produtosIndex; ?>" class="modal">
                                                        <div id="results-container-<?php echo $produtosIndex; ?>" class="results-container"></div>
                                                    </div>
                                                <?php } 
                                            ?>
                                        </table>
                                    </div>
                            <?php } ?>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    atualizarIndicesGlobais();
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
                            </script>
                            <button id=botSaveWorksheet type="submit">Criar Ficha de Trabalho</button>
                        </section>
                    </div>
                </div>
            </main>
        </form>
    </div>
</body>
</html>
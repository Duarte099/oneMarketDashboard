<?php 
    session_start();
    include('../db/conexao.php'); 
    $estouEm = 3;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    if (adminPermissions("adm_002", "view") == 0) {
        header('Location: index.php');
        exit();
    }

    $produtosIndex = 0;
    
    // $op = '';
    $idBudget = $_GET['idBudget'];

    $idAdmin = $_SESSION['id'];

    $sql = "SELECT idClient, num, year FROM budget WHERE budget.id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
    }
    $numOrcamento = "$numBudget/$yearBudget";

    $sql = "SELECT name, contact FROM client WHERE client.id = $idClient;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameClient =  $row['name'];
        $contactClient = $row['contact'];
    }

    $sql = "SELECT name FROM administrator WHERE id = $idAdmin;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nameAdministrator =  $row['name'];
    }


    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }

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

    $sql = "SELECT num, year FROM budget WHERE id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
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
        <main>
            <div class="header">
                <div class="left">
                    <h1>Novo Orçamento</h1>
                </div>
                <form action="" method="GET">
                    <div class="select-container">
                        <input type="hidden" name="idBudget" value="<?= $idBudget ?>">
                        <label for="versao" class="select-label">Versão:</label>
                        <select name="versao" id="versao" onchange="confirmSubmit(this, <?php echo $maxVersao; ?>)">
                            <!-- <?php
                                $sql = "SELECT DISTINCT idVersion, created FROM budget_version WHERE idBudget = $idBudget ORDER BY idVersion DESC;";
                                $result = $con->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value=\"{$row['idVersion']}\" " . ($row['idVersion'] == $versao ? 'selected' : '') . ">{$row['idVersion']} | {$row['created']}</option>";
                                    }
                                }
                            ?> -->
                        </select>
                    </div>
                </form>
            </div>
            <form action="inserirFichaTrabalho.php?idBudget=<?= $idBudget ?>&op=save" method=post>
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
                        $produtosIndex = 0;
                        for ($i=1; $i <= $numSections; $i++) {
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
                                $nomeSecao = $row['nameSection'];
                            }

                            ?>
                            <div class="worksheet">
                                <section id="secoes">
                                    <div class="secao">
                                        <h3>Secção <?php echo $i; ?>:</h3>
                                        <input type="text" 
                                            id="search-box-<?php echo $i; ?>" 
                                            name="seccao_nome_<?php echo $i; ?>" 
                                            placeholder="Nome da secção" 
                                            value="<?php echo $nomeSecao; ?>" readonly/>
                                        <table id="table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 55px;">Check</th>
                                                    <th style="width: 55px;">Armazém</th>
                                                    <th style="width: 65px; text-align: center;">Nº</th>
                                                    <th style="width: 150px;">N/REF</th>
                                                    <th style="width: 300px;">Designação</th>
                                                    <th style="width: 65px;">Quantidade</th>
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
                                                            <td><input type="checkbox" style="margin-left 20px;" class="check" name="secao_<?php echo $i; ?>_produto_check_<?php echo $j; ?>"></td>
                                                            <td><input type="checkbox" style="margin-left 20px;" class="armazem" name="secao_<?php echo $i; ?>_produto_armazem_<?php echo $j; ?>"></td>
                                                            <td><input type="text" class="id" name="secao_<?php echo $i; ?>_produto_index_<?php echo $j; ?>" readonly></td>
                                                            <td><input type="text" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $i; ?>_produto_ref_<?php echo $j; ?>" value = "<?php echo $refProduct; ?>" readonly></td>
                                                            <td><input type="text" class="designacao" name="secao_<?php echo $i; ?>_produto_designacao_<?php echo $j; ?>" value = "<?php echo $nameProduct; ?>" readonly></td>
                                                            <td><input type="text" class="quantidade" name="secao_<?php echo $i; ?>_produto_quantidade_<?php echo $j; ?>" value = "<?php if (!isset($amountProduct)) {echo $amountProduct;} else {echo 1;} ?>" readonly></td>
                                                            <td class="inputs-td">
                                                                <input type="text" class="descricao" name="secao_<?php echo $i; ?>_produto_observacao_<?php echo $j; ?>" value = "<?php echo $descriptionProduct; ?>">
                                                                <input type="text" class="tamanho" name="secao_<?php echo $i; ?>_produto_tamanho_<?php echo $j; ?>">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                <?php } 
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
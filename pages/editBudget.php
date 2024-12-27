<?php 

    include('../db/conexao.php'); 
    $estouEm = 2;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $produtosIndex = 0;
    
    // $op = '';
    $idBudget = $_GET['idBudget'];

    $sql = "SELECT budget.idClient FROM budget WHERE budget.id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
    }

    $sql = "SELECT COUNT(*) AS numSections FROM budget_sections_products WHERE budget_sections_products.idBudget = $idBudget AND idProduct = 0;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }

    // if (isset($_GET['op'])) $op = $_GET['op'];

    // if ($op == 'save') {
    //     $numSeccao  = 10;
    //     for ($i=1; $i<=$numSeccao; $i++) {
    //         if (isset($_POST['produto_ref_' . $i])) {
    //             $produto_ref = $_POST['produto_ref_' . $i];
    //             echo "produto_ref = $produto_ref";
    //         }
    //     }
    // }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/editBudget.css">
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

        <form action="inserirOrcamento.php?idBudget=<?= $idBudget ?>&op=edit" method=post>
            <main>
                <div class="header">
                    <div class="left">
                        <h1>Editar Orçamento</h1>
                    </div>
                </div>
                <div class="bottom-data">
                    <div class="budget">
                        <section>
                            <h2>Dados do Orçamento</h2>
                            <!-- <button id=botSaveBudget2 data-num="4" data-id="new">XPTO</button> -->
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Orçamento Numero:</label>
                                    <input type="text" name="numOrcamento" required readonly value="<?php
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
                                    <label>Ficha de Trabalho:</label>
                                    <input type="text" name="fichaTrabalho" required readonly value="<?php 
                                        $sql = "SELECT worksheet.name FROM worksheet WHERE worksheet.idBudget = $idBudget;";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            echo $row['name'];
                                        }
                                    ?>">
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Projeto:</label>
                                    <input type="text" name="nomeProjeto" required value="<?php 
                                       $sql = "SELECT budget.name FROM budget WHERE budget.id = $idBudget;";
                                       $result = $con->query($sql);
                                       if ($result->num_rows > 0) {
                                           $row = $result->fetch_assoc();
                                           echo $row['name'];
                                       }  
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

                        <!-- Secções e Produtos -->
                        <section id="secoes">
                            <h2>Secções</h2>
                            <?php 
                                $produtosIndex = 0; 
                                for ($i=1; $i <= 20; $i++) { 
                                    $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i';";
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
                                        $displayStyle = '';
                                    } else {
                                        $nomeSecao = '';
                                        $displayStyle = 'display: none;';
                                    }?>
                                    <div class="secao" style="position: relative; width: 100%; <?php echo $displayStyle; ?>">
                                        <h3>Secção <?php echo $i; ?>:</h3>
                                        <input type="text" 
                                            id="search-box-<?php echo $i; ?>" 
                                            name="seccao_nome_<?php echo $i; ?>" 
                                            placeholder="Nome da secção" 
                                            oninput="performSearchSecoes(this, <?php echo $i; ?>)" 
                                            value="<?php echo $nomeSecao; ?>" />
                                        <div id="secoesModal-<?php echo $i; ?>" class="modal">
                                            <div id="results-container-<?php echo $i; ?>" class="results-container"></div>
                                        </div>
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
                                                for ($j=1; $j <= 10; $j++) { 
                                                    $produtosIndex++; ?>
                                                    <?php if ($j == 1 || $j <= $numProducts) { ?>
                                                        <tbody class="produtos">
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
                                            ?>
                                            <button type="button" onclick="adicionarProduto(<?php echo $i - 1; ?>)">Adicionar Produto</button>
                                        </table>
                                    </div>
                            <?php } ?>
                            <script>
                                const searchDataSecoes = [];
                                const searchDataProdutos = [];
                                
                                $.ajax({
                                    url: 'ajax.obterSecoes.php',
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(data) {
                                        searchDataSecoes.push(...data);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erro ao buscar os dados:', error);
                                    }
                                });

                                $.ajax({
                                    url: 'ajax.obterProdutos.php',
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(data) {
                                        searchDataProdutos.push(...data);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erro ao buscar os dados:', error);
                                    }
                                });

                                document.addEventListener("DOMContentLoaded", function() {
                                    atualizarIndicesGlobais();
                                });

                                document.addEventListener("click", function (event) {
                                    const modals = document.querySelectorAll(".modal");

                                    modals.forEach((modal) => {
                                        const input = modal.previousElementSibling;
                                        if (!modal.contains(event.target) && event.target !== input) {
                                            modal.style.display = "none";
                                        }
                                    });
                                });

                                // var template = [
                                //     '<tbody class="produtos">',
                                //         '<tr>',
                                //             '<td><input type="number" class="id" name="secao_<?php echo $i; ?>_index_<?php echo $produtosIndex; ?>" readonly></td>',
                                //             '<td><input type="text" id="reference-<?php echo $produtosIndex; ?>" name="secao_<?php echo $i; ?>_produto_ref_<?php echo $produtosIndex; ?>" oninput="atualizarCampos(this); performSearchProdutos(this, <?php echo $produtosIndex; ?>);"></td>',
                                //             '<td><input type="text" class="designacao" name="secao_<?php echo $i; ?>_designacao_<?php echo $produtosIndex; ?>" readonly></td>',
                                //             '<td><input type="number" class="quantidade" name="secao_<?php echo $i; ?>_quantidade_<?php echo $produtosIndex; ?>" value="1" oninput="atualizarPrecoTotal(this)"></td>',
                                //             '<td><input type="text" class="descricao" name="secao_<?php echo $i; ?>_descricao_<?php echo $produtosIndex; ?>" ></td>',
                                //             '<td><input type="text" class="valor" name="secao_<?php echo $i; ?>_preco_unitario_<?php echo $produtosIndex; ?>" readonly></td>',
                                //             '<td><input type="text" class="valorTotal" name="secao_<?php echo $i; ?>_preco_total_<?php echo $produtosIndex; ?>" readonly></td>',
                                //         '</tr>',
                                //     '</tbody>',
                                //     '<div id="produtosModal-<?php echo $produtosIndex; ?>" class="modal">',
                                //         '<div id="results-container-<?php echo $produtosIndex; ?>" class="results-container"></div>',
                                //     '</div>'
                                //     ].join('');

                                //     var container = $('#table');

                                //     $('#click-me').on('click', function(){
                                //     container.append(template);
                                //     });

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
                                    const secao = document.querySelectorAll(".secao");
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

                                function performSearchSecoes(searchBox, num) {
                                    const modal = document.getElementById('secoesModal-'+num);
                                    const query = searchBox.value.toLowerCase();
                                    modal.innerHTML = "";

                                    if (query) {
                                        const filteredResults = searchDataSecoes.filter(item =>
                                        item.toLowerCase().includes(query)
                                        );

                                        if (filteredResults.length > 0) {
                                            modal.style.display = 'block';
                                            filteredResults.forEach(result => {
                                                const resultDiv = document.createElement("div");
                                                resultDiv.classList.add("result-item");
                                                resultDiv.textContent = result;
                                                resultDiv.onclick = () => selectResultSecoes(result, searchBox, num);
                                                modal.appendChild(resultDiv);
                                            });
                                        } else {
                                            modal.style.display = "none";
                                        }
                                    } else {
                                        modal.style.display = "none";
                                    }
                                }

                                function selectResultSecoes(result, searchBox, num) {
                                    const modal = document.getElementById('secoesModal-'+num);
                                    searchBox.value = result;
                                    modal.style.display = "none";
                                }

                                function performSearchProdutos(searchBox, num) {
                                    const modal = document.getElementById('produtosModal-'+num);
                                    const query = searchBox.value.toLowerCase();
                                    modal.innerHTML = "";

                                    if (query) {
                                        const filteredResults = searchDataProdutos.filter(item =>
                                        item.toLowerCase().includes(query)
                                        );

                                        if (filteredResults.length > 0) {
                                            modal.style.display = 'block';
                                            filteredResults.forEach(result => {
                                                const resultDiv = document.createElement("div");
                                                resultDiv.classList.add("result-item");
                                                resultDiv.textContent = result;
                                                resultDiv.onclick = () => selectResultProdutos(result, searchBox, num);
                                                modal.appendChild(resultDiv);
                                            });
                                        } else {
                                            modal.style.display = "none";
                                        }
                                    } else {
                                        modal.style.display = "none";
                                    }
                                }

                                function selectResultProdutos(result, searchBox, num) {
                                    const modal = document.getElementById('produtosModal-'+num);
                                    searchBox.value = result;
                                    atualizarCampos(searchBox);
                                    modal.style.display = "none";
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
                            <button type="button" onclick="adicionarSecao()">Adicionar Secção</button>
                            <button id=botSaveBudget type="submit">Guardar Alterações</button>         
                        </section>
                    </div>
                </div>
            </main>
        <!-- <input type=text id=numSeccao name=numSeccao value=1> -->
        </form>
    </div>
</body>
</html>
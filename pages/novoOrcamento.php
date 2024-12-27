<?php 

    include('../db/conexao.php'); 
    $estouEm = 2;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $op = '';
    $idCliente = $_GET['idClient'];
    if (isset($_GET['op'])) $op = $_GET['op'];

    if ($op == 'save') {
        $numSeccao  = 10;
        for ($i=1; $i<=$numSeccao; $i++) {
            if (isset($_POST['produto_ref_' . $i])) {
                $produto_ref = $_POST['produto_ref_' . $i];
                echo "produto_ref = $produto_ref";
            }
        }
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

        <form action="inserirOrcamento.php?idClient=<?= $idCliente ?>&op=save" method=post>
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
                            <!-- <button id=botSaveBudget2 data-num="4" data-id="new">XPTO</button> -->
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Orçamento Numero:</label>
                                    <input type="text" name="numOrcamento" required readonly value="<?php
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
                                        $numOrçamento = "$proximo_numero/$anoAtual";
                                        echo $numOrçamento;
                                    ?>">
                                </div>
                                <div class="section-group">
                                    <label>Cliente:</label>
                                    <input type="text" name="cliente" id="new-budget" required readonly value="<?php 
                                        $sql = "SELECT client.name FROM client WHERE client.id = $idCliente;";
                                        $result = $con->query($sql);
                                        $row = $result->fetch_assoc();
                                        echo $row['name'];
                                    ?>">
                                </div>
                                <div class="section-group">
                                    <label>Ficha de Trabalho:</label>
                                    <input type="text" name="fichaTrabalho" required readonly value="<?php 
                                        $sql = "SELECT worksheet.name FROM worksheet INNER JOIN budget ON worksheet.idBudget = budget.id;";
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
                                    <input type="text" name="nomeProjeto" required>
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="text" name="contacto" required readonly value="<?php 
                                        $sql = "SELECT client.contact FROM client WHERE client.id = $idCliente;";
                                        $result = $con->query($sql);
                                        $row = $result->fetch_assoc();
                                        echo $row['contact'];
                                    ?>">
                                </div>
                                <div class="section-group">
                                    <label>Data de Criação:</label>
                                    <input type="text" name="dataCriacao" readonly required>
                                </div>
                            </div>
                        </section>

                        <!-- Secções e Produtos -->
                        <section id="secoes">
                            <h2>Secções</h2>
                            
                            <?php for ($i=1; $i <= 20; $i++) { ?>
                                <div class="secao" style="position: relative; width: 100%;">
                                    <h3>Secção <?php echo $i; ?>: </h3>
                                    <input type="text" id="search-box-<?php echo $i; ?>" name="seccao_nome_<?php echo $i; ?>" placeholder="Nome da secção" oninput="performSearch(this,<?php echo $i; ?>)">
                                    <div id="secoesModal-<?php echo $i; ?>" class="modal">
                                        <div id="results-container-<?php echo $i; ?>" class="results-container"></div>
                                    </div>
                                </div>
                            <?php } ?>
                            <script>
                                localStorage.removeItem('modalOpen');

                                const searchData = [];
                                
                                $.ajax({
                                    url: 'ajax.obterSecoes.php',
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(data) {
                                        searchData.push(...data);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Erro ao buscar os dados:', error);
                                    }
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

                                function performSearch(searchBox, num) {
                                    const modal = document.getElementById('secoesModal-'+num);
                                    const query = searchBox.value.toLowerCase();
                                    modal.innerHTML = "";

                                    if (query) {
                                        const filteredResults = searchData.filter(item =>
                                        item.toLowerCase().includes(query)
                                        );

                                        if (filteredResults.length > 0) {
                                            modal.style.display = 'block';
                                            filteredResults.forEach(result => {
                                                const resultDiv = document.createElement("div");
                                                resultDiv.classList.add("result-item");
                                                resultDiv.textContent = result;
                                                resultDiv.onclick = () => selectResult(result, searchBox, num);
                                                modal.appendChild(resultDiv);
                                            });
                                        } else {
                                            modal.style.display = "none";
                                        }
                                    } else {
                                        modal.style.display = "none";
                                    }
                                }

                                function selectResult(result, searchBox, num) {
                                    const modal = document.getElementById('secoesModal-'+num);
                                    searchBox.value = result;
                                    modal.style.display = "none";
                                }
                            </script>
                            <button id=botSaveBudget type="submit" class="botao-salvar-orcamento">Criar Orçamento</button>         
                        </section>
                    </div>
                </div>
            </main>
        <!-- <input type=text id=numSeccao name=numSeccao value=1> -->
        </form>
    </div>
</body>
</html>
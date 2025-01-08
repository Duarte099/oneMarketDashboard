<?php 
    $estouEm = 2;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/orcamentos.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Orçamentos</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <script>
        //PESQUISA MODAL CLIENTES
            const clientsSearchData = [];
                                    
            $.ajax({
                url: 'ajax.obterClientes.php',
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    clientsSearchData.push(...data);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao buscar os dados:', error);
                }
            }); 

            function clientsSearch(searchBox) {
                const modal = document.getElementById('budgetModal');
                const tbody = modal.querySelector('table tbody');
                const query = searchBox.value.toLowerCase();
                tbody.innerHTML = ""; // Limpa os resultados anteriores

                const displayResults = (results) => {
                    if (results.length > 0) {
                        results.forEach((result) => {
                            const row = document.createElement("tr");

                            // Adiciona as colunas da tabela
                            const nomeCliente = document.createElement("td");
                            nomeCliente.textContent = result.nomeCliente;

                            const emailCliente = document.createElement("td");
                            emailCliente.textContent = result.emailCliente;

                            const contactoCliente = document.createElement("td");
                            contactoCliente.textContent = result.contactoCliente;

                            // Adiciona as células à linha
                            row.appendChild(nomeCliente);
                            row.appendChild(emailCliente);
                            row.appendChild(contactoCliente);

                            row.addEventListener("click", () => handleRowClick(result.idCliente, "budget"));
                            row.addEventListener("click", () => searchBox.value = "");

                            // Adiciona a linha ao corpo da tabela
                            tbody.appendChild(row);
                        });
                    } else {
                        // Adiciona uma linha dizendo "Sem resultados"
                        const row = document.createElement("tr");
                        const noResultsCell = document.createElement("td");
                        noResultsCell.textContent = "Sem resultados";
                        noResultsCell.colSpan = 3; // Define a célula para ocupar todas as colunas
                        noResultsCell.style.textAlign = "center"; // Centraliza o texto

                        row.appendChild(noResultsCell);
                        tbody.appendChild(row);
                    }
                };

                if (query) {
                    // Filtra os resultados com base na pesquisa
                    const filteredResults = clientsSearchData.filter(item =>
                        item.nomeCliente.toLowerCase().includes(query) ||
                        item.emailCliente.toLowerCase().includes(query) ||
                        item.contactoCliente.toLowerCase().includes(query)
                    );

                    displayResults(filteredResults);
                } else {
                    // Exibe todos os resultados se o campo de busca estiver vazio
                    displayResults(clientsSearchData);
                }
            }
        
        //PESQUISA ORÇAMENTOS
        const budgetsSearchData = [];
                                    
        $.ajax({
            url: 'ajax.obterOrcamentos.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                budgetsSearchData.push(...data);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao buscar os dados:', error);
            }
        }); 

        function budgetsSearch(searchBox) {
            const data = document.getElementById('bottom-data');
            const tbody = data.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = ""; // Limpa os resultados anteriores

            const displayResults = (results) => {
                if (results.length > 0) {
                    results.forEach((result) => {
                        const row = document.createElement("tr");

                        // Adiciona as colunas da tabela
                        const numBudget = document.createElement("td");
                        numBudget.textContent = result.numBudget;

                        const nomeCliente = document.createElement("td");
                        nomeCliente.textContent = result.nomeCliente;

                        const contactoCliente = document.createElement("td");
                        contactoCliente.textContent = result.contactoCliente;

                        const numWorksheet = document.createElement("td");
                        numWorksheet.textContent = result.numWorksheet;

                        const dataCriacao = document.createElement("td");
                        dataCriacao.textContent = result.dataCriacao;

                        const responsavel = document.createElement("td");
                        responsavel.textContent = result.responsavel;

                        // Adiciona o botão de exclusão
                        const actions = document.createElement("td");
                        const deleteButton = document.createElement("button");
                        deleteButton.className = 'btn-small';
                        deleteButton.id = 'botDeleteBudget';
                        deleteButton.innerHTML = '🗑️';
                        deleteButton.onclick = (event) => {
                            deleteBudget(result.numBudget, result.idbudget);
                            event.stopPropagation();
                        };
                        actions.appendChild(deleteButton);

                        // Adiciona todas as células à linha
                        row.appendChild(numBudget);
                        row.appendChild(nomeCliente);
                        row.appendChild(contactoCliente);
                        row.appendChild(numWorksheet);
                        row.appendChild(dataCriacao);
                        row.appendChild(responsavel);
                        row.appendChild(actions);

                        row.addEventListener("click", () => handleRowClick(result.idBudget, "editBudget"));
                        row.addEventListener("click", () => searchBox.value = "");

                        // Adiciona a linha ao corpo da tabela
                        tbody.appendChild(row);
                    });
                } else {
                    // Adiciona uma linha dizendo "Sem resultados"
                    const row = document.createElement("tr");
                    const noResultsCell = document.createElement("td");
                    noResultsCell.textContent = "Sem resultados";
                    noResultsCell.colSpan = 7; // Atualiza para incluir todas as colunas (inclusive a de ações)
                    noResultsCell.style.textAlign = "center"; // Centraliza o texto

                    row.appendChild(noResultsCell);
                    tbody.appendChild(row);
                }
            };

            if (query) {
                // Filtra os resultados com base nos campos numBudget, nomeCliente e responsavel
                const filteredResults = budgetsSearchData.filter(item =>
                    item.numBudget.toLowerCase().includes(query) ||
                    item.nomeCliente.toLowerCase().includes(query) ||
                    item.responsavel.toLowerCase().includes(query)
                );

                displayResults(filteredResults);
            } else {
                // Exibe todos os resultados se o campo de busca estiver vazio
                displayResults(budgetsSearchData);
            }
        }
    </script>

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
                <h1>Orçamentos</h1>
                <!-- Barra de pesquisa -->
                <div class="search-bar">
                    <input type="text" id="searchBox" placeholder="Pesquisar orçamentos..." oninput="budgetsSearch(this)" />
                </div>
            </div>
            <a href="novoOrcamento.php" id="new-budget" class="report">
                <i class='bx bx-plus'></i>
                <span>Novo Orçamento</span>
            </a>
        </div>
            <div id="budgetModal" class="modal">
                <div class="modal-content">
                    <div class="headerModal">
                        <h2>Selecione um Cliente</h2>
                        <span class="close">&times;</span>
                    </div>
                        <div class="form-input">
                            <input id="budget-search-input" name="budget-search-input" type="text" placeholder="Pesquisar clientes..." oninput="clientsSearch(this)">
                        </div>
                    <div class="tabela">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Contacto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT
                                                client.id as idCliente,
                                                client.name as nameCliente,
                                                client.email as emailCliente,
                                                client.contact as contactCliente
                                            FROM client
                                            ORDER BY idCliente DESC LIMIT 10;";
                                    $result = $con->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr onclick=\"handleRowClick('{$row['idCliente']}', 'budget')\" style=\"cursor: pointer;\">
                                                <td>{$row['nameCliente']}</td>
                                                <td>{$row['emailCliente']}</td>
                                                <td>{$row['contactCliente']}</td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>Sem registros para exibir.</td></tr>";
                                    }
                                ?>  
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bottom-data" id="bottom-data">
                <div class="budget">
                    <table>
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Ficha Trabalho</th>
                                <th>Data Criação</th>
                                <th>Responsavel</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT 
                                            budget.id as idbudget,
                                            budget.num as numBudget,
                                            budget.year as yearBudget,
                                            client.name as nomeCliente, 
                                            client.contact as contactoCliente, 
                                            worksheet.num as numWorksheet, 
                                            worksheet.year as yearWorksheet, 
                                            budget.created as dataCriacao,
                                            administrator.name as responsavel
                                        FROM budget
                                        LEFT JOIN 
                                            client ON budget.idClient = client.id
                                        LEFT JOIN 
                                            administrator ON budget.createdBy = administrator.id
                                        LEFT JOIN 
                                            worksheet ON budget.idWorksheet = worksheet.id
                                        ORDER BY budget.id DESC ;";
                                $result = $con->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        if (isset($row['numWorksheet'])) {
                                            $numWorksheet = $row['numWorksheet'] . "/" . $row['yearWorksheet'];
                                        }
                                        else{
                                            $numWorksheet = '';
                                        }
                                        echo "<tr onclick=\"handleRowClick('{$row['idbudget']}', 'editBudget')\" style=\"cursor: pointer; position: relative;\">
                                            <td>" . $row['numBudget'] . "/" . $row['yearBudget'] . "</td>
                                            <td>{$row['nomeCliente']}</td>
                                            <td>{$row['contactoCliente']}</td>
                                            <td>{$numWorksheet}</td>
                                            <td>{$row['dataCriacao']}</td>
                                            <td>{$row['responsavel']}</td>
                                            <td><button class='btn-small' id='botDeleteBudget' onclick=\"deleteBudget('{$row['numBudget']}/{$row['yearBudget']}', {$row['idbudget']}); event.stopPropagation();\">🗑️</button></td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>Sem registros para exibir.</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <script>
            function deleteBudget(num, id) {
                const result = confirm("Tem a certeza que deseja eliminar o orçamento " + num + "?");
                if (result) {
                    fetch(`./deleteBudget.php?idBudget=${encodeURIComponent(id)}`, {
                        method: 'GET',
                    })
                    .then(() => {
                        console.log("ID enviado com sucesso via GET.");
                    })
                    .catch(error => {
                        console.error("Erro ao enviar ID:", error);
                    });
                }
                window.location.href = window.location.pathname; // Recarrega a página
            }

            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('budget-search-input');
                const modal = document.getElementById('budgetModal');
                const newBudgetButton = document.getElementById('new-budget'); // Seleciona o botão

                // Função para abrir o modal
                window.openModal = function () {
                    modal.style.display = 'block';
                };

                // Função para fechar o modal
                function closeModal() {
                    modal.style.display = 'none';
                    window.location.href = window.location.pathname; // Recarrega a página
                }

                // Limpar pesquisa modal
                window.limparPesquisa = function() {
                    if (searchInput) {
                        searchInput.value = ''; // Limpa o valor do input
                        clientsSearch(searchInput);
                    }
                };

                // Fechar modal ao clicar no "x"
                const closeButton = document.querySelector('.close');
                if (closeButton) {
                    closeButton.addEventListener('click', closeModal);
                }

                // Adiciona evento de clique ao botão "Nova Ficha de Trabalho"
                if (newBudgetButton) {
                    newBudgetButton.addEventListener('click', function (event) {
                        event.preventDefault(); // Evita o comportamento padrão do link
                        openModal(); // Abre o modal
                    });
                }
            });
        </script>
    </div>
</body>
</html>
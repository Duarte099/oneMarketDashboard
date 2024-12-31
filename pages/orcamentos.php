<?php 
    include('../db/conexao.php'); 
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
        const searchData = [];
                                
        $.ajax({
            url: 'ajax.obterClientes.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                searchData.push(...data);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao buscar os dados:', error);
            }
        });

        console.log(searchData);

        function performSearch(searchBox) {
            const modal = document.getElementById('budgetModal');
            const tbody = modal.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = ""; // Limpa os resultados anteriores

            if (query) {
                // Filtra os resultados com base na pesquisa
                const filteredResults = searchData.filter(item =>
                    item.nomeCliente.toLowerCase().includes(query) ||
                    item.emailCliente.toLowerCase().includes(query) ||
                    item.contactoCliente.toLowerCase().includes(query) // Busca diretamente na string "numBudget"
                );

                if (filteredResults.length > 0) {
                    filteredResults.forEach((result) => {
                        const row = document.createElement("tr");

                        // Adiciona as colunas da tabela
                        const nomeCliente = document.createElement("td");
                        nomeCliente.textContent = result.nomeCliente; // Exibe "numBudget" diretamente

                        const emailCliente = document.createElement("td");
                        emailCliente.textContent = result.emailCliente;

                        const contactoCliente = document.createElement("td");
                        contactoCliente.textContent = result.contactoCliente;

                        // Configura o evento de clique para selecionar o cliente
                        row.onclick = () => selectResult(result.nomeCliente, searchBox);

                        // Adiciona as células à linha
                        row.appendChild(nomeCliente);
                        row.appendChild(emailCliente);
                        row.appendChild(contactoCliente);

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
            } else {
                // Adiciona uma linha dizendo "Sem resultados" caso o campo de busca esteja vazio
                const row = document.createElement("tr");
                const noResultsCell = document.createElement("td");
                noResultsCell.textContent = "Sem resultados";
                noResultsCell.colSpan = 3; // Define a célula para ocupar todas as colunas
                noResultsCell.style.textAlign = "center"; // Centraliza o texto

                row.appendChild(noResultsCell);
                tbody.appendChild(row);
            }
        }

        function selectResult(nomeCliente, searchBox) {
            const modal = document.getElementById('budgetModal');
            searchBox.value = nomeCliente; // Atualiza o valor do campo de entrada com o nome do cliente
            modal.style.display = "none"; // Fecha o modal
        }

        function limparPesquisa() {
            const searchBox = document.getElementById('search-input');
            const modal = document.getElementById('budgetModal');
            const tbody = modal.querySelector('table tbody');
            
            searchBox.value = ""; // Limpa o campo de pesquisa
            tbody.innerHTML = ""; // Limpa os resultados da tabela
            modal.style.display = "none"; // Fecha o modal
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
                            <input id="budget-search-input" name="budget-search-input" type="text" placeholder="Search..." oninput="performSearch(this)">
                            <button type="button" class="clear-button" onclick="limparPesquisa()">✖</button>
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

            <div class="bottom-data">
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
                                        echo "<tr onclick=\"handleRowClick('{$row['idbudget']}', 'editBudget')\" style=\"cursor: pointer; position: relative;\">
                                            <td>" . $row['numBudget'] . "/" . $row['yearBudget'] . "</td>
                                            <td>{$row['nomeCliente']}</td>
                                            <td>{$row['contactoCliente']}</td>
                                            <td>" . $row['numWorksheet'] . "/" . $row['yearWorksheet'] . "</td>
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
                console.log("ID do orçamento a ser excluído:", id);
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
                        performSearch(searchInput);
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
<?php 

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php');
    $estouEm = 3;

    if (adminPermissions("adm_002", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/fichasTrabalho.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Fichas de Trabalho</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <script>
        //PESQUISA FICHAS DE TRABALHO
        const worksheetsSearchData = [];
                                
        $.ajax({
            url: 'json.obterWorksheets.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                worksheetsSearchData.push(...data);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao buscar os dados:', error);
            }
        }); 

        function worksheetsSearch(searchBox) {
            const dataWorksheet = document.getElementById('bottom-data');
            const tbody = dataWorksheet.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = ""; // Limpa os resultados anteriores

            const displayResults = (results) => {
                if (results.length > 0) {
                    results.forEach((result) => {
                        const row = document.createElement("tr");

                        // Adiciona as colunas da tabela
                        const numWorksheet = document.createElement("td");
                        numWorksheet.textContent = result.numWorksheet;

                        const nomeCliente = document.createElement("td");
                        nomeCliente.textContent = result.nomeCliente;

                        const contactoCliente = document.createElement("td");
                        contactoCliente.textContent = result.contactoCliente;

                        const numBudget = document.createElement("td");
                        numBudget.textContent = result.numBudget;

                        const readyStorage = document.createElement("td");
                        readyStorage.textContent = result.readyStorage;

                        const joinWork = document.createElement("td");
                        joinWork.textContent = result.joinWork;

                        const exitWork = document.createElement("td");
                        exitWork.textContent = result.exitWork;

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
                        row.appendChild(numWorksheet);
                        row.appendChild(nomeCliente);
                        row.appendChild(contactoCliente);
                        row.appendChild(numBudget);
                        row.appendChild(readyStorage);
                        row.appendChild(joinWork);
                        row.appendChild(exitWork);
                        row.appendChild(responsavel);
                        row.appendChild(actions);

                        row.addEventListener("click", () => handleRowClick(result.idWorksheet, "editWorksheet"));
                        row.addEventListener("click", () => searchBox.value = "");

                        // Adiciona a linha ao corpo da tabela
                        tbody.appendChild(row);
                    });
                } else {
                    // Adiciona uma linha dizendo "Sem resultados"
                    const row = document.createElement("tr");
                    const noResultsCell = document.createElement("td");
                    noResultsCell.textContent = "Sem resultados";
                    noResultsCell.colSpan = 9; // Atualiza para incluir todas as colunas (inclusive a de ações)
                    noResultsCell.style.textAlign = "center"; // Centraliza o texto

                    row.appendChild(noResultsCell);
                    tbody.appendChild(row);
                }
            };

            if (query) {
                // Filtra os resultados com base nos campos numBudget, nomeCliente e responsavel
                const filteredResults = worksheetsSearchData.filter(item =>
                    item.numWorksheet.toLowerCase().includes(query) ||
                    item.nomeCliente.toLowerCase().includes(query) ||
                    item.responsavel.toLowerCase().includes(query)
                );

                displayResults(filteredResults);
            } else {
                // Exibe todos os resultados se o campo de busca estiver vazio
                displayResults(worksheetsSearchData);
            }
        }
    
        //PESQUISA ORÇAMENTOS
        const budgetsSearchData = [];
                                    
        $.ajax({
            url: 'json.obterOrcamentosModal.php',
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
            const modal = document.getElementById('worksheetModal');
            const tbody = modal.querySelector('table tbody');
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

                        // Adiciona todas as células à linha
                        row.appendChild(numBudget);
                        row.appendChild(nomeCliente);
                        row.appendChild(responsavel);

                        row.addEventListener("click", () => handleRowClick(result.idBudget, "worksheet"));
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
                    <h1>Fichas de Trabalho</h1>
                    <div class="search-bar">
                        <input type="text" id="searchBox" placeholder="Pesquisar fichas de trabalho..." oninput="worksheetsSearch(this)" />
                    </div>
                </div>
                <?php if (adminPermissions("adm_002", "inserir") == 1) { ?>
                    <a href="novaFichaTrabalho.php" id="new-worksheet" class="report">
                        <i class='bx bx-plus'></i>
                        <span>Nova Ficha de Trabalho</span>
                    </a>
                <?php } ?>
            </div>

            <div id="worksheetModal" class="modal">
                <div class="worksheet-modal-content">
                    <div class="headerModal">
                        <h2>Selecione um Orçamento</h2>
                        <span class="close">&times;</span>
                    </div>
                    <form method="GET" action="">
                        <div class="form-input">
                            <input id="search-input" name="search-input" type="text" placeholder="Search..." oninput="budgetsSearch(this)">
                        </div>
                    </form>
                    <div class="tabela">
                        <table>
                            <thead>
                                <tr>
                                    <th>Numero</th>
                                    <th>Cliente</th>
                                    <th>Responsavel</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "SELECT 
                                                budget.id as idBudget,
                                                budget.num as numBudget,
                                                budget.year as yearBudget,
                                                budget.name as nomeBudget,
                                                client.name as nomeCliente, 
                                                budget.created as dataCriacao,
                                                administrator.name as responsavel
                                            FROM budget 
                                            LEFT JOIN administrator ON budget.createdBy = administrator.id
                                            LEFT JOIN client ON budget.idClient = client.id
                                            WHERE idWorksheet IS NULL 
                                            ORDER BY budget.id DESC 
                                            LIMIT 10;";

                                    $result = $con->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr onclick=\"handleRowClick('{$row['idBudget']}', 'worksheet')\" style=\"cursor: pointer;\">
                                                <td>" . $row['numBudget'] . "/" . $row['yearBudget'] . "</td>
                                                <td>{$row['nomeBudget']}</td>
                                                <td>{$row['nomeCliente']}</td>
                                            </tr>";
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bottom-data" id="bottom-data">
                <div class="worksheet">
                    <table>
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Cliente</th>
                                <th>Contato</th>
                                <th>Nº Orçamento</th>
                                <th>Pronto em Armazém</th>
                                <th>Entrada em Obra</th>
                                <th>Saída de Obra</th>
                                <th>Elaborado Por</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT 
                                            client.name as nomeCliente, 
                                            client.contact as contactoCliente, 
                                            budget.id as idBudget,
                                            budget.num as numBudget,
                                            budget.year as yearBudget, 
                                            worksheet.id as idWorksheet,
                                            worksheet.num as numWorksheet,
                                            worksheet.year as yearWorksheet, 
                                            worksheet.readyStorage, 
                                            worksheet.joinWork, 
                                            worksheet.exitWork, 
                                            administrator.name as nomeAdministrador
                                        FROM worksheet
                                        LEFT JOIN 
                                            client ON worksheet.idClient = client.id
                                        LEFT JOIN 
                                            administrator ON worksheet.createdBy = administrator.id
                                        LEFT JOIN 
                                            budget ON worksheet.idBudget = budget.id
                                        ORDER BY idWorksheet DESC;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr onclick=\"handleRowClick('{$row['idWorksheet']}', 'editWorksheet')\" style=\"cursor: pointer;\">
                                            <td>" . $row['numWorksheet'] . "/" . $row['yearWorksheet'] . "</td>
                                            <td>{$row['nomeCliente']}</td>
                                            <td>{$row['contactoCliente']}</td>
                                            <td>" . $row['numBudget'] . "/" . $row['yearBudget'] . "</td>
                                            <td>{$row['readyStorage']}</td>
                                            <td>{$row['joinWork']}</td>
                                            <td>{$row['exitWork']}</td>
                                            <td>{$row['nomeAdministrador']}</td>
                                            <td>" . (adminPermissions("adm_002", "delete") == '1' ? "<button class='btn-small' id='botDeleteWorksheet' onclick=\"deleteWorksheet('{$row['numWorksheet']}/{$row['yearWorksheet']}', {$row['idWorksheet']}); event.stopPropagation();\">🗑️</button>" : " ") .  "</td>
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

        <script src="../index.js" defer></script>
        <script>
            function deleteWorksheet(num, id) {
                const result = confirm("Tem a certeza que deseja eliminar a ficha de trabalho " + num + "?");
                if (result) {
                    fetch(`./fichaTrabalhoDelete.php?idWorksheet=${encodeURIComponent(id)}`, {
                        method: 'GET',
                    })
                    .then(() => {
                        console.log("ID enviado com sucesso via GET.");
                    })
                    .catch(error => {
                        console.error("Erro ao enviar ID:", error);
                    });
                }
                window.location.href = window.location.pathname;
            }

            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('search-input');
                const modal = document.getElementById('worksheetModal');
                const newBudgetButton = document.getElementById('new-worksheet'); // Seleciona o botão

                // Função para abrir o modal
                window.openModal = function () {
                    modal.style.display = 'block';
                };

                // Função para fechar o modal
                function closeModal() {
                    modal.style.display = 'none';
                    searchInput.value = '';
                    budgetsSearch(searchInput);
                }

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
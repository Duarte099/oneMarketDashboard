<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 3;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_002", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }
?>
    <link rel="stylesheet" href="./css/fichasTrabalho.css">
    <title>OneMarket | Fichas de Trabalho</title>
</head>

<body>
    <script>
        //Função para pesquisar fichas de trabalho

        //Variavel para guardar todas as fichas de trabalho
        const worksheetsSearchData = [];
                                
        //Obtem as fichas de trabalho via json no arquivo json.obterWorksheets.php
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

        //Função para fazer a pesquisa, apenas mostra os resultados que forem parecidos com a pesquisa
        function worksheetsSearch(searchBox) {
            const dataWorksheet = document.getElementById('bottom-data');
            const tbody = dataWorksheet.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = ""; // Limpa os resultados anteriores

            const displayResults = (results) => {
                if (results.length > 0) {
                    results.forEach((result) => {
                        const row = document.createElement("tr");
                        row.style.cursor = "pointer";

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
    
        //Função para pesquisar os orçamentos para o MODAL

        //Variavel para guardar todas os orçamentos
        const budgetsSearchData = [];
                              
        //Obtem os orçamentos via json no arquivo json.obterOrcamentosModal.php
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

        //Função para fazer a pesquisa, apenas mostra os resultados que forem parecidos com a pesquisa
        function budgetsSearch(searchBox) {
            const modal = document.getElementById('worksheetModal');
            const tbody = modal.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = ""; // Limpa os resultados anteriores

            const displayResults = (results) => {
                if (results.length > 0) {
                    results.forEach((result) => {
                        const row = document.createElement("tr");
                        row.style.cursor = "pointer";

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
                    <h1>Fichas de Trabalho</h1>
                    <div class="search-bar">
                        <input type="text" id="searchBox" placeholder="Pesquisar fichas de trabalho..." oninput="worksheetsSearch(this)" />
                    </div>
                </div>
                <?php 
                //Se o administrador tiver permissões para criar ficahs de trabalho então mostra o botão para tal
                if (adminPermissions($con, "adm_002", "inserir") == 1) { 
                    ?>
                    <a href="" id="new-worksheet" class="report">
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
                        <div class="form-input">
                            <input id="search-input" name="search-input" type="text" placeholder="Search..." oninput="budgetsSearch(this)">
                        </div>
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
                                    //query sql para selecionar todos os orçamentos que não tem uma ficha de trabalho associada
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
                                <th style="width: 90px;">Numero</th>
                                <th>Cliente</th>
                                <th>Contato</th>
                                <th style="width: 100px;">Orçamento</th>
                                <th>Pronto em Armazém</th>
                                <th>Entrada em Obra</th>
                                <th>Saída de Obra</th>
                                <th>Elaborado Por</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                //query sql para selecionar todas as fichas de trabalho
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
                                        //mostra os resultados, caso o administrador tenha permissões para apagar mostra também, o botão para tal
                                        echo "<tr onclick=\"handleRowClick('{$row['idWorksheet']}', 'editWorksheet')\" style=\"cursor: pointer;\">
                                            <td style=\"width: 90px;\">" . $row['numWorksheet'] . "/" . $row['yearWorksheet'] . "</td>
                                            <td>{$row['nomeCliente']}</td>
                                            <td>{$row['contactoCliente']}</td>
                                            <td style=\"width: 90px;\">" . $row['numBudget'] . "/" . $row['yearBudget'] . "</td>
                                            <td>{$row['readyStorage']}</td>
                                            <td>{$row['joinWork']}</td>
                                            <td>{$row['exitWork']}</td>
                                            <td>{$row['nomeAdministrador']}</td>
                                            <td>" . (adminPermissions($con, "adm_002", "delete") == '1' ? "<button class='btn-small' id='botDeleteWorksheet' onclick=\"deleteWorksheet('{$row['numWorksheet']}/{$row['yearWorksheet']}', {$row['idWorksheet']}); event.stopPropagation();\">🗑️</button>" : " ") .  "</td>
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
            //Função para deletar uma ficha de trabalho
            function deleteWorksheet(num, id) {
                //Faz uma pergunta e guarda o resultado em result
                const result = confirm("Tem a certeza que deseja eliminar a ficha de trabalho " + num + "?");
                //Se tiver respondido que sim
                if (result) {
                    //redireciona para a pagina fichaTrabalhoDelete e manda o id da ficha a ser deletada por GET
                    window.location.href = "fichaTrabalhoDelete?idWorksheet=" + id;
                }
            }

            // Evento para o modal
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('search-input');
                const modal = document.getElementById('worksheetModal');
                const newWorksheetButton = document.getElementById('new-worksheet'); // Seleciona o botão

                // Verificar se o botão está presente no DOM
                if (newWorksheetButton) {
                    console.log('Button found');
                    
                    // Verificar se outro evento está impedindo o funcionamento do botão
                    newWorksheetButton.addEventListener('click', function (event) {
                        event.preventDefault(); // Evita o comportamento padrão do link
                        openModal(); // Abre o modal
                    });
                } else {
                    console.log('Button not found');
                }

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
                } else {
                    console.log('Close button not found');
                }
            });
        </script>
    </div>
</body>

</html>
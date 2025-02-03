<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 2;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_001", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }
?>
    <link rel="stylesheet" href="./css/orcamentos.css">
    <title>OneMarket | Orçamentos</title>
</head>

<body>
    <script>
        //Função para pesquisar clientes do modal

            //Variavel para guardar todos os clientes
            const clientsSearchData = [];
                    
            //Pega os administradores todos via json no arquivo json.obterClientes.php
            $.ajax({
                url: 'json.obterClientes.php',
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    clientsSearchData.push(...data);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao buscar os dados:', error);
                }
            });

            //Função para fazer a pesquisa, apenas mostra os resultados que forem parecidos com a pesquisa
            function clientsSearch(searchBox) {
                const modal = document.getElementById('budgetModal');
                const tbody = modal.querySelector('table tbody');
                const query = searchBox.value.toLowerCase();
                tbody.innerHTML = ""; // Limpa os resultados anteriores

                const displayResults = (results) => {
                    if (results.length > 0) {
                        results.forEach((result) => {
                            const row = document.createElement("tr");
                            row.style.cursor = "pointer";

                            // Adiciona as colunas da tabela
                            const nomeCliente = document.createElement("td");
                            nomeCliente.textContent = result.nome;

                            const emailCliente = document.createElement("td");
                            emailCliente.textContent = result.email;

                            const contactoCliente = document.createElement("td");
                            contactoCliente.textContent = result.contacto;

                            // Adiciona todas as células à linha
                            row.appendChild(nomeCliente);
                            row.appendChild(emailCliente);
                            row.appendChild(contactoCliente);

                            row.addEventListener("click", () => handleRowClick(result.id, "budget"));
                            row.addEventListener("click", () => searchBox.value = "");

                            // Adiciona a linha ao corpo da tabela
                            tbody.appendChild(row);
                        });
                    } else {
                        // Adiciona uma linha dizendo "Sem resultados"
                        const row = document.createElement("tr");
                        const noResultsCell = document.createElement("td");
                        noResultsCell.textContent = "Sem resultados";
                        noResultsCell.colSpan = 3; // Atualiza para incluir todas as colunas (inclusive a de ações)
                        noResultsCell.style.textAlign = "center"; // Centraliza o texto

                        row.appendChild(noResultsCell);
                        tbody.appendChild(row);
                    }
                };

                if (query) {
                    // Filtra os resultados com base nos campos numBudget, nomeCliente e responsavel
                    const filteredResults = clientsSearchData.filter(item =>
                        item.nome.toLowerCase().includes(query) ||
                        item.email.toLowerCase().includes(query) ||
                        item.contacto.toLowerCase().includes(query)
                    );

                    displayResults(filteredResults);
                } else {
                    // Exibe todos os resultados se o campo de busca estiver vazio
                    displayResults(clientsSearchData);
                }
            }
        

        //Função para pesquisar orçamentos

            //Variavel para guardar todos os orçamentos
            const budgetsSearchData = [];
                     
            //Pega os orçamentos todos via json no arquivo json.obterOrcamentos.php
            $.ajax({
                url: 'json.obterOrcamentos.php',
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
                const data = document.getElementById('bottom-data');
                const tbody = data.querySelector('table tbody');
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
                <h1>Orçamentos</h1>
                <!-- Barra de pesquisa -->
                <div class="search-bar">
                    <input type="text" id="searchBox" placeholder="Pesquisar orçamentos..." oninput="budgetsSearch(this)" />
                </div>
            </div>
            <?php if (adminPermissions($con, "adm_001", "inserir") == 1) { ?>
                <a href="novoOrcamento.php" id="new-budget" class="report">
                    <i class='bx bx-plus'></i>
                    <span>Novo Orçamento</span>
                </a>
            <?php } ?>
        </div>
            <div id="budgetModal" class="modal">
                <div class="budget-modal-content">
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
                                <th>Ação</th>
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
                                            <td>" . (adminPermissions($con, "adm_001", "delete") == '1' ? "<button class='btn-small' id='botDeleteBudget' onclick=\"deleteBudget('{$row['numBudget']}/{$row['yearBudget']}', {$row['idbudget']}); event.stopPropagation();\">🗑️</button>" : " ") .  "</td>
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
            //Função para deletar um orçamento
            function deleteBudget(num, id) {
                //Faz uma pergunta e guarda o resultado em result
                const result = confirm("Tem a certeza que deseja eliminar o orçamento " + num + "?");
                //Se tiver respondido que sim
                if (result) {
                    //redireciona para a pagina orcamentoDelete e manda o id do orçamento a ser deletada por GET
                    window.location.href = "orcamentoDelete?idBudget=" + id;
                }
            }

            //Evento para o modal
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
                    searchInput.value = '';
                    clientsSearch(searchInput);
                }

                // Fechar modal ao clicar no "x"
                const closeButton = document.querySelector('.close');
                if (closeButton) {
                    closeButton.addEventListener('click', closeModal);
                }

                // Adiciona evento de clique ao botão "Novo Orçamento"
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
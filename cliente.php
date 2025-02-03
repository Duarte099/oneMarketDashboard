<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 5;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_004", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }
?>
    <link rel="stylesheet" href="./css/orcamentos.css">
    <title>OneMarket | Clientes</title>
</head>

<body>
    <script>
        //Função para pesquisar clientes

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
            const tbody = document.querySelector('#bottom-data table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = "";

            const filteredResults = clientsSearchData.filter(item =>
                item.nome.toLowerCase().includes(query) ||
                item.email.toLowerCase().includes(query) ||
                item.contacto.toLowerCase().includes(query) ||
                item.nif.toLowerCase().includes(query)
            );

            if (filteredResults.length > 0) {
                filteredResults.forEach(result => {
                    const row = document.createElement("tr");
                    row.className = result.status == 1 ? "ativo" : "inativo";
                    row.setAttribute("onclick", `handleRowClick('${result.id}', 'editClient')`);

                    row.innerHTML = `
                        <td>${result.nome}</td>
                        <td>${result.email}</td>
                        <td>${result.contacto}</td>
                        <td>${result.nif}</td>
                        <td>${result.status == 1 ? "Ativo" : "Inativo"}</td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center;">Sem resultados</td></tr>`;
            }
        }
    </script>

    <?php 
        //inclui a sideBar na página
        include('sideBar.php'); 
    ?>

    <div class="content">
        <?php 
            //Inclui o header na página
            include('header.php'); 
        ?>

        <main>
            <div class="header">
                <div class="left">
                    <h1>Clientes</h1>
                    <div class="search-bar">
                        <input type="text" id="searchBox" placeholder="Pesquisar clientes..." oninput="clientsSearch(this)" />
                    </div>
                </div>
                <?php //Se o administrador tiver permissões para criar novos clientes então mostra o botão
                if (adminPermissions($con, "adm_005", "inserir") == 1) { 
                    ?>
                    <a href="clienteCriar.php" id="new-budget" class="report">
                        <i class='bx bx-plus'></i>
                        <span>Novo Cliente</span>
                    </a>
                <?php } ?>
            </div>

            <div class="bottom-data" id="bottom-data">
                <div class="budget">
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Contacto</th>
                                <th>NIF</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                //query sql para selecionar todos os clientes 
                                $sql = "SELECT id, name, email, contact, nif, active FROM client ORDER BY id DESC;";
                                $result = $con->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status = $row['active'] == 1 ? 'Ativo' : 'Inativo';
                                        $class = $row['active'] == 1 ? 'ativo' : 'inativo';
                                        //Mostra os resultados(produtos)
                                        echo "<tr class='$class' onclick=\"handleRowClick('{$row['id']}', 'editClient')\" style=\"cursor: pointer;\">
                                            <td>{$row['name']}</td>
                                            <td>{$row['email']}</td>
                                            <td>{$row['contact']}</td>
                                            <td>{$row['nif']}</td>
                                            <td>{$status}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align:center;'>Sem registros para exibir.</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>

</html>

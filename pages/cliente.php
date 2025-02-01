<?php 
    include('../pages/head.php'); 
    $estouEm = 5;

    if (adminPermissions($con, "adm_004", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }
?>
    <link rel="stylesheet" href="../css/client.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Clientes</title>
</head>

<body>
    <script>
        // Pesquisa de clientes usando AJAX
        const clientsSearchData = [];

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
        include('../pages/sideBar.php'); 
    ?>

    <div class="content">
        <?php 
            include('../pages/header.php'); 
        ?>

        <main>
            <div class="header">
                <div class="left">
                    <h1>Clientes</h1>
                    <div class="search-bar">
                        <input type="text" id="searchBox" placeholder="Pesquisar clientes..." oninput="clientsSearch(this)" />
                    </div>
                </div>
                <?php if (adminPermissions($con, "adm_005", "inserir") == 1) { ?>
                    <a href="../pages/clienteCriar.php" id="new-budget" class="report">
                        <i class='bx bx-plus'></i>
                        <span>Novo Cliente</span>
                    </a>
                <?php } ?>
            </div>

            <div class="bottom-data" id="bottom-data">
                <div class="client">
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
                            $sql = "SELECT id, name, email, contact, nif, active FROM client;";
                            $result = $con->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $status = $row['active'] == 1 ? 'Ativo' : 'Inativo';
                                    $class = $row['active'] == 1 ? 'ativo' : 'inativo';
                                    echo "<tr class='$class' onclick=\"handleRowClick('{$row['id']}', 'editClient')\">
                                        <td data-label='Nome'>{$row['name']}</td>
                                        <td data-label='Email'>{$row['email']}</td>
                                        <td data-label='Contacto'>{$row['contact']}</td>
                                        <td data-label='NIF'>{$row['nif']}</td>
                                        <td data-label='Status'>{$status}</td>
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

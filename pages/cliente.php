<?php
    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php'); 
    
    $estouEm = 5;

    if (adminPermissions("adm_004", "view") == 0) {
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
    <link rel="stylesheet" href="../css/client.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Fichas de Trabalho</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <script>
        //PESQUISA CLIENTES
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
            const dataclient = document.getElementById('bottom-data');
            const tbody = dataclient.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = "";

            const displayResults = (results) => {
                if (results.length > 0) {
                    results.forEach((result) => {
                        if (result.status == 1) {
                            result.status = "Ativo";
                        } else {
                            result.status = "Inativo";
                        }
                        const row = document.createElement("tr");

                        // Colunas adicionais
                        const nome = document.createElement("td");
                        nome.textContent = result.nome;

                        const email = document.createElement("td");
                        email.textContent = result.email;

                        const contacto = document.createElement("td");
                        contacto.textContent = result.contacto;

                        const nif = document.createElement("td");
                        nif.textContent = result.nif;

                        const status = document.createElement("td");
                        status.textContent = result.status;

                        // Adiciona todas as células à linha
                        row.appendChild(nome);
                        row.appendChild(email);
                        row.appendChild(contacto);
                        row.appendChild(nif);
                        row.appendChild(status);

                        // Adiciona a linha ao corpo da tabela
                        tbody.appendChild(row);
                    });
                } else {
                    // Adiciona uma linha dizendo "Sem resultados"
                    const row = document.createElement("tr");
                    const noResultsCell = document.createElement("td");
                    noResultsCell.textContent = "Sem resultados";
                    noResultsCell.colSpan = 9; // Atualiza para incluir todas as colunas
                    noResultsCell.style.textAlign = "center";

                    row.appendChild(noResultsCell);
                    tbody.appendChild(row);
                }
            };

            if (query) {
                const filteredResults = clientsSearchData.filter(item =>
                    item.nome.toLowerCase().includes(query) ||
                    item.email.toLowerCase().includes(query) ||
                    item.contacto.toLowerCase().includes(query) ||
                    item.nif.toLowerCase().includes(query)
                );

                displayResults(filteredResults);
            } else {
                displayResults(clientsSearchData);
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
                    <h1>Clientes</h1>
                    <div class="search-bar">
                        <input type="text" id="searchBox" placeholder="Pesquisar clientes..." oninput="clientsSearch(this)" />
                    </div>
                </div>
                <?php if (adminPermissions("adm_004", "inserir") == 1) { ?>
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
                                $sql = "SELECT
                                            client.id as id,
                                            client.name, 
                                            client.email,
                                            client.contact,
                                            client.nif,
                                            client.active
                                        FROM client;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status = $row['active'] == 1 ? 'Ativo' : 'Inativo';
                                        echo "<tr onclick=\"handleRowClick('{$row['id']}', 'editClient')\" style=\"cursor: pointer; position: relative;\">
                                            <td>{$row['name']}</td>
                                            <td>{$row['email']}</td>
                                            <td>{$row['contact']}</td>
                                            <td>{$row['nif']}</td>
                                            <td>{$status}</td>
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

        <script src="../index.js"></script>
    </div>
</body>

</html>
<?php 
    session_start();

    $estouEm = 6;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php');

    if (adminPermissions("adm_005", "view") == 0 || adminPermissions("adm_006", "view") == 0) {
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
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Admin</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<script>
        //PESQUISA ADMINISTRADORES
        const adminsSearchData = [];
                                
        $.ajax({
            url: 'json.obterAdmins.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                adminsSearchData.push(...data);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao buscar os dados:', error);
            }
        }); 

        function adminsSearch(searchBox) {
            const dataadmin = document.getElementById('admins');
            const tbody = dataadmin.querySelector('table tbody');
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

                        // Coluna da Imagem
                        const imgCell = document.createElement("td");
                        const img = document.createElement("img");
                        img.src = result.img; // Link da imagem
                        img.style.width = "50px"; // Tamanho da imagem
                        img.style.height = "50px"; // Tamanho da imagem
                        imgCell.appendChild(img);
                        row.appendChild(imgCell);

                        // Colunas adicionais
                        const nome = document.createElement("td");
                        nome.textContent = result.nome;

                        const email = document.createElement("td");
                        email.textContent = result.email;

                        const user = document.createElement("td");
                        user.textContent = result.user;

                        const nascimento = document.createElement("td");
                        nascimento.textContent = result.nascimento;

                        const status = document.createElement("td");
                        status.textContent = result.status;

                        // Adiciona todas as células à linha
                        row.appendChild(nome);
                        row.appendChild(email);
                        row.appendChild(user);
                        row.appendChild(nascimento);
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
                const filteredResults = adminsSearchData.filter(item =>
                    item.nome.toLowerCase().includes(query) ||
                    item.email.toLowerCase().includes(query) ||
                    item.user.toLowerCase().includes(query)
                );

                displayResults(filteredResults);
            } else {
                displayResults(adminsSearchData);
            }
        }

        //PESQUISA LOGS
        const logsSearchData = [];
                                
        $.ajax({
            url: 'json.obterLogs.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                logsSearchData.push(...data);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao buscar os dados:', error);
            }
        }); 

        function logsSearch(searchBox) {
            const dataadmin = document.getElementById('logs');
            const tbody = dataadmin.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = "";

            const displayResults = (results) => {
                if (results.length > 0) {
                    results.forEach((result) => {
                        const row = document.createElement("tr");
                        // Colunas adicionais
                        const data = document.createElement("td");
                        data.textContent = result.data;

                        const log = document.createElement("td");
                        log.textContent = result.log;
                        // Adiciona todas as células à linha
                        row.appendChild(data);
                        row.appendChild(log);

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
                const filteredResults = logsSearchData.filter(item =>
                    item.data.toLowerCase().includes(query) ||
                    item.log.toLowerCase().includes(query)
                );

                displayResults(filteredResults);
            } else {
                displayResults(logsSearchData);
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
                    <h1>Administração</h1>
                </div>
            </div>

            <div class="container">
                <?php if (adminPermissions("adm_005", "view") == 1) { ?>
                    <!-- Tabela de Administradores -->
                    <div class="bottom-data">
                        <div class="admins" id="admins">
                            <div class="up">
                                <h2>Administradores</h2>
                                <?php if (adminPermissions("adm_005", "inserir") == 1) { ?>
                                    <a href="../pages/adminCriar.php" class="report">
                                        <i class='bx bx-plus'></i>
                                        <span>Novo Administrador</span>
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="search-bar" style="  margin-top: 2px;">
                                <input type="text" id="searchBox" placeholder="Pesquisar administradores..." oninput="adminsSearch(this)" />
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Img</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT 
                                                    administrator.id as id,
                                                    administrator.name,
                                                    administrator.email,
                                                    administrator.user,
                                                    administrator.active,
                                                    administrator.img as imagem
                                                FROM administrator ;";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $status = $row['active'] == 1 ? 'Ativo' : 'Inativo';
                                                echo "<tr onclick=\"handleRowClick('{$row['id']}', 'editAdmin')\" style=\"cursor: pointer;\">
                                                        <td>
                                                            <div id=\"profilePic\" style=\"width:100%; max-width:500px; background: url('{$row['imagem']}') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; border-radius: 250px;\">
                                                                <img src=\"../images/semfundo.png\" style=\"width:100%;padding-bottom: 13px;\">
                                                            </div>
                                                        </td>
                                                        <td>{$row['name']}</td>
                                                        <td>{$row['email']}</td>
                                                        <td>{$row['user']}</td>
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
                <?php } ?>

                <?php if (adminPermissions("adm_006", "view") == 1) { ?>
                    <!-- Tabela de Logs -->
                    <div class="bottom-data">
                        <div class="logs" id="logs">
                            <h2>Logs</h2>
                            <div class="search-bar">
                                <input type="text" id="searchLogs" placeholder="Pesquisar logs..." oninput="logsSearch(this)" />
                            </div>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 200px;">Data</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $sql = "SELECT dataLog, logFile FROM administrator_logs ORDER BY dataLog DESC LIMIT 10";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                    <td style=\"width: 200px;\">{$row['dataLog']}</td>
                                                    <td>{$row['logFile']}</td>
                                                    </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='2'>Sem logs recentes para exibir.</td></tr>";
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </main>

        <script src="../index.js"></script>
    </div>
</body>

</html>
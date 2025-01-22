<?php 
    session_start();

    $estouEm = 6;

    include('../db/conexao.php');

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    if (adminPermissions("adm_005", "view") == 0) {
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
            url: 'ajax.obterAdmins.php',
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
            const dataadmin = document.getElementById('bottom-data');
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
                    <h1>Administradores</h1>
                    <div class="search-bar">
                        <input type="text" id="searchBox" placeholder="Pesquisar administradores..." oninput="adminsSearch(this)" />
                    </div>
                </div>
                <?php if (adminPermissions("adm_005", "inserir") == 1) { ?>
                    <a href="../pages/adminCriar.php" class="report">
                        <i class='bx bx-plus'></i>
                        <span>Novo Administrador</span>
                    </a>
                <?php } ?>
            </div>

            <div class="bottom-data" id="bottom-data">
                <div class="admins">
                    <table>
                        <thead>
                            <tr>
                                <th>Img</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Data Nascimento</th>
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
                                            administrator.img as imagem,
                                            administrator.birthday
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
                                                <td>{$row['birthday']}</td>
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
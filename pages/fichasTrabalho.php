<?php 

    session_start();
    
    include('../db/conexao.php'); 
    $estouEm = 3;


    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $pesquisa = '';

    if (isset($_GET['search-input'])) {
        $pesquisa = $_GET['search-input'];
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
</head>

<body>

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
                </div>
                <a href="novaFichaTrabalho.php" id="new-budget" class="report">
                    <i class='bx bx-plus'></i>
                    <span>Nova Ficha de Trabalho</span>
                </a>
            </div>

            <div class="bottom-data">
                <div class="budget">
                    <table>
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Contato</th>
                                <th>Nº Orçamento</th>
                                <th>Pronto em Armazém</th>
                                <th>Entrada em Obra</th>
                                <th>Saída de Obra</th>
                                <th>Elaborado Por</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT 
                                            client.name as nomeCliente, 
                                            client.contact as contactoCliente, 
                                            budget.id as idBudget, 
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
                                            budget ON worksheet.idBudget = budget.id;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>{$row['nomeCliente']}</td>
                                            <td>{$row['contactoCliente']}</td>
                                            <td>{$row['idBudget']}</td>
                                            <td>{$row['readyStorage']}</td>
                                            <td>{$row['joinWork']}</td>
                                            <td>{$row['exitWork']}</td>
                                            <td>{$row['nomeAdministrador']}</td>
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

            <!-- Modal -->
            <div id="budgetModal" class="modal">
                <div class="modal-content">
                    <div class="headerModal">
                        <h2>Selecione um Orçamento</h2>
                        <span class="close">&times;</span>
                    </div>
                    <form method="GET" action="">
                        <div class="form-input">
                            <input id="search-input" name="search-input" type="text" placeholder="Search..." value="<?=$pesquisa?>">
                            <button type="button" class="clear-button" onclick="limparPesquisa()">✖</button>
                        </div>
                    </form>
                    <div class="tabela">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Cliente</th>
                                    <th>Tipo Projeto</th>
                                    <th>Data Criação</th>
                                    <th>Elaborado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (isset($_GET['search-input']) && !empty($_GET['search-input'])) {
                                        $pesquisa = $con->real_escape_string($_GET['search-input']);
                                        $sql = "SELECT 
                                                    budget.id as idBudget,
                                                    budget.name as nomeBudget,
                                                    client.name as nomeCliente, 
                                                    project_type.name as projectType,
                                                    budget.created as dataCriacao,
                                                    administrator.name as responsavel
                                                FROM budget 
                                                LEFT JOIN administrator ON budget.createdBy = administrator.id
                                                LEFT JOIN client ON budget.idClient = client.id
                                                LEFT JOIN project_type ON budget.idProjectType = project_type.id
                                                WHERE idWorksheet IS NULL 
                                                AND (
                                                    budget.name LIKE '%$pesquisa%' 
                                                    OR client.name LIKE '%$pesquisa%'
                                                    OR project_type.name LIKE '%$pesquisa%'
                                                    OR budget.created LIKE '%$pesquisa%'
                                                    OR administrator.name LIKE '%$pesquisa%'
                                                )
                                                ORDER BY budget.id DESC";
                                    } else {
                                        $sql = "SELECT 
                                                    budget.id as idBudget,
                                                    budget.name as nomeBudget,
                                                    client.name as nomeCliente, 
                                                    project_type.name as projectType,
                                                    budget.created as dataCriacao,
                                                    administrator.name as responsavel
                                                FROM budget 
                                                LEFT JOIN administrator ON budget.createdBy = administrator.id
                                                LEFT JOIN client ON budget.idClient = client.id
                                                LEFT JOIN project_type ON budget.idProjectType = project_type.id
                                                WHERE idWorksheet IS NULL 
                                                ORDER BY budget.id DESC 
                                                LIMIT 10;";
                                    }

                                    $result = $con->query($sql);
                                    if (!$result) {
                                        die("Erro na consulta: " . $con->error);
                                    }

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr onclick=\"handleRowClick('{$row['idBudget']}', 'worksheet')\" style=\"cursor: pointer;\">
                                                <td>{$row['nomeBudget']}</td>
                                                <td>{$row['nomeCliente']}</td>
                                                <td>{$row['projectType']}</td>
                                                <td>{$row['dataCriacao']}</td>
                                                <td>{$row['responsavel']}</td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>Sem orçamentos disponíveis.</td></tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <script src="../index.js" defer>
        </script>
    </div>
</body>

</html>
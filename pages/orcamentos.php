<?php 
    include('../db/conexao.php'); 
    $estouEm = 2;

    session_start();

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
    <link rel="stylesheet" href="../css/orcamentos.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Orçamentos</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <h1>Orçamentos</h1>
                </div>
                <a href="#" id="new-budget" class="report">
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
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Contacto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if (isset($_GET['search-input']) && !empty($_GET['search-input'])) {
                                        $pesquisa = $con->real_escape_string($_GET['search-input']);
                                        $sql = "SELECT 
                                                    client.id as id,
                                                    client.name as nome,
                                                    client.email as email, 
                                                    client.contact as contacto
                                                FROM client 
                                                WHERE (
                                                    id LIKE '%$pesquisa%' 
                                                    OR nome LIKE '%$pesquisa%'
                                                    OR email LIKE '%$pesquisa%'
                                                    OR contacto LIKE '%$pesquisa%'
                                                )";
                                    } else {
                                        $sql = "SELECT * FROM client;";
                                    }

                                    $result = $con->query($sql);
                                    if (!$result) {
                                        die("Erro na consulta: " . $con->error);
                                    }

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr onclick=\"handleRowClick('{$row['id']}', 'budget')\" style=\"cursor: pointer;\">
                                                <td>{$row['id']}</td>
                                                <td>{$row['name']}</td>
                                                <td>{$row['email']}</td>
                                                <td>{$row['contact']}</td>
                                            </tr>";
                                        }
                                    } else {
                                        
                                        echo "<tr><td colspan='5'>Sem orçamentos disponíveis.</td></tr>";
                                        echo "<tr><td colspan='5' style='text-align: center; padding-top: 10px;'>
                                            <a href='../pages/novoCliente.php' style='display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; font-size: 14px;'>
                                                Criar Novo Cliente
                                            </a>
                                        </td></tr>";
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
                                            worksheet.name as nomeWorksheet, 
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
                                            <td>{$row['nomeWorksheet']}</td>
                                            <td>{$row['dataCriacao']}</td>
                                            <td>{$row['responsavel']}</td>
                                            <td><button class='btn-small' id='botDeleteBudget' onclick=\"deleteBudget('{$row['numBudget']}/{$row['yearBudget']}'); event.stopPropagation();\">🗑️</button></td>
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
            function deleteBudget(id) {
                console.log("ID do orçamento a ser excluído:", id);
                const result = confirm("Tem a certeza que deseja eliminar o orçamento " + id + "?");
                if (result) {
                    fetch(`deleteBudget.php?idBudget=${encodeURIComponent(id)}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                    })
                    .then(response => {
                        console.log("Resposta do servidor:", response);
                        if (!response.ok) {
                            throw new Error(`Erro HTTP! Status: ${response.status}`);
                        }
                        return response.text(); // Ou .json() dependendo do que o servidor retorna
                    })
                    .then(data => {
                        console.log("Dados retornados:", data);
                        alert('Orçamento eliminado com sucesso.');
                    })
                    .catch(error => {
                        console.error("Erro ao executar o fetch:", error);
                        alert("Ocorreu um erro ao eliminar o orçamento.");
                    });
                }
            }
        </script>
    </div>
</body>
</html>
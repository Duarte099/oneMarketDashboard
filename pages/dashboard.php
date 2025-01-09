<?php 
    session_start();
    include('../db/conexao.php'); 
    $estouEm = 1;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
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
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Dashboard</title>
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
                <h1>Dashboard</h1>
            </div>

            <!-- Insights -->
            <ul class="insights">
                <li >
                    <i class='bx bx-calculator'></i>
                    <span class="info">
                        <p>Orçamentos</p>
                        <h3>
                            <?php 
                                $sql = "SELECT COUNT(*) AS numeroOrçamentos FROM budget";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo $row['numeroOrçamentos'];
                                    }
                                } else {
                                    echo "0";
                                }
                            ?>
                        </h3>
                    </span>
                </li>
                <li><i class='bx bx-file'></i>
                    <span class="info">
                        <p>Fichas de trabalho</p>
                        <h3>
                            <?php 
                                $sql = "SELECT COUNT(*) AS numeroFichas FROM worksheet";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo $row['numeroFichas'];
                                    }
                                } else {
                                    echo "0";
                                }
                            ?>
                        </h3>
                    </span>
                </li>
                <li><i class='bx bx-user'></i>
                    <span class="info">
                        <p>Clientes</p>
                        <h3>
                            <?php 
                                $sql = "SELECT COUNT(*) AS numeroClientes FROM client";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo $row['numeroClientes'];
                                    }
                                } else {
                                    echo "0";
                                }
                            ?>
                        </h3>
                    </span>
                </li>
                <li><i class='bx bx-file'></i>
                    <span class="info">
                        <p>Fichas de trabalho ativas</p>
                        <h3>21</h3>
                    </span>
                </li>
            </ul>
            <!-- End of Insights -->

            <div class="bottom-data">
                <div class="tables">
                    <div class="header">
                        <i class='bx bx-calculator'></i>
                        <h3>Orçamentos recentes</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Cliente</th>
                                <th>Projeto</th>
                                <th>Responsavel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT 
                                            budget.name as budgetName, 
                                            budget.num as numBudget,
                                            budget.year as yearBudget,
                                            client.name as clientName,
                                            administrator.name as adminName
                                        FROM budget 
                                        INNER JOIN client ON budget.idClient = client.id
                                        INNER JOIN administrator ON administrator.id = budget.createdBy
                                        ORDER BY budget.id DESC LIMIT 10;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()){
                                        // $statusClass = ($row['status'] == 'Concluida') ? 'completa' : 'em-desenvolvimento';
                                        echo "<tr>
                                            <td>" . $row['numBudget'] . "/" . $row['yearBudget'] . "</td>
                                            <td>{$row['clientName']}</td>
                                            <td>{$row['budgetName']}</td>
                                            <td>{$row['adminName']}</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>Sem Orçamentos para mostrar.</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fichas de trabalho -->
            <div class="bottom-data">
                <div class="tables">
                    <div class="header">
                        <i class='bx bx-file'></i>
                        <h3>Fichas de trabalho recentes</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Cliente</th>
                                <th>Contacto</th>
                                <th>Projeto</th>
                                <th>Ficha de Trabalho</th>
                                <th>Data Criação</th>
                                <th>Responsavel</th>
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
                                        INNER JOIN 
                                            client ON worksheet.idclient = client.id
                                        INNER JOIN 
                                            administrator ON worksheet.createdBy = administrator.id
                                        INNER JOIN 
                                            budget ON worksheet.idBudget = budget.id
                                        ORDER BY worksheet.id DESC LIMIT 10;";
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
            <!-- Fim ficha de trabalho layout-->
        </main>
    </div>
    <script src="../index.js"></script>
</body>

</html>
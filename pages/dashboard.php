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
                <h1>Bem vindo <?php echo $_SESSION['name']; ?></h1>
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
            
            <!-- AGENDA -->
            <!-- <div class="agenda">
                <h2>Agenda</h2>
                <iframe src="https://calendar.google.com/calendar/embed?src=your_calendar_id&ctz=Europe/Lisbon" style="border: 0" width="100%" height="400" frameborder="0" scrolling="no"></iframe>
            </div> -->
            
            <div class="work-status">
                <div class="products">
                <h2>Estado das Fichas de Trabalho</h2>
                <table>
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Nº Orçamento</th>
                                <th>Pronto em armazém</th>
                                <th>Entrada em Obra</th>
                                <th>Saída de Obra</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $sql = "SELECT 
                                    budget.id as idBudget,
                                    budget.num as numBudget,
                                    budget.year as yearBudget, 
                                    worksheet.id as idWorksheet,
                                    worksheet.num as numWorksheet,
                                    worksheet.year as yearWorksheet,
                                    worksheet.readyStorage,
                                    worksheet.status,
                                    worksheet.joinWork, 
                                    worksheet.exitWork,
                                    CASE
                                        WHEN worksheet.readyStorage = '0000-00-00' THEN 'Em Desenvolvimento'
                                        WHEN worksheet.readyStorage != '0000-00-00' AND worksheet.joinWork = '0000-00-00' THEN 'Pendente'
                                        WHEN worksheet.joinWork != '0000-00-00' AND worksheet.exitWork = '0000-00-00' THEN 'Em Obra'
                                        WHEN worksheet.exitwork != '0000-00-00' THEN 'Concluido'
                                    END as status
                                FROM worksheet
                                LEFT JOIN 
                                    budget ON worksheet.idBudget = budget.id
                                ORDER BY idWorksheet DESC;";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                
                                while ($row = $result->fetch_assoc()) {
                                    $statustext = $row['status'];
                                    $statusicon = '';

                                    switch($statustext){
                                        case 'Em Desenvolvimento':
                                            $statusicon = '<i class="bx bx-time" style="color: yellow; margin-right: 8px;"></i>';
                                            break;
                                        case 'Pendente':
                                            $statusicon = '<i class="bx bx-error" style="color: red; margin-right: 8px;"></i>';
                                            break;
                                        case 'Em Obra':
                                            $statusicon = '<i class="bx bx-check-circle" style="color: orange; margin-right: 8px;"></i>';
                                            break;
                                        case 'Concluido':
                                            $statusicon = '<i class="bx bx-check-circle" style="color: green; margin-right: 8px;"></i>';
                                            break;
                                        default:
                                            $statusicon = '<i class="bx bx-question-mark" style="color: gray; margin-right: 8px;"></i>';
                                            break;
                                    }
                                    echo "<tr onclick=\"handleRowClick('{$row['idWorksheet']}', 'editWorksheet')\" style=\"cursor: pointer;\">
                                        <td>" . $row['numWorksheet'] . "/" . $row['yearWorksheet'] . "</td>
                                        <td>" . $row['numBudget'] . "/" . $row['yearBudget'] . "</td>
                                        <td>{$row['readyStorage']}</td>
                                        <td>{$row['joinWork']}</td>
                                        <td>{$row['exitWork']}</td>
                                        <td>{$statusicon}{$statustext}</td>
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

            <div class="logs-section">
                <div class="logs">
                <h2>Logs Recentes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Log</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sql = "SELECT dataLog, logFile
                            from administrator_logs
                            ORDER BY dataLog DESC
                            LIMIT 10";
                        $result = $con->query($sql);
                        if ($result->num_rows > 0) {  
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                <td>{$row['dataLog']}</td>
                                <td>{$row['logFile']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>Sem logs recentes para exibir.</td></tr>";
                        }
                    ?>
                </table>
            </div>
        </main>
    </div>
    <script src="../index.js"></script>
</body>

</html>
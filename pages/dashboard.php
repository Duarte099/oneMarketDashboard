<?php 
    include('../pages/head.php'); 

    $estouEm = 1;
    $anoAtual = date('Y');
?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li>
                    <i class='bx bx-calculator'></i>
                    <span class="info">
                        <p>Orçamentos</p>
                        <h3>
                            <?php
                                $sql = "SELECT COUNT(*) AS numeroOrcamentos FROM budget";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo $row['numeroOrcamentos'];
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
                <li><i class='bx bx-shield'></i>
                    <span class="info">
                        <p>Administradores</p>
                        <h3>
                            <?php
                                $sql = "SELECT COUNT(*) AS numeroAdmins FROM administrator";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo $row['numeroAdmins'];
                                    }
                                } else {
                                    echo "0";
                                }
                            ?>
                        </h3>
                    </span>
                </li>
            </ul>
            <!-- End of Insights -->

            <!-- Tabela produto fora de stock -->
            <div class="container">
                <div class="products-stock-custom">
                    <h2>Produtos Fora de Stock</h2>
                    <button type="button" onclick="window.location.href='produto.php'">Ver Mais</button>
                    <table>
                        <thead>
                            <tr>
                                <th>Imagem</th>
                                <th>Nome</th>
                                <th>Referência</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT 
                                        p.id AS id,
                                        p.img AS imagem,
                                        p.name AS name,
                                        p.reference AS reference
                                    FROM product_stock ps
                                    INNER JOIN product p ON ps.idProduct = p.id
                                    WHERE ps.quantity = 0 AND p.active = 1
                                    ORDER BY p.name ASC
                                    LIMIT 10;";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr onclick=\"handleRowClick('{$row['id']}', 'stock')\" style=\"cursor: pointer;\">
                                            <td>
                                                <div id=\"profilePic\" style=\"width:100%; height:50px; max-width:500px; background: url('{$row['imagem']}') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; border-radius: 250px;\">
                                                    <img src=\"../images/semfundo.png\" style=\"width:100%;padding-bottom: 13px;\">
                                                </div>
                                            </td>
                                            <td>" . $row['name'] . "</td>
                                            <td>" . $row['reference'] . "</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>Sem produtos fora de stock.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php if (adminPermissions($con, "adm_007", "view") == 1) { ?>
                    <!-- Ano -->
                    <div class="chart-container-custom">
                        <div class="year-selector">
                            <button id="prevYear" class="year-button">❮</button>
                            <span id="currentYear" class="year-display"><?php echo $anoAtual; ?></span>
                            <button id="nextYear" class="year-button">❯</button>
                        </div>
                        <canvas id="ganhosChart"></canvas>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            let anoAtual = new Date().getFullYear(); // Ano atual
                            let anoSelecionado = anoAtual; // Inicialmente, o ano selecionado é o atual
                            const anoDisplay = document.getElementById('currentYear');

                            // Atualiza o ano exibido no display
                            function atualizarAnoDisplay() {
                                anoDisplay.innerText = anoSelecionado;
                            }

                            // Atualiza o gráfico com base no ano selecionado
                            async function atualizarGrafico() {
                                const response = await buscarDados();
                                if (!response) {
                                    console.error('Não foi possível atualizar o gráfico.');
                                    return;
                                }

                                const data = response.data;
                                const canvas = document.getElementById('ganhosChart');
                                if (!canvas) {
                                    console.error('Canvas para o gráfico não encontrado.');
                                    return;
                                }

                                const ctx = canvas.getContext('2d');
                                if (!ctx) {
                                    console.error('Contexto 2D não disponível para o gráfico.');
                                    return;
                                }

                                // Verifica se o gráfico já existe antes de destruir
                                if (window.ganhosChart && typeof window.ganhosChart.destroy === 'function') {
                                    window.ganhosChart.destroy();
                                }

                                // Cria o novo gráfico
                                window.ganhosChart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                                        datasets: [{
                                            label: 'Ganhos em €',
                                            data: [
                                                data.JANE, data.FEVE, data.MARC, data.ABRI, data.MAIO,
                                                data.JUNH, data.JULH, data.AGOS, data.SETE, data.OUTR,
                                                data.NOVE, data.DEZE
                                            ],
                                            backgroundColor: '#781215',
                                            borderColor: '#781215',
                                            borderWidth: 1,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        }
                                    }
                                });

                                // Atualiza o título do gráfico
                                const titulo = document.getElementById('tituloGrafico');
                                if (titulo) {
                                    titulo.innerText = `Ganhos Mensais em € (${anoSelecionado})`;
                                }
                            }

                            // Busca os dados do ano selecionado
                            async function buscarDados() {
                                try {
                                    const response = await fetch(`json.obterGanhosAnuais.php?ano=${anoSelecionado}`);
                                    if (!response.ok) {
                                        throw new Error('Erro ao buscar os dados.');
                                    }
                                    return await response.json();
                                } catch (error) {
                                    console.error(error);
                                    return null;
                                }
                            }

                            // Eventos dos botões
                            document.getElementById('prevYear').addEventListener('click', function() {
                                if (anoSelecionado > anoAtual - 1) { // Apenas permite o ano atual e o anterior
                                    anoSelecionado--;
                                    atualizarAnoDisplay();
                                    atualizarGrafico();
                                }
                            });

                            document.getElementById('nextYear').addEventListener('click', function() {
                                if (anoSelecionado < anoAtual) { // Apenas permite voltar ao ano atual
                                    anoSelecionado++;
                                    atualizarAnoDisplay();
                                    atualizarGrafico();
                                }
                            });

                            // Inicializa o gráfico com o ano atual
                            atualizarAnoDisplay();
                            atualizarGrafico();
                        });
                    </script>
                <?php  } ?>
            </div>

            <div class="fichalogs">
                <div class="work-status">
                    <div class="products">
                        <h2>Estado das Fichas de Trabalho</h2>
                        <button type="button" onclick="window.location.href='fichatrabalho.php'">Ver Mais</button>
                        <table>
                            <thead>
                                <tr>
                                    <th>Numero</th>
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
                                    ORDER BY idWorksheet DESC
                                    LIMIT 10;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {

                                    while ($row = $result->fetch_assoc()) {
                                        $statustext = $row['status'];
                                        $statusicon = '';

                                        switch ($statustext) {
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

                <?php if (adminPermissions($con, "adm_006", "view") == 1) { ?>
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
                    <?php  } ?>
                </div>
            </div>
        </main>
    </div>
    
</body>

</html>
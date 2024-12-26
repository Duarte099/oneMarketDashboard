<?php 
    include('../db/conexao.php'); 
    $estouEm = 5;

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
    <link rel="stylesheet" href="../css/client.css">
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
                    <h1>Clientes</h1>
                </div>
                <a href="../pages/novoCliente.php" id="new-budget" class="report">
                    <i class='bx bx-plus'></i>
                    <span>Novo Cliente</span>
                </a>
            </div>

            <div class="bottom-data">
                <div class="client">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Contato</th>
                                <th>NIF</th>
                                <th>Status</th>
                                <th>Data-Criação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT
                                            client.id,
                                            client.name, 
                                            client.email,
                                            client.contact,
                                            client.nif,
                                            client.active,
                                            client.created
                                        FROM client;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status = $row['active'] == 1 ? 'Ativo' : 'Inativo';
                                        echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>{$row['name']}</td>
                                            <td>{$row['email']}</td>
                                            <td>{$row['contact']}</td>
                                            <td>{$row['nif']}</td>
                                            <td>{$status}</td>
                                            <td>{$row['created']}</td>
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
    </div>
</body>

</html>
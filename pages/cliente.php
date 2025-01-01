<?php 
    include('../db/conexao.php'); 
    $estouEm = 5;

    session_start();

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
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Contacto</th>
                                <th>NIF</th>
                                <th>Status</th>
                                <th></th>
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
                                            <td><button class='btn-small' id='botDeleteClient' onclick=\"event.stopPropagation(); deleteClient('{$row['name']}', {$row['id']});\">🗑️</button></td>
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
        <script>
            function deleteClient(name, id) {
                console.log("ID do cliente a ser excluído:", id);
                const result = confirm("Tem a certeza que deseja eliminar o cliente " + name + "?");
                if (result) {
                    fetch(`./deleteClient.php?id=${encodeURIComponent(id)}`, {
                        method: 'GET',
                    })
                    .then(() => {
                        console.log("ID enviado com sucesso via GET.");
                    })
                    .catch(error => {
                        console.error("Erro ao enviar ID:", error);
                    });
                }
                window.location.href = window.location.pathname;
            }
        </script>
    </div>
</body>

</html>
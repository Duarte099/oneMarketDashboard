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
    <link rel="stylesheet" href="../css/novoCliente.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Novo Cliente</title>
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
                    <h1>Novo Cliente</h1>
                </div>
            </div>
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $nome = trim($_POST['nome']);
                    $email = trim($_POST['email']);
                    $contato = trim($_POST['contato']);
                    $nif = trim($_POST['nif']);
                    $status = intval($_POST['status']);
                
                    if (!empty($nome) && !empty($email) && !empty($contato) && !empty($nif)) {
                        $query = "INSERT INTO client (name, email, contact, nif, active) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $con->prepare($query);
                
                        if ($stmt) {
                            $stmt->bind_param("ssssi", $nome, $email, $contato, $nif, $status);
                
                            if ($stmt->execute()) {
                                header('Location: cliente.php');
                                exit();
                            }
                        }
                    }
                }
            ?>
            <div class="bottom-data">
                <div class="client">
                    <form method="POST" action="">
                    <section>
                        <h2>Dados do Cliente</h2>
                        <div class="section-row">
                            <div class="section-group">
                                <label>Nome:</label>
                                <input type="text" name="nome" required>
                            </div>
                            <div class="section-group">
                                <label>Email:</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="section-group">
                                <label>Contacto:</label>
                                <input type="number" name="contato" required>
                            </div>

                        </div>
                        <div class="section-row">
                            <div class="section-group">
                                <label>NIF:</label>
                                <input type="number" name="nif" required>
                            </div>
                            <div class="section-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit">Adicionar Cliente</button>
                    </section>
                </div>
            </div>
        </main>

        <script>
        </script>

    </div>
</body>

</html>
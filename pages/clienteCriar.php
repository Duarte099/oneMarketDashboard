<?php 
    session_start();
    include('../db/conexao.php'); 
    $estouEm = 5;

    $permission = adminPermissions("adm_004", "inserir");

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $permission == 0) {
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
            <div class="bottom-data">
                <div class="client">
                    <form method="POST" action="inserirCliente.php">
                        <section>
                            <h2>Dados do Cliente</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Nome:</label>
                                    <input type="text" name="nome" required>
                                </div>
                                <div class="section-group">
                                    <label>Email:</label>
                                    <input type="email" name="email">
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="number" name="contacto" required>
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
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
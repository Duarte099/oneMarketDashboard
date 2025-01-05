<?php 
    include('../db/conexao.php'); 
    $estouEm = 4;

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
    <link rel="stylesheet" href="../css/novoproduto.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Novo Produto</title>
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
                    <h1>Novo Produto</h1>
                </div>
            </div>
            <div class="bottom-data">
                <div class="client">
                    <form method="POST" action="inserirProduto.php">
                    <section>
                        <h2>Dados do Produto</h2>
                        <div class="section-row">
                            <div class="section-group">
                                <label>Img:</label>
                                <input type="image" name="img">
                            </div>
                            <div class="section-group">
                                <label>Nome</label>
                                <input type="text" name="name" required>
                            </div>
                            <div class="section-group">
                                <label>Referencia:</label>
                                <input type="text" name="reference" required>
                            </div>

                        </div>
                        <div class="section-row">
                            <div class="section-group">
                                <label>Valor:</label>
                                <input type="number" name="value" required>
                            </div>
                            <div class="section-group">
                                <label>Stock</label>
                                <input type="number" name="stock">
                            </div>
                        </div>
                        <button type="submit">Adicionar Produto</button>
                    </section>
                </div>
            </div>
        </main>

        <script>
        </script>

    </div>
</body>

</html>
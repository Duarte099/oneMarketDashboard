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
    <link rel="stylesheet" href="../css/stock.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Stock</title>
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
                    <h1>Stock de produtos</h1>
                </div>
                <a href="#" class="report">
                    <i class='bx bx-cloud-download'></i>
                    <span>Download CSV</span>
                </a>
            </div>
            <div class="bottom-data">
                <div class="products">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nome</th>
                                <th>Referencia</th>
                                <th>Valor</th>
                                <th>Stock</th>
                                <img src="" alt="">
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT 
                                            product.img as imagem, 
                                            product.id as idProduto, 
                                            product.name as nomeProduto, 
                                            product.reference as refProduto, 
                                            product.value as valorProduto, 
                                            product_stock.quantity as stockProduto
                                        FROM product
                                        LEFT JOIN product_stock ON product.id = product_stock.idProduct;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                                <td><img src={$row['imagem']}></td>
                                                <td>{$row['nomeProduto']}</td>
                                                <td>{$row['refProduto']}</td>
                                                <td>{$row['valorProduto']}</td>
                                                <td>" . number_format($row['stockProduto'], 0) . "</td>
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
    </div>

    <script src="../index.js"></script>
</body>

</html>
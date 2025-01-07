<?php
    session_start();

    include('../db/conexao.php'); 
    $estouEm = 4;


    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    // Verificar se o id está na URL
    if (isset($_GET['idProduct'])) {
        $id = intval($_GET['idProduct']);
    } else {
        header('Location: stock.php');  // Caso não esteja na url volta para a página dos produtos
        exit();
    }   

    // Buscar o id selecionado antes
    $query = "SELECT * FROM product WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $reference = $product['reference'];
        $name = $product['name'];
        $value = $product['value'];
        $img = $product['img'];
    }

    // Se não encontrar o produto selecionado
    if ($result->num_rows === 0) {
        header('Location: stock.php');  // Manda para a página dos produtos
        exit();
    }

    // Pega as informações do protudo
    $product = $result->fetch_assoc();


    // Buscar a quantidade da tabela product stock
    $stockQuery = "SELECT quantity FROM product_stock WHERE idProduct = ?";
    $stockStmt = $con->prepare($stockQuery);
    $stockStmt->bind_param("i", $id);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result();

    if ($stockResult->num_rows > 0) {
        $stock = $stockResult->fetch_assoc();
        $quantity = $stock['quantity'];
    }


    $product_stock = $stockResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Editar Produto</title>
</head>
<body>

    <?php include('../pages/sideBar.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <?php include('../pages/header.php'); ?>          
        
        <main>
            <div class="header">
                <div class="left">
                    <h1>Editar Produto</h1>
                </div>
            </div>

            <div class="form-container">
                <form action="inserirProduto.php?idProduct=<?= $id ?>&op=edit" id="profileForm" method=post>
                    <section>
                    <div class="column-left">
                        <label for="photo">Foto do Produto:</label>
                        <img src="<?php echo $img; ?>" alt="Product Picture" id="profilePic">
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()">
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $name; ?>">

                        <label for="ref">Referencia:</label>
                        <input type="text" name="ref" id="ref" value="<?php echo $reference; ?>">

                        <label for="valor">Valor:</label>
                        <input type="number" name="valor" id="valor" value="<?php echo $value; ?>">

                        <label for="stock">Stock:</label>
                        <input type="text" name="stock" id="stock" value="<?php echo $quantity; ?>">

                        <button type="submit">Guardar alterações</button>
                    </div>
                    </section>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

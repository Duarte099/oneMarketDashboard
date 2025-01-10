<?php 
    session_start();

    $estouEm = 4;

    include('../db/conexao.php');

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $idProduct = $_GET['idProduct'];

    $sql = "SELECT id, img, name, reference, value, quantity, active FROM product
            INNER JOIN product_stock ON idProduct = id
            WHERE product.id = $idProduct;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id =  $row['id'];
        $image =  $row['img'];
        $name =  $row['name'];
        $ref =  $row['reference'];
        $value =  $row['value'];
        $quantity =  $row['quantity'];
        $status = $row['active'];
    }
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
                    <h1>Editar Produto</h1>
                </div>
            </div>
            <div class="form-container">
                <form action="../pages/produtoInserir.php?op=edit" id="profileForm" method="post" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto do produto:</label>
                        <div style="width:100%; max-width:500px; background: url('<?php echo $img; ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="../images/semfundo.png" style="width:100%;">
                        </div>
                        <!--<img src="<?php echo $img; ?>" alt="Profile Picture" id="profilePic">-->
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $name; ?>">

                        <label for="ref">Referencia:</label>
                        <input type="text" name="ref" id="ref" value="<?php echo $ref; ?>">

                        <label for="value">Valor:</label>
                        <input type="text" name="value" id="value" value="<?php echo str_replace('.', '.', $value); ?>" />

                        <label for="quantity">Stock:</label>
                        <input type="number" name="quantity" id="quantity" value="<?php echo $quantity; ?>">
                        
                        <label>Status:</label>
                        <?php
                        ?>
                            <select name="status">
                                <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Ativo</option>
                                <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Inativo</option>
                            </select>

                        <button type="submit">Guardar alterações</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../index.js"></script>
    <script>
        function displayProfilePic(){
            const file = event.target.files[0]; // Obtém o primeiro arquivo selecionado
            const preview = document.getElementById('profilePic');

            if (file) {
                const reader = new FileReader();

                // Evento para quando o arquivo for carregado
                reader.onload = function(e) {
                preview.src = e.target.result; // Define o src da imagem para o resultado do arquivo carregado
                };

                reader.readAsDataURL(file); // Lê o arquivo como URL base64
            } else {
                preview.src = ""; // Remove a imagem se nenhum arquivo for selecionado
            }
        }

        const form = document.getElementById('profileForm');
            form.addEventListener('submit', (event) => {
            const valueInput = document.getElementById('value');
            valueInput.value = valueInput.value.replace(',', '.'); // Substitui vírgula por ponto
        });
    </script>


</body>
</html>
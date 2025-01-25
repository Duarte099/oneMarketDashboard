<?php 
    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php');

    $estouEm = 4;

    if (adminPermissions("adm_003", "inserir") == 0) {
        header('Location: dashboard.php');
        exit();
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
    <title>OneMarket | Criar Produto</title>
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
            <div class="form-container">
                <form action="../pages/produtoInserir.php" id="profileForm" method="post" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="../images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                        </div>
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name">

                        <label for="ref">Referencia:</label>
                        <input type="text" name="ref" id="ref">

                        <label for="value">Valor:</label>
                        <input type="number" name="value" id="value">

                        <label for="quantity">Stock:</label>
                        <input type="number" name="quantity" id="quantity">

                        <label for="quantity">Status:</label>
                        <select name="status">
                            <option value="1" class="selectoption">Ativo</option>
                            <option value="0" class="selectoption">Inativo</option>
                        </select>

                        <button type="submit" style="margin-top: 15px">Guardar alterações</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../index.js"></script>
    <script>
        function displayProfilePic() {
            const file = event.target.files[0]; // Obtém o primeiro arquivo selecionado
            const preview = document.getElementById('profilePic'); // Seleciona a div

            if (file) {
                const reader = new FileReader();

                // Evento para quando o arquivo for carregado
                reader.onload = function(e) {
                    // Define o background-image da div com o resultado do arquivo carregado
                    preview.style.backgroundImage = `url(${e.target.result})`;
                };

                reader.readAsDataURL(file); // Lê o arquivo como URL base64
            } else {
                // Remove a imagem de fundo se nenhum arquivo for selecionado
                preview.style.backgroundImage = "";
            }
        }
    </script>
</body>

</html>
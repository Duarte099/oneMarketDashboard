<?php 
    include('head.php'); 

    $estouEm = 4;

    if (adminPermissions($con, "adm_003", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idProduct = $_GET['idProduct'];
    $sql = "SELECT * FROM product WHERE id = '$idProduct'";
    $result = $con->query($sql);
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    else {
        $row = $result->fetch_assoc();
        $nomeProduto = $row['name'];
        $imgProduct = $row['img'];
        $ref = $row['reference'];
        $value = $row['value'];
        $active = $row['active'];
    }

    $sql = "SELECT quantity FROM product_stock WHERE idProduct = '$idProduct'";
    $result = $con->query($sql);
    if ($result->num_rows >= 0) {
        $row = $result->fetch_assoc();
        $quantidade = $row['quantity'];
    }
?>
    <link rel="stylesheet" href="./css/perfil.css">
    
    <title>OneMarket | <?php echo $nomeProduto; ?></title>
</head>

<body>

    <?php 
        include('sideBar.php'); 
    ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php 
            include('header.php'); 
        ?>          
        <!-- End of Navbar -->
        
        <main>
            <div class="header">
                <div class="left">
                    <h1><?php echo $nomeProduto ?></h1>
                </div>
            </div>
            <div class="form-container">
                <form method="POST" action="produtoInserir.php?idProduct=<?= $idProduct ?>&op=edit" id="profileForm" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto do produto:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('<?php echo $imgProduct ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="./images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                        </div>
                        <?php if (adminPermissions($con, "adm_003", "update") == 1) { ?>
                            <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                        <?php } ?>
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $nomeProduto; ?>" <?php if (adminPermissions($con, "adm_003", "update") == 0) {echo "readonly";}?>>

                        <label for="ref">Referencia:</label>
                        <input type="text" name="ref" id="ref" value="<?php echo $ref; ?>" <?php if (adminPermissions($con, "adm_003", "update") == 0) {echo "readonly";}?>>

                        <label for="value">Valor:</label>
                        <input type="text" name="value" id="value" value="<?php echo str_replace('.', '.', $value); ?>" <?php if (adminPermissions($con, "adm_003", "update") == 0) {echo "readonly";}?>>

                        <label for="quantity">Stock:</label>
                        <input type="int" name="quantity" id="quantity" value="<?php echo intval($quantidade); ?>" <?php if (adminPermissions($con, "adm_003", "update") == 0) {echo "readonly";}?>>
                        
                        <label>Status:</label>
                        <?php if (adminPermissions($con, "adm_003", "update") == 0) { ?>
                            <input type="text" name="status" id="status" value="<?php if ($active == 0) {echo "Inativo";} else {echo "Ativo";}?>" readonly>
                        <?php } else {?>
                            <select name="status">
                                <option value="1" <?php echo $active == 1 ? 'selected' : ''; ?>>Ativo</option>
                                <option value="0" <?php echo $active == 0 ? 'selected' : ''; ?>>Inativo</option>
                            </select>
                        <?php } ?>
                        
                        <?php if (adminPermissions($con, "adm_003", "update") == 1) { ?>
                            <button type="submit" style="margin-top: 15px">Guardar alterações</button>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </main>
    </div>

    
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

        const form = document.getElementById('profileForm');
            form.addEventListener('submit', (event) => {
            const valueInput = document.getElementById('value');
            valueInput.value = valueInput.value.replace(',', '.'); // Substitui vírgula por ponto
        });
    </script>
</body>
</html>
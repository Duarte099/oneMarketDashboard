<?php
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 1;

    //Obtem o id do administrador logado
    $idAdmin = $_SESSION['id'];

    //Obtem todos os dados do administrador logado
    $sql = "SELECT name, email, user, img, birthday FROM administrator WHERE administrator.id = $idAdmin;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name =  $row['name'];
        $email =  $row['email'];
        $user =  $row['user'];
        $imgPerfil =  $row['img'];
        $birthday =  $row['birthday'];
    }
?>
    <link rel="stylesheet" href="./css/perfil.css">
    <title>OneMarket | Perfil</title>
</head>

<body>

    <?php 
        //Inclui a sideBar na página
        include('sideBar.php'); 
    ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php 
            //Inclui o header na página
            include('header.php'); 
        ?>          
        <!-- End of Navbar -->
        
        <main>
            <div class="header">
                <div class="left">
                    <h1>Perfil</h1>
                </div>
            </div>
            <div class="form-container">
                <form action="perfilInserir.php" id="profileForm" method="post" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto de perfil:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('<?php echo $imgPerfil; ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="./images/semfundo.png" style="width:100%;">
                        </div>
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $name; ?>">

                        <label for="user">Nome de utilizador:</label>
                        <input type="text" name="user" id="user" value="<?php echo $user; ?>">

                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?php echo $email; ?>">

                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" placeholder="Nova password">
                        <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirmar nova password">

                        <label for="birthday">Data nascimento:</label>
                        <input type="date" name="birthday" id="birthday" value="<?php echo $birthday; ?>">

                        <button type="submit" onclick="return validarPass()">Guardar alterações</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    
    <script>
        //Função para ver se a palavra pass é igual nos dois campos
        function validarPass() {
            const pass = document.querySelector('input[name="password"]');
            const passC = document.querySelector('input[name="passwordConfirm"]');

            // Verifica se as senhas são diferentes
            if (pass.value !== passC.value) {
                passC.setCustomValidity("As palavras-passe não coincidem!");
                passC.reportValidity();
                return false;
            } else {
                passC.setCustomValidity("");
                return true;
            }
        }

        //Função para mostrar a nova foto inserida
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
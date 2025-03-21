<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 
    
    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 6;

    //Verificar se o admin tem permissões para criar administradores
    if (adminPermissions($con, "adm_005", "inserir") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Obtem o numero de modulos existentes
    $sql = "SELECT COUNT(id) AS numModules FROM modules;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numModules = $row['numModules'];
    }
?>
    <link rel="stylesheet" href="./css/adminCriar.css">
    <title>OneMarket | Novo Administrador</title>
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
                    <h1>Novo Administrador</h1>
                </div>
            </div>
            <div class="form-container">
                <form action="adminInserir.php?op=save" id="profileForm" method="post" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="./images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                        </div>
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                    </div>
                    <div class="column-right" id="infoSection">
                        <div class="button-container">
                            <button type="button" id="infoButton1" class="toggle-button" onclick="showSection('info')">Informações</button>
                            <button type="button" id="permissionsButton1" class="toggle-button" onclick="showSection('permissions')">Permissões</button>
                        </div>

                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" required>

                        <label for="user">Nome de utilizador:</label>
                        <input type="text" name="user" id="user" required>

                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" required>

                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" placeholder="Nova password" required>
                        <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirmar nova password" required>

                        <label for="birthday">Data nascimento:</label>
                        <input type="date" name="birthday" id="birthday">

                        <label for="status">Status:</label>
                        <select name="status">
                            <option value="1" class="selectoption">Ativo</option>
                            <option value="0" class="selectoption">Inativo</option>
                        </select>

                        <button type="submit" id="submitButton" style="margin-top: 15px" onclick="return validarPass()">Criar Administrador</button>
                    </div>
                    <div class="column-right" id="permissionsSection" style="display:none;">
                        <div class="button-container">
                            <button type="button" id="infoButton2" class="toggle-button" onclick="showSection('info')">Informações</button>
                            <button type="button" id="permissionsButton2" class="toggle-button" onclick="showSection('permissions')">Permissões</button>
                        </div>
                        <div class="modules">
                            <?php
                            //query sql para buscar os modulos todos
                                $sql = "SELECT module AS nameModule, id FROM modules;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        //mostra os resultados em forma de checkboxes para selecionar as permissões
                                        echo "<div class=\"module\">
                                            <span>{$row['nameModule']}</span>
                                            <div class=\"permissions\">
                                                <label><input type=\"checkbox\" name=\"modulo_{$row['id']}_perm_ver\"> Ver</label>
                                                <label><input type=\"checkbox\" name=\"modulo_{$row['id']}_perm_edit\"> Editar</label>
                                                <label><input type=\"checkbox\" name=\"modulo_{$row['id']}_perm_criar\"> Criar</label>
                                                <label><input type=\"checkbox\" name=\"modulo_{$row['id']}_perm_apagar\"> Apagar</label>
                                            </div>
                                        </div>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>Sem registros para exibir.</td></tr>";
                                }
                            ?>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    
    <script>
        //Valida se os campos de password e confimarção de password contem o mesmo valor
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

        //Mostra a foto que foi inserida
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

        //Mostra a secção de permissões ou de informações ao clicar
        function showSection(section) {
            const infoSection = document.getElementById('infoSection');
            const permissionsSection = document.getElementById('permissionsSection');
            const infoButton1 = document.getElementById('infoButton1');
            const permissionsButton1 = document.getElementById('permissionsButton1');
            const infoButton2 = document.getElementById('infoButton2');
            const permissionsButton2 = document.getElementById('permissionsButton2');

            if (section == 'info') {
                infoSection.style.display = 'block';
                permissionsSection.style.display = 'none';
                permissionsButton1.style.backgroundColor = 'var(--background-color)';
                permissionsButton2.style.color = '#fff';
                permissionsButton1.style.color = 'var(--primary-text-color)';
                infoButton1.style.backgroundColor = 'var(--theme-color)';
            }
            if (section == 'permissions') {
                infoSection.style.display = 'none';
                permissionsSection.style.display = 'block';
                permissionsButton2.style.backgroundColor = 'var(--theme-color)';
                infoButton2.style.color = 'var(--primary-text-color)';
                infoButton2.style.backgroundColor = 'var(--background-color)';
            }
        }

        // Mostrar "Informações" ao carregar a página
        document.addEventListener('DOMContentLoaded', () => showSection('info'));
    </script>   
</body>

</html>
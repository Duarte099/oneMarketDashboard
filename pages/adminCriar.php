<?php 
    session_start();

    $estouEm = 6;

    include('../db/conexao.php');

    $permission = adminPermissions("adm_005", "inserir");

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true  || $permission == 0) {
        header('Location: index.php');
        exit();
    }

    $sql = "SELECT COUNT(id) AS numModules FROM modules;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numModules = $row['numModules'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/adminCriar.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Criar Administrador</title>
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
                    <h1>Novo Administrador</h1>
                </div>
            </div>
            <div class="form-container">
                <form action="../pages/adminInserir.php?op=save" id="profileForm" method="post" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="../images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                        </div>
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                    </div>
                    <div class="column-right" id="infoSection">
                        <div class="button-container">
                            <button type="button" id="infoButton1" class="toggle-button" onclick="showSection('info')">Informações</button>
                            <button type="button" id="permissionsButton1" class="toggle-button" onclick="showSection('permissions')">Permissões</button>
                        </div>

                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name">

                        <label for="user">Nome de utilizador:</label>
                        <input type="text" name="user" id="user">

                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email">

                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" placeholder="Nova password">
                        <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirmar nova password">

                        <label for="birthday">Data nascimento:</label>
                        <input type="date" name="birthday" id="birthday">

                        <label for="status">Status:</label>
                        <select name="status">
                            <option value="1" class="selectoption">Ativo</option>
                            <option value="0" class="selectoption">Inativo</option>
                        </select>

                        <button type="submit" id="submitButton" onclick="return validarPass()">Criar Administrador</button>
                    </div>
                    <div class="column-right" id="permissionsSection" style="display:none;">
                        <div class="button-container">
                            <button type="button" id="infoButton2" class="toggle-button" onclick="showSection('info')">Informações</button>
                            <button type="button" id="permissionsButton2" class="toggle-button" onclick="showSection('permissions')">Permissões</button>
                        </div>
                        <div class="modules">
                            <?php
                                $sql = "SELECT module AS nameModule, id FROM modules;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
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

    <script src="../index.js"></script>
    <script>
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
                infoButton1.style.backgroundColor = 'var(--theme-color)';
            }
            if (section == 'permissions') {
                infoSection.style.display = 'none';
                permissionsSection.style.display = 'block';
                permissionsButton2.style.backgroundColor = 'var(--theme-color)';
                infoButton2.style.backgroundColor = 'var(--background-color)';
            }
        }

        // Mostrar "Informações" ao carregar a página
        document.addEventListener('DOMContentLoaded', () => showSection('info'));
    </script>   
</body>

</html>
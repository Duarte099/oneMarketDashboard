<?php 
    include('../pages/head.php'); 
    $estouEm = 6;

    if (adminPermissions($con, "adm_005", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idAdminEdit = $_GET['idAdmin'];

    $sql = "SELECT * FROM administrator WHERE id = '$idAdminEdit'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nome = $row['name'];
        $email = $row['email'];
        $user = $row['user'];
        $imgAdmin = $row['img'];
        $adminMor = $row['adminMor'];
        $birthday = $row['birthday'];
        $status = $row['active'];
    }
    else{
        header('Location: dashboard.php');
        exit();
    }

    $sql = "SELECT MAX(id) AS numModules FROM modules;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numModules = $row['numModules'];
    }
?>
    <link rel="stylesheet" href="../css/adminCriar.css">
    
    <title>OneMarket | <?php echo $user; ?></title>
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
                    <h1><?php echo $nome; ?></h1>
                    <?php if ($adminMor == 1) { ?>
                        <label>*Este é um administrador mor, não podes alterar as informações e permissões.</label>
                    <?php } ?>
                </div>
            </div>
            <div class="form-container">
                <form action="../pages/adminInserir.php?idAdmin=<?=$idAdminEdit?>&op=edit" id="profileForm" method="post" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('<?php echo $imgAdmin; ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="../images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                        </div>
                        <?php if (adminPermissions($con, "adm_005", "update") == 1) { if ($adminMor == 0) { ?>
                            <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                        <?php } }?>
                        </div>
                    <div class="column-right" id="infoSection">
                        <div class="button-container">
                            <button type="button" id="infoButton1" class="toggle-button" onclick="showSection('info')">Informações</button>
                            <?php if ($adminMor == 0) { ?>
                                <button type="button" id="permissionsButton1" class="toggle-button" onclick="showSection('permissions')">Permissões</button>
                            <?php } ?>
                        </div>

                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $nome; ?>" <?php if (adminPermissions($con, "adm_005", "update") == 0 || $adminMor == 1) {echo "readonly";}?>>

                        <label for="user">Nome de utilizador:</label>
                        <input type="text" name="user" id="user" value="<?php echo $user; ?>" <?php if (adminPermissions($con, "adm_005", "update") == 0 || $adminMor == 1) {echo "readonly";}?>>

                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?php echo $email; ?>" <?php if (adminPermissions($con, "adm_005", "update") == 0 || $adminMor == 1) {echo "readonly";}?>>

                        <?php if (adminPermissions($con, "adm_005", "update") == 1) { if ($adminMor == 0) {?>
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" placeholder="Nova password">
                            <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirmar nova password">
                        <?php }} ?>

                        <label for="birthday">Data nascimento:</label>
                        <input type="date" name="birthday" id="birthday" value="<?php echo $birthday; ?>" <?php if (adminPermissions($con, "adm_005", "update") == 0 || $adminMor == 1) {echo "readonly";}?>>

                        <label for="status">Status:</label>
                        <?php if (adminPermissions($con, "adm_005", "update") == 0 || $adminMor == 1) {?>
                            <input type="text" name="status" id="status" value="<?php if ($status == 0) {echo "Inativo";} else {echo "Ativo";}?>" readonly>
                        <?php } else {?>
                            <select name="status">
                                <option value="1" <?php echo $status == 1 ? 'selected' : ''; ?>>Ativo</option>
                                <option value="0" <?php echo $status == 0 ? 'selected' : ''; ?>>Inativo</option>
                            </select>
                        <?php } ?>
                        
                        <?php if (adminPermissions($con, "adm_005", "update") == 1) { if ($adminMor == 0) { ?>
                            <button type="submit" id="submitButton" style="margin-top: 15px" onclick="return validarPass()">Guardar Alterações</button>
                        <?php }} ?>
                    </div>
                    <?php if ($adminMor == 0) { ?>
                        <div class="column-right" id="permissionsSection" style="display:none;">
                            <div class="button-container">
                                <button type="button" id="infoButton2" class="toggle-button" onclick="showSection('info')">Informações</button>
                                <button type="button" id="permissionsButton2" class="toggle-button" onclick="showSection('permissions')">Permissões</button>
                            </div>
                            <div class="modules">
                                <?php
                                    for ($i=1; $i <= $numModules; $i++) { 
                                        $sql = "SELECT id FROM modules WHERE id = $i;";
                                        $result = $con->query($sql);
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            $idModule =  $row['id'];
                                        }
                                        if ($i == $idModule) {
                                            $pView = "";
                                            $pInsert = "";
                                            $pUpdate = "";
                                            $pDelete = "";

                                            $sql = "SELECT pView, pInsert, pUpdate, pDelete FROM administrator_modules WHERE idAdministrator = $idAdminEdit AND idModule = $i;";
                                            $result2 = $con->query($sql);
                                            
                                            if ($result2->num_rows > 0) {
                                                $row2 = $result2->fetch_assoc();
                                            
                                                if ($row2['pView'] == 1) {
                                                    $pView = "checked";
                                                }
                                                if ($row2['pInsert'] == 1) {
                                                    $pInsert = "checked";
                                                }
                                                if ($row2['pUpdate'] == 1) {
                                                    $pUpdate = "checked";
                                                }
                                                if ($row2['pDelete'] == 1) {
                                                    $pDelete = "checked";
                                                }
                                            }
                                            $sql = "SELECT id, cod, module AS nameModule FROM modules WHERE id = $i;";
                                            $result3 = $con->query($sql);
                                            if ($result3->num_rows > 0) {        
                                                $row3 = $result3->fetch_assoc();
                                                $id = $row3['id'];
                                                $nome = $row3['nameModule'];
                                                if ($row3['cod'] == "adm_007") {
                                                    ?>
                                                        <div class="module">
                                                            <span><?php echo $nome;?></span>
                                                            <div class="permissions">
                                                                <label><input type="checkbox" name="modulo_<?php echo $id; ?>_perm_ver" <?php echo $pView; ?> <?php if (adminPermissions($con, "adm_005", "update") == 0) {echo "disabled";}?>> Ver</label>
                                                            </div>
                                                        </div>
                                                    <?php
                                                }
                                                else {
                                                    ?>
                                                        <div class="module">
                                                            <span><?php echo $nome;?></span>
                                                            <div class="permissions">
                                                                <label><input type="checkbox" name="modulo_<?php echo $id; ?>_perm_ver" <?php echo $pView; ?> <?php if (adminPermissions($con, "adm_005", "update") == 0) {echo "disabled";}?>> Ver</label>
                                                                <label><input type="checkbox" name="modulo_<?php echo $id; ?>_perm_edit" <?php echo $pUpdate; ?> <?php if (adminPermissions($con, "adm_005", "update") == 0) {echo "disabled";}?>> Editar</label>
                                                                <label><input type="checkbox" name="modulo_<?php echo $id; ?>_perm_criar" <?php echo $pInsert; ?> <?php if (adminPermissions($con, "adm_005", "update") == 0) {echo "disabled";}?>> Criar</label>
                                                                <label><input type="checkbox" name="modulo_<?php echo $id; ?>_perm_apagar" <?php echo $pDelete; ?> <?php if (adminPermissions($con, "adm_005", "update") == 0) {echo "disabled";}?>> Apagar</label>
                                                            </div>
                                                        </div>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </main>
    </div>

    
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
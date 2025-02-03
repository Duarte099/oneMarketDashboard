<?php
    //Inclui a conexão à base de dados na página
    include('./db/conexao.php'); 

    //Caso a variavel op nao esteja declarado e o metodo não seja post volta para a página da dashboard
    if (isset($_GET['op']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        //Obtem o operação 
        $op = $_GET['op'];

        //Obtem o numero de modulos
        $sql = "SELECT MAX(id) AS numModules FROM modules;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $numModules = $row['numModules'];
        }

        if ($op == 'save') {
            //Se o administrador não tiver permissão para criar novos administradores redireciona para a dashboard
            if (adminPermissions($con, "adm_005", "inserir") == 0) {
                header('Location: dashboard.php');
                exit();
            }

            // Diretório onde os arquivos serão armazenados
            $uploadDir = './images/uploads/';
            $publicDir = './images/uploads/'; // Caminho acessível pelo navegador

            // Verifica se o campo 'photo' está definido no array $_FILES
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = './images/uploads/';
                $publicDir = './images/uploads/';

                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['photo']['tmp_name'];
                    $fileName = $_FILES['photo']['name'];

                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    
                    $uniqueFileName = uniqid('photo_', true) . '.' . $extension;
                    $destinationPath = $uploadDir . $uniqueFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                        $thumbWidth = 500;
                        $thumbHeight = 500;
                        createThumbnail($destinationPath, $destinationPath, $thumbWidth, $thumbHeight);

                        $fileUrl = $publicDir . $uniqueFileName;
                        echo "Upload realizado com sucesso! Link: <a href=\"$fileUrl\">$fileUrl</a>";
                    } else {
                        echo "Erro ao mover o arquivo para o diretório final.";
                    }
                } else {
                    echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
                }
            } else {
                $fileUrl = "";
                echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
            }

            //Obtem os dados inseridos via POST
            $nome = $_POST['name'];
            $email = $_POST['email'];
            $user = $_POST['user'];
            $birthday = $_POST['birthday'];
            $password = $_POST['password'];
            $confirmpassword = $_POST['passwordConfirm'];
            $status = $_POST['status'];
        
            //Se os campos não tiverem vazios insere o administrador
            if (!empty($nome) && !empty($email) && !empty($user) && !empty($password)&& !empty($confirmpassword)) {
                $birthday2 = date('Y-m-d', strtotime($birthday));
                //obtem a incripetação da password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                //Insere o administrador
                $query = "INSERT INTO administrator (name, email, user, pass, birthday, img, active) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $result = $con->prepare($query);
                
                if ($result) {
                    $result->bind_param("ssssssi", $nome, $email, $user, $passwordHash, $birthday2, $fileUrl, $status);
                    $result->execute();
                    $idAdmin = $con->insert_id;

                    //funcao log
                    $username = $_SESSION['name'];
                    $mensagem = "Administrador '$nome' (ID: $idAdmin) criado pelo administrador de ID $username.";
                    registrar_log($mensagem);
                }
            }

            //Percorre todos os modulos
            for ($i=1; $i <= $numModules; $i++) { 
                $sql = "SELECT id FROM modules WHERE id = $i;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $idModule =  $row['id'];
                }
                //Se o modulo for válido
                if ($i == $idModule) {
                    //Se tiverem selecionado a permissão ver altera a variavel permVer de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_ver']) && $_POST['modulo_' . $i . '_perm_ver'] == "on") {
                        $permVer = 1;
                    } else {
                        $permVer = 0;
                    }
                    
                    //Se tiverem selecionado a permissão edit altera a variavel permEdit de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_edit']) && $_POST['modulo_' . $i . '_perm_edit'] == "on") {
                        $permEdit = 1;
                    } else {
                        $permEdit = 0;
                    }

                    //Se tiverem selecionado a permissão criar altera a variavel permCriar de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_criar']) && $_POST['modulo_' . $i . '_perm_criar'] == "on") {
                        $permCriar = 1;
                    } else {
                        $permCriar = 0;
                    }
                    
                    //Se tiverem selecionado a permissão apagar altera a variavel permApagar de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_apagar']) && $_POST['modulo_' . $i . '_perm-apagar'] == "on") {
                        $permApagar = 1;
                    } else {
                        $permApagar = 0;
                    }
                    //Insere as permissões do administrador
                    $sql = "INSERT INTO administrator_modules (idAdministrator, idModule, pView, pInsert, pUpdate, pDelete) VALUES (?, ?, ?, ?, ?, ?)";
                    $result = $con->prepare($sql);

                    if ($result) {
                        $result->bind_param("iissss", $idAdmin, $i, $permVer, $permEdit, $permCriar, $permApagar);
                    }

                    $result->execute();
                }
            }
            //Após tudo ser concluido redireciona para a página dos administradores
            header('Location: admin.php');
        }
        //Se a operação for edit
        elseif ($op == 'edit') {
            //Se o administrador não tiver permissões de editar um administrador então redireciona para a dashboard
            if (adminPermissions($con, "adm_005", "update") == 0) {
                header('Location: dashboard.php');
                exit();
            }

            //Obtem o id do admin que foi editado via GET
            $idAdmin = $_GET['idAdmin'];
            
            //Se houver um administrador com o id que recebeu via GET obtem a imagem dele para a eliminar caso tenha sido alterada senão redireciona para a dashboard
            $sql = "SELECT * FROM administrator WHERE id = '$idAdmin'";
            $result = $con->query($sql);
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }
            else {
                $row = $result->fetch_assoc();
                $lastPhoto = $row['img'];
            }

            $uploadDir = './images/uploads/';
            $publicDir = './images/uploads/';

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['photo']['tmp_name'];
                $fileName = $_FILES['photo']['name'];

                // Exclui a foto anterior se existir
                if (!empty($lastPhoto)) {
                    $lastPhotoPath = str_replace($publicDir, $uploadDir, $lastPhoto);
                    if (file_exists($lastPhotoPath)) {
                        unlink($lastPhotoPath);
                    }
                }

                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                
                $uniqueFileName = uniqid('photo_', true) . '.' . $extension;
                $destinationPath = $uploadDir . $uniqueFileName;
                
                if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                    $thumbWidth = 500;
                    $thumbHeight = 500;
                    createThumbnail($destinationPath, $destinationPath, $thumbWidth, $thumbHeight);

                    $fileUrl = $publicDir . $uniqueFileName;
                    echo "Upload realizado com sucesso! Link: <a href=\"$fileUrl\">$fileUrl</a>";
                } else {
                    echo "Erro ao mover o arquivo para o diretório final.";
                }
            } else {
                echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
            }

            //Obtem os dados inseridos via POST
            $nome = $_POST['name'];
            $email = $_POST['email'];
            $user = $_POST['user'];
            $password = $_POST['password'];
            $confirmpassword = $_POST['passwordConfirm'];
            $status = $_POST['status'];

            //Se a password e a imagem não tiverem vazios então insere altera tudo 
            if (!empty($password) && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE administrator SET name = '$nome', email = '$email', user = '$user', pass = '$passwordHash', active = '$status', img = '$fileUrl' WHERE id = $idAdmin;";
                $result = $con->prepare($sql);
                $result->execute();
            }
            //senão altera tudo menos a password e a imagem
            else {
                $sql = "UPDATE administrator SET name = '$nome', email = '$email', user = '$user', active = '$status' WHERE id = $idAdmin;";
                $result = $con->prepare($sql);
                $result->execute();
            }
            
            //funcao log
            $username = $_SESSION['name'];
            $mensagem = "Administrador '$nome' (ID: $idAdmin) editado pelo administrador de ID $username.";
            registrar_log($mensagem);

            //Percorre todos os modulos
            for ($i=1; $i <= $numModules; $i++) { 
                $sql = "SELECT id FROM modules WHERE id = $i;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $idModule =  $row['id'];
                }
                //Se o modulo for válido
                if ($i == $idModule) {
                    //Se tiverem selecionado a permissão ver altera a variavel permVer de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_ver']) && $_POST['modulo_' . $i . '_perm_ver'] == "on") {
                        $permVer = 1;
                    } else {
                        $permVer = 0;
                    }
                    
                    //Se tiverem selecionado a permissão edit altera a variavel permEdit de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_edit']) && $_POST['modulo_' . $i . '_perm_edit'] == "on") {
                        $permEdit = 1;
                    } else {
                        $permEdit = 0;
                    }

                    //Se tiverem selecionado a permissão criar altera a variavel permCriar de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_criar']) && $_POST['modulo_' . $i . '_perm_criar'] == "on") {
                        $permCriar = 1;
                    } else {
                        $permCriar = 0;
                    }
                    
                    //Se tiverem selecionado a permissão apagar altera a variavel permApagar de acordo com o que foi selecionado
                    if (isset($_POST['modulo_' . $i . '_perm_apagar']) && $_POST['modulo_' . $i . '_perm_apagar'] == "on") {
                        $permApagar = 1;
                    } else {
                        $permApagar = 0;
                    }

                    //seleciona as permissões do administrador
                    $sql = "SELECT * FROM administrator_modules WHERE idAdministrator = '$idAdmin' AND idModule = $i";
                    $result = $con->query($sql);
                    //se o administrador não tiver permissões definidas então insere
                    if ($result->num_rows <= 0) {
                        $sql = "INSERT INTO administrator_modules (idAdministrator, idModule, pView, pInsert, pUpdate, pDelete) VALUES (?, ?, ?, ?, ?, ?)";
                        $result = $con->prepare($sql);
                        if ($result) {
                            $result->bind_param("iissss", $idAdmin, $i, $permVer, $permEdit, $permCriar, $permApagar);
                        }
                        $result->execute();
                    }

                    //altera as permissões do administrador
                    $sql = "UPDATE administrator_modules SET pView = $permVer, pInsert = $permCriar, pUpdate = $permEdit , pDelete = $permApagar WHERE idAdministrator = $idAdmin AND idModule = $i";
                    $result = $con->prepare($sql);
                    $result->execute();
                }
            }
            //redireciona para a pagina de edit do administrador que acabou de alterar
            header('Location: adminEdit.php?idAdmin=' . $idAdmin);
        }
        else {
            header('Location: dashboard.php');
            exit();
        }
    }
    else {
        header('Location: dashboard.php');
        exit();
    }
?>
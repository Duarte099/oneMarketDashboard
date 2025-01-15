<?php
    session_start();

    include('../db/conexao.php'); 

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    if (adminPermissions("adm_005", "inserir") == 0 || adminPermissions("adm_005", "update") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $op = $_GET['op'];

    $sql = "SELECT MAX(id) AS numModules FROM modules;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numModules = $row['numModules'];
    }

    if ($op == 'save') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['name']);
            $email = trim($_POST['email']);
            $user = trim($_POST['user']);
            $birthday = $_POST['birthday']; 
            $password = trim($_POST['password']);
            $confirmpassword = trim($_POST['passwordConfirm']);
            $status = intval($_POST['status']);
        
            if ($password !== $confirmpassword) {
                echo "<script>alert('As passwords não coincidem!');</script>";
            } elseif (!empty($nome) && !empty($email) && !empty($user) && !empty($password)&& !empty($confirmpassword) && !empty($birthday)) {
                $birthday2 = date('Y-m-d', strtotime($birthday));
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO administrator (name, email, user, pass, birthday, active) VALUES (?, ?, ?, ?, ?, ?)";
                $result = $con->prepare($query);
                
                if ($result) {
                    $result->bind_param("sssssi", $nome, $email, $user, $passwordHash, $birthday2, $status);
                    $result->execute();
                    $idAdmin = $con->insert_id;


                    //funcao log
                    $username = $_SESSION['name'];
                    $mensagem = "Administrador '$nome' (ID: $idAdmin) criado pelo administrador de ID $username.";
                    registrar_log($mensagem);
                }
            }

            for ($i=1; $i <= $numModules; $i++) { 
                $sql = "SELECT id FROM modules WHERE id = $i;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $idModule =  $row['id'];
                }
                if ($i == $idModule) {
                    if (isset($_POST['modulo_' . $i . '_perm_ver']) && $_POST['modulo_' . $i . '_perm_ver'] == "on") {
                        $permVer = 1;
                    } else {
                        $permVer = 0;
                    }
                    
                    if (isset($_POST['modulo_' . $i . '_perm_edit']) && $_POST['modulo_' . $i . '_perm_edit'] == "on") {
                        $permEdit = 1;
                    } else {
                        $permEdit = 0;
                    }

                    if (isset($_POST['modulo_' . $i . '_perm_criar']) && $_POST['modulo_' . $i . '_perm_criar'] == "on") {
                        $permCriar = 1;
                    } else {
                        $permCriar = 0;
                    }
                    
                    if (isset($_POST['modulo_' . $i . '_perm_apagar']) && $_POST['modulo_' . $i . '_perm-apagar'] == "on") {
                        $permApagar = 1;
                    } else {
                        $permApagar = 0;
                    }
                    $sql = "INSERT INTO administrator_modules (idAdministrator, idModule, pView, pInsert, pUpdate, pDelete) VALUES (?, ?, ?, ?, ?, ?)";
                    $result = $con->prepare($sql);

                    if ($result) {
                        $result->bind_param("iissss", $idAdmin, $i, $permVer, $permEdit, $permCriar, $permApagar);
                    }

                    $result->execute();
                }
            }
        }
    }
    elseif ($op == 'edit') {
        $idAdmin = $_GET['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['name']);
            $email = trim($_POST['email']);
            $user = trim($_POST['user']);
            $password = trim($_POST['password']);
            $confirmpassword = trim($_POST['passwordConfirm']);
            $status = intval($_POST['status']);
        
            if ($password !== $confirmpassword) {
                echo "<script>alert('As passwords não coincidem!');</script>";
            } elseif (!empty($nome) && !empty($email) && !empty($user) && !empty($password)&& !empty($confirmpassword)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE administrator SET name = $nome, email = $email, user = $user, pass = $passwordHash, active = $status";
                $result = $con->prepare($sql);
                $result->execute();

                //funcao log
                $username = $_SESSION['name'];
                $mensagem = "Administrador '$nome' (ID: $idAdmin) editado pelo administrador de ID $username.";
                registrar_log($mensagem);
            }

            for ($i=1; $i <= $numModules; $i++) { 
                $sql = "SELECT id FROM modules WHERE id = $i;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $idModule =  $row['id'];
                }
                if ($i == $idModule) {
                    if (isset($_POST['modulo_' . $i . '_perm_ver']) && $_POST['modulo_' . $i . '_perm_ver'] == "on") {
                        $permVer = 1;
                    } else {
                        $permVer = 0;
                    }
                    
                    if (isset($_POST['modulo_' . $i . '_perm_edit']) && $_POST['modulo_' . $i . '_perm_edit'] == "on") {
                        $permEdit = 1;
                    } else {
                        $permEdit = 0;
                    }

                    if (isset($_POST['modulo_' . $i . '_perm_criar']) && $_POST['modulo_' . $i . '_perm_criar'] == "on") {
                        $permCriar = 1;
                    } else {
                        $permCriar = 0;
                    }
                    
                    if (isset($_POST['modulo_' . $i . '_perm_apagar']) && $_POST['modulo_' . $i . '_perm_apagar'] == "on") {
                        $permApagar = 1;
                    } else {
                        $permApagar = 0;
                    }

                    $sql = "UPDATE administrator_modules SET pView = $permVer, pInsert = $permCriar, pUpdate = $permEdit , pDelete = $permApagar WHERE idAdministrator = $idAdmin AND idModule = $i";
                    $result = $con->prepare($sql);
                    $result->execute();
                }
            }
        }
    }
    header('Location: ../pages/admin.php');
?>
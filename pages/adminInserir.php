<?php
    include('../db/conexao.php'); 

    session_start();

    $op = $_GET['op'];

    if ($op == 'save') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = trim($_POST['nome']);
            $email = trim($_POST['email']);
            $user = trim($_POST['user']);
            $password = trim($_POST['password']);
            $confirmpassword = trim($_POST['passwordConfirm']);
            $status = intval($_POST['status']);
        
            if ($password !== $confirmpassword) {
                echo "<script>alert('As passwords não coincidem!');</script>";
            } elseif (!empty($nome) && !empty($email) && !empty($user) && !empty($password)&& !empty($confirmpassword)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO administrator (name, email, user, pass, active) VALUES (?, ?, ?, ?, ?)";
                $stmt = $con->prepare($query);
    
                if ($stmt) {
                    $stmt->bind_param("ssssi", $nome, $email, $user, $passwordHash, $status);
    
                    if ($stmt->execute()) {
                        header('Location: ../pages/admin.php');
                        exit();
                    } else {
                        echo "<script>alert('Erro ao adicionar administrador.');</script>";
                    }
                }
            }
        }
    }
    elseif ($op == 'edit') {
        // formulario para editar
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Acrescentar foto
            $nome = isset($_POST['nome']) ? trim($_POST['nome']) : $admin['name'];
            $email = isset($_POST['email']) ? trim($_POST['email']) : $admin['email'];
            $user = isset($_POST['user']) ? trim($_POST['user']) : $admin['user'];
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $status = isset($_POST['status']) ? intval($_POST['status']) : $admin['active'];
            $birthday = isset($_POST['birthday']) ? $_POST['birthday'] : $admin['birthday'];

            // Se não mudar a pass deixar em branco e assim continua a mesma
            if (!empty($password)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            }

            // Atualiza os dados na base de dados
            $updateQuery = "UPDATE administrator SET name = ?, email = ?, user = ?, pass = ?, active = ?, birthday = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("ssssisi", $nome, $email, $user, $passwordHash, $status, $birthday, $id);

            if ($stmt->execute()) {
                // header('Location: admin.php');  // Quando acabar, manda de volta para a página dos administradores
                // exit();
            } else {
                $error = "Erro ao atualizar administrador.";
            }
        }
    }
?>
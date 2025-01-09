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
        // Verificar se o formulário foi enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include("../db/conexao.php");
    
            // Obter o ID do administrador
            if (isset($_GET['idAdmin'])) {
                $id = intval($_GET['idAdmin']);
            } else {
                $error = "ID do administrador não fornecido.";
            }
    
            $nome = trim($_POST['nome']);
            $email = trim($_POST['email']);
            $user = trim($_POST['user']);
            $birthday = trim($_POST['birthday']);
            $status = intval($_POST['status']);
    
            $password = trim($_POST['password']);
            $passwordConfirm = trim($_POST['passwordConfirm']);
    
            // Verificar se as senhas foram preenchidas e correspondem
            if (!empty($password) || !empty($passwordConfirm)) {
                if ($password !== $passwordConfirm) {
                    $error = "As senhas não correspondem!";
                } else {
                    // Hash da nova senha
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                }
            }
    
            if (!isset($error)) {
                // Atualizar os dados do administrador
                $query = "UPDATE administrator SET name = ?, email = ?, user = ?, birthday = ?, active = ?";
                if (!empty($password)) {
                    $query .= ", pass = ?"; // Adicionar a senha se ela for fornecida
                }
                $query .= " WHERE id = ?";
                $stmt = $con->prepare($query);
    
                if (!empty($password)) {
                    $stmt->bind_param("ssssisi", $nome, $email, $user, $birthday, $status, $passwordHash, $id);
                } else {
                    $stmt->bind_param("ssssi", $nome, $email, $user, $birthday, $status, $id);
                }
    
                if ($stmt->execute()) {
                    header('Location: admin.php');
                    exit();
                } else {
                    $error = "Erro ao atualizar administrador!";
                }
            }
        }
    
        // Exibir mensagens de erro, se houver
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
?>
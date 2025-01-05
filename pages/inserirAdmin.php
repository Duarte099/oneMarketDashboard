<?php
    include('../db/conexao.php'); 

    session_start();

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
?>
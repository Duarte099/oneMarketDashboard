<?php

    session_start();

    include('../db/conexao.php'); 
    $estouEm = 6;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/novoCliente.css">
    <title>OneMarket | Novo Administrador</title>
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
            <?php
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
                                header('Location: admin.php');
                                exit();
                            } else {
                                echo "<script>alert('Erro ao adicionar administrador.');</script>";
                            }
                        }
                    }
                }
            ?>
            <div class="bottom-data">
                <div class="administrator">
                    <form method="POST" action="">
                    <section>
                        <h2>Dados do Administrador</h2>
                        <div class="section-row">
                            <div class="section-group">
                                <label>Nome:</label>
                                <input type="text" name="nome" required>
                            </div>
                            <div class="section-group">
                                <label>Email:</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="section-group">
                                <label>User:</label>
                                <input type="text" name="user" required>
                            </div>

                        </div>
                        <div class="section-row">
                            <div class="section-group">
                                <label>Password:</label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="section-group">
                                <label>Confirmar Password:</label>
                                <input type="password" name="passwordConfirm" required>
                            </div>    
                            <div class="section-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="1">Ativo</option>
                                <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" onclick="return validarPass()">Adicionar Administrador</button>
                    </section>
                </div>
            </div>
        </main>

        <script>
            function validarPass() {
                const pass = document.querySelector('input[name="password"]');
                const passC = document.querySelector('input[name="passwordConfirm"]');

                // Verifica se as senhas são diferentes
                if (pass.value !== passC.value) {
                    passC.setCustomValidity("As palavras-passe não coincidem!");
                    passC.reportValidity(); // Força a exibição da mensagem de erro
                    return false; // Bloqueia o envio do formulário
                } else {
                    passC.setCustomValidity(""); // Remove a mensagem de erro
                    return true; // Permite o envio do formulário
                }
            }

        </script>

    </div>
</body>

</html>
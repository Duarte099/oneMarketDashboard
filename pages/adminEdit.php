<?php
    session_start();
    include('../db/conexao.php');
    $estouEm = 6;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    // Verificar se o id está na URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        header('Location: admin.php');  // Caso não esteja na url volta para a página dos administradores
        exit();
    }   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/editadmin.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Editar Administrador</title>
</head>
<body>

    <?php include('../pages/sideBar.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <?php include('../pages/header.php'); 
            // Buscar o id selecionado antes
            $query = "SELECT * FROM administrator WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Se não encontrar o administrador
            if ($result->num_rows === 0) {
                header('Location: admin.php');  // Manda para a página de administradores
                exit();
            }

            // Pega as informações do admin
            $admin = $result->fetch_assoc();
        ?>          

        <main>
            <div class="header">
                <div class="left">
                    <h1>Editar Administrador</h1>
                </div>
            </div>

            <div class="bottom-data">
                <div class="administrator">
                    <form method="POST" action="adminInserir.php?idAdmin=<?= $id ?>&op=edit">
                        <section>
                        <div class="column-left">
                                <label for="photo">Foto do Administrador:</label>
                                <img src="<?php echo $img; ?>" alt="Admin Picture" id="adminPic">
                                <input type="file" name="photo" id="photo" oninput="displayProfilePic()">
                        </div>
                        <div class="column-right">
                                <label>Nome:</label>
                                <input type="text" name="nome" value="<?php echo htmlspecialchars($admin['name']); ?>" required>

                                <label>Email:</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>

                                <label>User:</label>
                                <input type="text" name="user" value="<?php echo htmlspecialchars($admin['user']); ?>" required>

                                <label>Password (Deixar em branco para deixar a pass atual):</label>
                                <input type="password" name="password">

                                <label>Confirmar Password(apenas se mudar):</label>
                                <input type="password" name="passwordConfirm">

                                <label>Data-Nascimento:</label>
                                <input type="date" name="birthday"value="<?php echo htmlspecialchars($admin['birthday']); ?>">

                                <label>Status:</label>
                                    <select name="status">
                                        <option value="1" <?php echo $admin['active'] == 1 ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="0" <?php echo $admin['active'] == 0 ? 'selected' : ''; ?>>Inativo</option>
                                    </select>

                                <button type="submit">Atualizar Administrador</button>
                        </div>   
                        </section>
                    </form>

                    <?php
                        if (isset($error)) {
                            echo "<p style='color: red;'>$error</p>";
                        }
                    ?>
                </div>
            </div>
        </main>
        
        <script>
        </script>
    </div>
</body>
</html>

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

    // formulario para editar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se o formulário é para editar ou remover
        if (isset($_POST['delete'])) {
            // Apagar o admin
            $deleteQuery = "DELETE FROM administrator WHERE id = ?";
            $stmt = $con->prepare($deleteQuery);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                header('Location: admin.php');  // Redireciona após a exclusão
                exit();
            } else {
                $error = "Erro ao remover administrador.";
            }
        } else {
            // Caso o formulário seja para editar
            $nome = isset($_POST['nome']) ? trim($_POST['nome']) : $admin['name'];
            $email = isset($_POST['email']) ? trim($_POST['email']) : $admin['email'];
            $user = isset($_POST['user']) ? trim($_POST['user']) : $admin['user'];
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $status = isset($_POST['status']) ? intval($_POST['status']) : $admin['active'];

            // Se não mudar a pass deixar em branco e assim continua a mesma
            if (!empty($password)) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            } else {
                $passwordHash = $admin['pass'];  // Pass antiga
            }

            // Atualiza os dados na base de dados
            $updateQuery = "UPDATE administrator SET name = ?, email = ?, user = ?, pass = ?, active = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("ssssii", $nome, $email, $user, $passwordHash, $status, $id);

            if ($stmt->execute()) {
                header('Location: admin.php');  // Quando acabar, manda de volta para a página dos administradores
                exit();
            } else {
                $error = "Erro ao atualizar administrador.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/novoCliente.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Editar Administrador</title>
</head>
<body>

    <?php include('../pages/sideBar.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <?php include('../pages/header.php'); ?>          

        <main>
            <div class="header">
                <div class="left">
                    <h1>Editar Administrador</h1>
                </div>
            </div>

            <div class="bottom-data">
                <div class="administrator">
                    <form method="POST" action="">
                        <section>
                            <h2>Dados do Administrador</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Nome:</label>
                                    <input type="text" name="nome" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                </div>
                                <div class="section-group">
                                    <label>Email:</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                                <div class="section-group">
                                    <label>User:</label>
                                    <input type="text" name="user" value="<?php echo htmlspecialchars($admin['user']); ?>" required>
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Password (Deixar em branco para deixar a pass atual):</label>
                                    <input type="password" name="password">
                                </div>
                                <div class="section-group">
                                    <label>Status:</label>
                                    <select name="status">
                                        <option value="1" <?php echo $admin['active'] == 1 ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="0" <?php echo $admin['active'] == 0 ? 'selected' : ''; ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit">Atualizar Administrador</button>
                            <button type="button" id="deleteButton">Remover Administrador</button>
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
            document.getElementById('deleteButton').addEventListener('click', function() {
                if (confirm("Tem certeza que deseja remover este administrador?")) {
                    // Cria um formulário para submeter o pedido de remoção
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'delete';
                    form.appendChild(input);

                    // Submete o formulário
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        </script>
    </div>
</body>
</html>

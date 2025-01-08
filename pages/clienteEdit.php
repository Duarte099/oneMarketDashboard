<?php
    session_start();

    $estouEm = 5;


    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    // Verificar se o id está na URL
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        header('Location: cliente.php');  // Caso não esteja na url volta para a página dos clientes
        exit();
    }   

    // Buscar o id selecionado antes
    $query = "SELECT * FROM client WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se não encontrar o cliente
    if ($result->num_rows === 0) {
        header('Location: admin.php');  // Manda para a página de administradores
        exit();
    }

    // Pega as informações do cliente
    $client = $result->fetch_assoc();

    // formulario para editar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verifica se o formulário é para editar ou remover
        if (isset($_POST['delete'])) {
            // Apagar o admin
            $deleteQuery = "DELETE FROM client WHERE id = ?";
            $stmt = $con->prepare($deleteQuery);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                header('Location: cliente.php');  // Redireciona após a exclusão
                exit();
            } else {
                $error = "Erro ao remover cliente.";
            }
        } else {
            // Caso o formulário seja para editar
            $nome = isset($_POST['nome']) ? trim($_POST['nome']) : $client['name'];
            $email = isset($_POST['email']) ? trim($_POST['email']) : $client['email'];
            $contact = isset($_POST['contact']) ? trim($_POST['contact']) : $client['contact'];
            $nif = isset($_POST['nif']) ? trim($_POST['nif']) : $client['nif'];
            $status = isset($_POST['status']) ? intval($_POST['status']) : $client['active'];

            // Atualiza os dados na base de dados
            $updateQuery = "UPDATE client SET name = ?, email = ?, contact = ?, nif = ?, active = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("ssssii", $nome, $email, $contact, $nif, $status, $id);

            if ($stmt->execute()) {
                header('Location: cliente.php');  // Quando acabar, manda de volta para a página dos clientes
                exit();
            } else {
                $error = "Erro ao atualizar o cliente.";
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
    <title>OneMarket | Editar Cliente</title>
</head>
<body>

    <?php include('../pages/sideBar.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <?php include('../pages/header.php'); ?>          

        <main>
            <div class="header">
                <div class="left">
                    <h1>Editar Cliente</h1>
                </div>
            </div>

            <div class="bottom-data">
                <div class="administrator">
                    <form method="POST" action="">
                        <section>
                            <h2>Dados do Cliente</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Nome:</label>
                                    <input type="text" name="nome" value="<?php echo htmlspecialchars($client['name']); ?>" required>
                                </div>
                                <div class="section-group">
                                    <label>Email:</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required>
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="text" name="contact" value="<?php echo htmlspecialchars($client['contact']); ?>" required>
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>NIF:</label>
                                    <input type="text" name="nif" value="<?php echo htmlspecialchars($client['nif']); ?>" required>
                                </div>
                                <div class="section-group">
                                    <label>Status:</label>
                                    <select name="status">
                                        <option value="1" <?php echo $client['active'] == 1 ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="0" <?php echo $client['active'] == 0 ? 'selected' : ''; ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit">Atualizar Cliente</button>
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

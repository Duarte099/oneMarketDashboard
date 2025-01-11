<?php 
    session_start();

    $estouEm = 4;

    include('../db/conexao.php');

    $permission = adminPermissions("adm_003", "update");

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $permission == 0) {
        header('Location: index.php');
        exit();
    }


    // Verificar se o id está na URL
    if (isset($_GET['idProduct'])) {
        $id = intval($_GET['idProduct']);
    } else {
        header('Location: produto.php');  // Caso não esteja na url volta para a página dos produtos
        exit();
    } 


    // Buscar o id selecionado antes
    $query = "SELECT * FROM product WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se não encontrar o produto
    if ($result->num_rows === 0) {
        header('Location: produto.php');  // Manda para a página dos produtos
        exit();
    }

    // Pega as informações do produto
    $product = $result->fetch_assoc();

    // Buscar o id da tabela product_stock selecionado antes
    $queryStock = "SELECT quantity FROM product_stock WHERE idProduct = ?";
    $stmtStock = $con->prepare($queryStock);
    $stmtStock->bind_param("i", $id);
    $stmtStock->execute();
    $resultStock = $stmtStock->get_result();


    // Se não encontrar nada, define um valor por padrao
    if ($resultStock->num_rows === 0) {
        $product_stock = ['quantity' => 0]; // valor definido caso não encontre
    } else {
        $product_stock = $resultStock->fetch_assoc();
    }

    
    // formulario para editar
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Caso o formulário seja para editar
        $img = isset($_POST['photo']) ? trim($_POST['photo']) : $product['img'];
        $name = isset($_POST['name']) ? trim($_POST['name']) : $product['name'];
        $ref = isset($_POST['ref']) ? trim($_POST['ref']) : $product['reference'];
        $value = isset($_POST['value']) ? trim($_POST['value']) : $product['value'];
        $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : $product_stock['quantity'];
        $status = isset($_POST['status']) ? intval($_POST['status']) : $product['active'];

        

        // Atualiza os dados na base de dados
        $updateQuery = "UPDATE product SET img = ?, name = ?, reference = ?, value = ?, active = ? WHERE id = ?";
        $stmt = $con->prepare($updateQuery);
        $stmt->bind_param("ssssii", $img, $name, $ref, $value, $status, $id);

        // Atualiza os dados na tabela product_stock
        $updateStockQuery = "UPDATE product_stock SET quantity = ? WHERE idProduct = ?";
        $stmtStockUpdate = $con->prepare($updateStockQuery);
        $stmtStockUpdate->bind_param("ii", $quantity, $id);


        if ($stmt->execute()) {
            if($stmtStockUpdate->execute()){
                header('Location: produto.php');  // Quando acabar, manda de volta para a página dos produtos
                exit();
            }
            } else {
                $error = "Erro ao atualizar o produto.";
            }
        }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Editar Produto</title>
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
                    <h1>Editar Produto</h1>
                </div>
            </div>
            <div class="form-container">
                <form method="POST" action="" id="profileForm" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto do produto:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('<?php echo $img; ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="../images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                        </div>
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $product['name']; ?>">

                        <label for="ref">Referencia:</label>
                        <input type="text" name="ref" id="ref" value="<?php echo $product['reference']; ?>">

                        <label for="value">Valor:</label>
                        <input type="text" name="value" id="value" value="<?php echo str_replace('.', '.', $product['value']); ?>" />

                        <label for="quantity">Stock:</label>
                        <input type="int" name="quantity" id="quantity" value="<?php echo intval($product_stock['quantity']); ?>">
                        
                        <label>Status:</label>
                            <select name="status">
                                <option value="1" <?php echo $product['active'] == 1 ? 'selected' : ''; ?>>Ativo</option>
                                <option value="0" <?php echo $product['active'] == 0 ? 'selected' : ''; ?>>Inativo</option>
                            </select>

                        <button type="submit">Guardar alterações</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../index.js"></script>
    <script>
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

        const form = document.getElementById('profileForm');
            form.addEventListener('submit', (event) => {
            const valueInput = document.getElementById('value');
            valueInput.value = valueInput.value.replace(',', '.'); // Substitui vírgula por ponto
        });
    </script>


</body>
</html>
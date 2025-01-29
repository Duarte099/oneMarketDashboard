<?php 
    session_start();

    $estouEm = 4;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php');

    if (adminPermissions("adm_003", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idProduct = $_GET['idProduct'];
    $sql = "SELECT * FROM product WHERE id = '$idProduct'";
    $result = $con->query($sql);
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    }
    else {
        $id = $_GET['idProduct'];
    }

    // Buscar o id selecionado antes
    $query = "SELECT * FROM product WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

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
        // Diretório onde os arquivos serão armazenados
        $uploadDir = '../images/uploads/';
        $publicDir = '/PAP/images/uploads/'; // Caminho acessível pelo navegador

        // Verifica se o campo 'photo' está definido no array $_FILES
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Recupera informações do arquivo enviado        
            $fileTmpPath = $_FILES['photo']['tmp_name']; // Caminho temporário
            $fileName = $_FILES['photo']['name'];       // Nome original do arquivo

            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (true) { // testar se a extensão faz parte das extensoes permitidas
                // gif / jpg / jpeg / png
                // verificar o tamanho da iamgem .. pode ser verificado em javascript // size of document
                // Gera um nome único para evitar sobrescrita de arquivos
                $uniqueFileName = uniqid('photo_', true) . '.' . $extension;
                $uniqueFileName_mini = "mini_" . $uniqueFileName;

                // Define o caminho completo para o upload
                $destinationPath = $uploadDir . $uniqueFileName_mini;
                $destinationPathmINI = $uploadDir . $uniqueFileName;

                // Move o arquivo para o diretório final
                if (move_uploaded_file($fileTmpPath, $destinationPath)) {

                    // GERAR O THUMBNAIL
                    $thumbWidth = 500; // Largura desejada
                    $thumbHeight = 500; // Altura desejada

                    createThumbnail($destinationPath, $destinationPathmINI, $thumbWidth, $thumbHeight);


                    // Gera o link público para o arquivo
                    $fileUrl = $publicDir . $uniqueFileName;

                    /** redimensionamento da imagem */

                    echo "Upload realizado com sucesso! Link: <a href=\"$fileUrl\">$fileUrl</a>";
                } else {
                    echo "Erro ao mover o arquivo para o diretório final.";
                }
            }
        } else {
            echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
        }

        // Caso o formulário seja para editar
        $name = isset($_POST['name']) ? trim($_POST['name']) : $product['name'];
        $ref = isset($_POST['ref']) ? trim($_POST['ref']) : $product['reference'];
        $value = isset($_POST['value']) ? trim($_POST['value']) : $product['value'];
        $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : $product_stock['quantity'];
        $status = isset($_POST['status']) ? intval($_POST['status']) : $product['active'];

        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            // Atualiza os dados na base de dados
            $updateQuery = "UPDATE product SET img = ?, name = ?, reference = ?, value = ?, active = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("ssssii", $fileUrl, $name, $ref, $value, $status, $id);

            // Atualiza os dados na tabela product_stock
            $updateStockQuery = "UPDATE product_stock SET quantity = ? WHERE idProduct = ?";
            $stmtStockUpdate = $con->prepare($updateStockQuery);
            $stmtStockUpdate->bind_param("ii", $quantity, $id);
        }
        else {
            // Atualiza os dados na base de dados
            $updateQuery = "UPDATE product SET name = ?, reference = ?, value = ?, active = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("sssii", $name, $ref, $value, $status, $id);

            // Atualiza os dados na tabela product_stock
            $updateStockQuery = "UPDATE product_stock SET quantity = ? WHERE idProduct = ?";
            $stmtStockUpdate = $con->prepare($updateStockQuery);
            $stmtStockUpdate->bind_param("ii", $quantity, $id);
        }


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
                    <h1><?php echo $product['name'] ?></h1>
                </div>
            </div>
            <div class="form-container">
                <form method="POST" action="" id="profileForm" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto do produto:</label>
                        <div id="profilePic" style="width:100%; max-width:500px; background: url('<?php echo $product['img']; ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                            <img src="../images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                        </div>
                        <?php if (adminPermissions("adm_003", "update") == 1) { ?>
                            <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                        <?php } ?>
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $product['name']; ?>" <?php if (adminPermissions("adm_003", "update") == 0) {echo "readonly";}?>>

                        <label for="ref">Referencia:</label>
                        <input type="text" name="ref" id="ref" value="<?php echo $product['reference']; ?>" <?php if (adminPermissions("adm_003", "update") == 0) {echo "readonly";}?>>

                        <label for="value">Valor:</label>
                        <input type="text" name="value" id="value" value="<?php echo str_replace('.', '.', $product['value']); ?>" <?php if (adminPermissions("adm_003", "update") == 0) {echo "readonly";}?>>

                        <label for="quantity">Stock:</label>
                        <input type="int" name="quantity" id="quantity" value="<?php echo intval($product_stock['quantity']); ?>" <?php if (adminPermissions("adm_003", "update") == 0) {echo "readonly";}?>>
                        
                        <label>Status:</label>
                        <?php if (adminPermissions("adm_003", "update") == 0) { ?>
                            <input type="text" name="status" id="status" value="<?php if ($product['active'] == 0) {echo "Inativo";} else {echo "Ativo";}?>" readonly>
                        <?php } else {?>
                            <select name="status">
                                <option value="1" <?php echo $product['active'] == 1 ? 'selected' : ''; ?>>Ativo</option>
                                <option value="0" <?php echo $product['active'] == 0 ? 'selected' : ''; ?>>Inativo</option>
                            </select>
                        <?php } ?>
                        
                        <?php if (adminPermissions("adm_003", "update") == 1) { ?>
                            <button type="submit" style="margin-top: 15px">Guardar alterações</button>
                        <?php } ?>
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
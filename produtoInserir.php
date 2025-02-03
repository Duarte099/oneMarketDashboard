<?php
    include('./db/conexao.php');

    if (isset($_GET['op']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $op = $_GET['op'];
        if ($op == "save") {
            if (adminPermissions($con, "adm_003", "inserir") == 0) {
                header('Location: dashboard.php');
                exit();
            }

            // Diretório onde os arquivos serão armazenados
            $uploadDir = './images/uploads/';
            $publicDir = './images/uploads/'; // Caminho acessível pelo navegador

            // Verifica se o campo 'photo' está definido no array $_FILES
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['photo']['tmp_name'];
                $fileName = $_FILES['photo']['name'];

                $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                $uniqueFileName = uniqid('photo_', true) . '.' . $extension;
                $destinationPath = $uploadDir . $uniqueFileName;

                if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                    $thumbWidth = 500;
                    $thumbHeight = 500;
                    createThumbnail($destinationPath, $destinationPath, $thumbWidth, $thumbHeight);

                    $fileUrl = $publicDir . $uniqueFileName;
                    echo "Upload realizado com sucesso! Link: <a href=\"$fileUrl\">$fileUrl</a>";
                } else {
                    echo "Erro ao mover o arquivo para o diretório final.";
                }
            } else {
                echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
            }

            $imagem = '';
            $nome = $_POST['name'];
            $ref = $_POST['ref'];
            $valor = $_POST['value'];
            $stock = $_POST['quantity'];
            $status = $_POST['status'];

            if (!empty($nome) && !empty($ref) && !empty($valor)) {
                $query = "INSERT INTO product (img, name, reference, value, active) VALUES (?, ?, ?, ?, ?)";
                $stmt = $con->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("sssdi", $imagem, $nome, $ref, $valor, $status);

                    $stmt->execute();
                    $idProduct = $con->insert_id;

                    //funcao log
                    $username = $_SESSION['name'];
                    $mensagem = "Produto '$nome' (ID: $idProduct) criado pelo administrador de ID $username.";
                    registrar_log($mensagem);
                }
                $query = "INSERT INTO product_stock (idProduct, quantity) VALUES (?, ?)";
                $stmt = $con->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("ii", $idProduct, $stock);
                    $stmt->execute();
                }
            }
            header('Location: produto.php');
        }
        if ($op == "edit") {
            if (adminPermissions($con, "adm_003", "update") == 0) {
                header('Location: dashboard.php');
                exit();
            }

            $idProduto = $_GET['idProduct'];

            $sql = "SELECT * FROM product WHERE id = '$idProduto'";
            $result = $con->query($sql);
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }
            // Diretório onde os arquivos serão armazenados
            $uploadDir = './images/uploads/';
            $publicDir = './images/uploads/'; // Caminho acessível pelo navegador

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['photo']['tmp_name'];
                $fileName = $_FILES['photo']['name'];

                // Exclui a foto anterior se existir
                if (!empty($lastPhoto)) {
                    $lastPhotoPath = str_replace($publicDir, $uploadDir, $lastPhoto);
                    if (file_exists($lastPhotoPath)) {
                        unlink($lastPhotoPath);
                    }
                }

                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                
                $uniqueFileName = uniqid('photo_', true) . '.' . $extension;
                $destinationPath = $uploadDir . $uniqueFileName;
                
                if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                    $thumbWidth = 500;
                    $thumbHeight = 500;
                    createThumbnail($destinationPath, $destinationPath, $thumbWidth, $thumbHeight);

                    $fileUrl = $publicDir . $uniqueFileName;
                    echo "Upload realizado com sucesso! Link: <a href=\"$fileUrl\">$fileUrl</a>";
                } else {
                    echo "Erro ao mover o arquivo para o diretório final.";
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
                $stmt->bind_param("ssssii", $fileUrl, $name, $ref, $value, $status, $idProduto);

                // Atualiza os dados na tabela product_stock
                $updateStockQuery = "UPDATE product_stock SET quantity = ? WHERE idProduct = ?";
                $stmtStockUpdate = $con->prepare($updateStockQuery);
                $stmtStockUpdate->bind_param("ii", $quantity, $idProduto);
            } else {
                // Atualiza os dados na base de dados
                $updateQuery = "UPDATE product SET name = ?, reference = ?, value = ?, active = ? WHERE id = ?";
                $stmt = $con->prepare($updateQuery);
                $stmt->bind_param("sssii", $name, $ref, $value, $status, $idProduto);

                // Atualiza os dados na tabela product_stock
                $updateStockQuery = "UPDATE product_stock SET quantity = ? WHERE idProduct = ?";
                $stmtStockUpdate = $con->prepare($updateStockQuery);
                $stmtStockUpdate->bind_param("ii", $quantity, $idProduto);
            }

            if ($stmt->execute()) {
                if ($stmtStockUpdate->execute()) {

                    //funcao log
                    $username = $_SESSION['name'];
                    $mensagem = "Produto '$name'($idProduto) editado pelo administrador $username().";
                    registrar_log($mensagem);
                }
            } else {
                $error = "Erro ao atualizar o produto.";
            }
            header('Location: produtoEdit.php?idProduct=' . $idProduto);
        }
    }
?>
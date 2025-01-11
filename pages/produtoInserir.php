<?php
    session_start();

    include('../db/conexao.php'); 

    $permission = adminPermissions("adm_003", "inserir");

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $permission == 0) {
        header('Location: index.php');
        exit();
    }
    
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
        }
        $query = "INSERT INTO product_stock (idProduct, quantity) VALUES (?, ?)";
        $stmt = $con->prepare($query);
        if ($stmt) {
            $stmt->bind_param("ii", $idProduct, $stock);
    
            if ($stmt->execute()) {
                header('Location: ../pages/produto.php');
                exit();
            }
        }
    }
?>
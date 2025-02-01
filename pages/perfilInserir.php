<?php
    include('../db/conexao.php'); 

    $idAdmin = $_SESSION['id'];

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

        $nome = trim($_POST['name']);
        $email = trim($_POST['email']);
        $user = trim($_POST['user']);
        $birthday = trim($_POST['birthday']);
        $password = trim($_POST['password']);
        $confirmpassword = trim($_POST['passwordConfirm']);

        if ($password == $confirmpassword && !empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE administrator SET name = '$nome', email = '$email', user = '$user', pass = '$passwordHash', birthday = '$birthday' WHERE id = $idAdmin;";
            $result = $con->prepare($sql);
            $result->execute();
        }
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $sql = "UPDATE administrator SET name = '$nome', email = '$email', user = '$user', birthday = '$birthday', img = '$fileUrl' WHERE id = $idAdmin;";
            $result = $con->prepare($sql);
            $result->execute();
        }
        else {
            $sql = "UPDATE administrator SET name = '$nome', email = '$email', user = '$user', birthday = '$birthday' WHERE id = $idAdmin;";
            $result = $con->prepare($sql);
            $result->execute();
        }
    }
    header('Location: ../pages/perfil.php');
?>
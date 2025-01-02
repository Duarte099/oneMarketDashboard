<?php

    session_start();

    include('../db/conexao.php'); 

    // Diretório onde os arquivos serão armazenados
    $uploadDir = '../images/uploads/';
    $publicDir = '/PAP/images/uploads/'; // Caminho acessível pelo navegador

    // Verifica se o campo 'photo' está definido no array $_FILES
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Recupera informações do arquivo enviado
        $fileTmpPath = $_FILES['photo']['tmp_name']; // Caminho temporário
        $fileName = $_FILES['photo']['name'];       // Nome original do arquivo

        // Gera um nome único para evitar sobrescrita de arquivos
        $uniqueFileName = uniqid('photo_', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);

        // Define o caminho completo para o upload
        $destinationPath = $uploadDir . $uniqueFileName;

        // Move o arquivo para o diretório final
        if (move_uploaded_file($fileTmpPath, $destinationPath)) {
            // Gera o link público para o arquivo
            $fileUrl = $publicDir . $uniqueFileName;

            echo "Upload realizado com sucesso! Link: <a href=\"$fileUrl\">$fileUrl</a>";
        } else {
            echo "Erro ao mover o arquivo para o diretório final.";
        }
    } else {
        echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $user = $_POST['user'];
    $birthday = $_POST['birthday'];
    $idAdmin = $_SESSION['id'];

    $sql = "UPDATE `administrator` SET name = '$name', email = '$email', user = '$user', img = '$fileUrl', birthday = '$birthday' WHERE id = $idAdmin";
    $result = $con->prepare($sql);
    $result->execute();

    header('Location: ../pages/perfil.php');
?>
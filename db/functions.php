<?php
    function createThumbnail($sourcePath, $thumbPath, $thumbWidth, $thumbHeight) {
        // Verifica se o arquivo existe
        if (!file_exists($sourcePath)) {
            die("Arquivo de origem não encontrado.");
        }

        // Obtém as dimensões e o tipo da imagem
        list($width, $height, $type) = getimagesize($sourcePath);

        // Cria uma imagem baseada no tipo
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                die("Formato de imagem não suportado.");
        }

        // Calcula proporções para o thumbnail
        $aspectRatio = $width / $height;
        if ($thumbWidth / $thumbHeight > $aspectRatio) {
            $newWidth = $thumbHeight * $aspectRatio;
            $newHeight = $thumbHeight;
        } else {
            $newWidth = $thumbWidth;
            $newHeight = $thumbWidth / $aspectRatio;
        }
        $newWidth = (int)$newWidth;
        $newHeight = (int)$newHeight;
        //echo " $newWidth  // $newHeight :: $width, $height";

        // Cria uma nova imagem para o thumbnail
        $thumbImage = imagecreatetruecolor($newWidth, $newHeight);

        // Copia e redimensiona a imagem original para o thumbnail
        imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Salva o thumbnail no caminho especificado
        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($thumbImage, $thumbPath);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbImage, $thumbPath);
                break;
            case IMAGETYPE_GIF:
                imagegif($thumbImage, $thumbPath);
                break;
        }

        // Libera memória
        imagedestroy($sourceImage);
        imagedestroy($thumbImage);

        echo "Thumbnail criado com sucesso em: $thumbPath";
    }

    function adminPermissions($codModule, $perm){
        include('../db/conexao.php');

        $idAdminPerms = $_SESSION['id'];

        switch ($perm) {
            case "view":
                $sql = "SELECT pView, cod FROM modules INNER JOIN administrator_modules ON modules.id = administrator_modules.idModule WHERE cod = '$codModule' AND idAdministrator = $idAdminPerms;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) { 
                    $row = $result->fetch_assoc();
                    return $row['pView'];
                }
                break;
            case "inserir":
                $sql = "SELECT pInsert, cod FROM modules INNER JOIN administrator_modules ON modules.id = administrator_modules.idModule WHERE cod = '$codModule' AND idAdministrator = $idAdminPerms;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) { 
                    $row = $result->fetch_assoc();
                    return $row['pInsert'];
                }
                break;
            case "update":
                $sql = "SELECT pUpdate, cod FROM modules INNER JOIN administrator_modules ON modules.id = administrator_modules.idModule WHERE cod = '$codModule' AND idAdministrator = $idAdminPerms;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {    
                    $row = $result->fetch_assoc();
                    return $row['pUpdate'];
                }
                break;
            case "delete":
                $sql = "SELECT pDelete, cod FROM modules INNER JOIN administrator_modules ON modules.id = administrator_modules.idModule WHERE cod = '$codModule' AND idAdministrator = $idAdminPerms;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {    
                    $row = $result->fetch_assoc();
                    return $row['pDelete'];
                }
                break;
        }
    }

    function registrar_log($mensagem) {
        include('../db/conexao.php');

        // Inserir na tabela de logs
        $query = "INSERT INTO administrator_logs (idAdministrator, logFile) VALUES (?, ?)";

        $stmt = $con->prepare($query);
        $stmt->bind_param('is', $_SESSION['id'], "$mensagem");

        //returnar true caso guardar, senao retorna false
        return $stmt->execute();
    }
?>
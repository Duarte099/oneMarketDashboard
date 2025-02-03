<?php
    $auxLogin = true;

    //Cria uma imagem mini para usar quando têm de ser carregadas muitas imagens
    function createThumbnail($sourcePath, $thumbPath, $thumbWidth, $thumbHeight) {
        // Verifica se o arquivo de origem existe, se não existir encerra a execução com uma mensagem de erro.
        if (!file_exists($sourcePath)) {
            die("Arquivo de origem não encontrado.");
        }
    
        // Obtém as dimensões e o tipo da imagem de origem
        list($width, $height, $type) = getimagesize($sourcePath);
    
        // Cria a imagem de origem de acordo com o tipo da imagem (JPEG, PNG ou GIF)
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
                // Se o formato não for suportado, interrompe a execução
                die("Formato de imagem não suportado.");
        }
    
        // Calcula a razão de aspecto da imagem original
        $aspectRatio = $width / $height;
        
        // Determina as novas dimensões para o thumbnail mantendo a razão de aspecto
        if ($thumbWidth / $thumbHeight > $aspectRatio) {
            // Se a razão do thumbnail for maior que a da imagem original,
            // ajusta a largura de forma proporcional
            $newWidth = $thumbHeight * $aspectRatio;
            $newHeight = $thumbHeight;
        } else {
            // Caso contrário, ajusta a altura de forma proporcional
            $newWidth = $thumbWidth;
            $newHeight = $thumbWidth / $aspectRatio;
        }
        // Converte as dimensões para inteiros
        $newWidth = (int)$newWidth;
        $newHeight = (int)$newHeight;
    
        // Cria uma nova imagem com as dimensões do thumbnail
        $thumbImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Redimensiona e copia a imagem de origem para a imagem do thumbnail
        imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
        // Salva o thumbnail no caminho especificado, de acordo com o tipo da imagem original
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
    
        // Libera a memória ocupada pela imagem do thumbnail
        imagedestroy($thumbImage);
        
        // Informa que o thumbnail foi criado com sucesso
        echo "Thumbnail criado com sucesso em: $thumbPath";
    }

    function adminPermissions($con, $codModule, $perm){
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
        include('./db/conexao.php');

        // Inserir na tabela de logs
        $query = "INSERT INTO administrator_logs (idAdministrator, logFile) VALUES (?, ?)";

        $stmt = $con->prepare($query);
        $stmt->bind_param('is', $_SESSION['id'], $mensagem);

        //returnar true caso guardar, senao retorna false
        return $stmt->execute();
    }

    // header('Location: index.php');
?>
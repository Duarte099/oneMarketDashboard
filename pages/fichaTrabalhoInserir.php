<?php
    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php');

    if (adminPermissions("adm_002", "inserir") == 0 || adminPermissions("adm_002", "update") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idAdmin = $_SESSION['id'];

    if (isset($_GET['op']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $op = $_GET['op'];
        if ($op == "save") {
            $idBudget = $_GET['idBudget'];
        
            $sql = "SELECT * FROM budget WHERE id = $idBudget;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $idClient =  $row['idClient'];
            }
            else {
                header('Location: ../pages/dashboard.php');
                exit();
            }
        
            $readyStorage = $_POST['prontoArmazem'];
            $joinWork = $_POST['entradaObra'];
            $exitWork = $_POST['saidaObra'];
            $observacaoWorksheet = $_POST['observation'];
        
            $anoAtual = date("Y");
        
            $sql = "SELECT MAX(num) AS maior_numero FROM worksheet WHERE year = $anoAtual;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $proximo_numero = $row['maior_numero'] + 1;
            } else {
                $proximo_numero = 1;
            }
        
            $sql = "INSERT INTO worksheet (idBudget, idClient, num, year, readyStorage, joinWork, exitWork, observation, createdBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $result = $con->prepare($sql);
        
            if ($result) {
                $result->bind_param("iiiissssi", $idBudget, $idClient, $proximo_numero, $anoAtual, $readyStorage, $joinWork, $exitWork, $observacaoWorksheet, $idAdmin);
            }
        
            $result->execute();
            $idWorksheet = $con->insert_id;
        
            $sql = "UPDATE `budget` SET idWorksheet = '$idWorksheet' WHERE id = $idBudget";
            $result = $con->prepare($sql);
            $result->execute();

            //funcao log
            $username = $_SESSION['name'];
            $mensagem = "Ficha de Trabalho '$proximo_numero/$anoAtual' (ID: $idWorksheet) criado pelo administrador de ID $username.";
            registrar_log($mensagem);
        
            //secções e produtos
            $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
            $resultSection = $con->query($sqlSection);
            if ($resultSection->num_rows > 0) {
                while ($rowSection = $resultSection->fetch_assoc()) {
                    $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $rowSection['orderSection']]);
        
                    $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
                    $resultProducts = $con->query($sqlProducts);
                    if ($resultProducts->num_rows > 0) {
                        while ($rowProducts = $resultProducts->fetch_assoc()){
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']] == "on") {
                                $check = 1;
                            } else {
                                $check = 0;
                            }
        
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']] == "on") {
                                $storage = 1;
                            } else {
                                $storage = 0;
                            }
                            $ref = $_POST['secao_' . $rowSection['orderSection'] . '_produto_ref_' . $rowProducts['orderProduct']];
                            $observacao = $_POST['secao_' . $rowSection['orderSection'] . '_produto_observacao_' . $rowProducts['orderProduct']];
                            $tamanho = $_POST['secao_' . $rowSection['orderSection'] . '_produto_tamanho_' . $rowProducts['orderProduct']];
                            if (!empty($descricao) || !empty($tamanho)) {
                                $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND orderSection = {$rowSection['orderSection']} AND orderProduct = '{$rowProducts['orderProduct']}';";
                                $result = $con->prepare($sql);
                                $result->execute();
                            }
                            $sql = "INSERT INTO worksheet_version (idVersion, idWorksheet, readyStorage, joinWork, exitWork, observation, orderSection, orderProduct, checkProduct, storageProduct, observationProduct, sizeProduct) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $result = $con->prepare($sql);
                            if ($result) {
                                $idVersion = 1;
                                $result->bind_param("iissssiissss", $idVersion, $idWorksheet, $readyStorage, $joinWork, $exitWork, $observacaoWorksheet, $rowSection['orderSection'], $rowProducts['orderProduct'], $check, $storage, $observacao, $tamanho);
                            }
                            $result->execute();
                        }
                    }
                }
            }
            header('Location: ../pages/fichaTrabalhoEdit.php?idWorksheet=' . $idWorksheet);
        } elseif ($op == "edit") {
            $readyStorage = $_POST['prontoArmazem'];
            $joinWork = $_POST['entradaObra'];
            $exitWork = $_POST['saidaObra'];
            $observacaoWorksheet = $_POST['observation'];
            $idWorksheet = $_GET['idWorksheet'];
        
            $sql = "SELECT idBudget FROM worksheet WHERE worksheet.id = $idWorksheet;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $idBudget =  $row['idBudget'];
            }
        
            $sql2 = "SELECT MAX(idVersion) AS idVersion FROM worksheet_version WHERE idWorksheet = $idWorksheet;";
            $result2 = $con->query($sql2);
            if ($result2->num_rows > 0) {
                $row = $result2->fetch_assoc();
                $idVersion =  $row['idVersion'] + 1;
            }
        
            //cabeçalho
            $sql = "UPDATE `worksheet` SET readyStorage = '$readyStorage', joinWork = '$joinWork', exitWork = '$exitWork' WHERE id = $idWorksheet";
            $result = $con->prepare($sql);
            $result->execute();

            $sql = "SELECT num, year FROM worksheet WHERE id = $idWorksheet";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $numFicha = $row['num'];
                $yearFicha = $row['year'];
                
                // Concatenar os dois valores com a barra
                $fichaCompleta = $numFicha . "/" . $yearFicha;

                // Criar a mensagem de log
                $username = $_SESSION['name'];
                $mensagem = "Ficha de Trabalho '$fichaCompleta' (ID: $idWorksheet) versao: ($idVersion) editado pelo administrador de ID $username.";
                
                // Registrar o log
                registrar_log($mensagem);
            }
        
            //secções e produtos
            $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
            $resultSection = $con->query($sqlSection);
            if ($resultSection->num_rows > 0) {
                while ($rowSection = $resultSection->fetch_assoc()) {
                    $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $rowSection['orderSection']]);
        
                    $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
                    $resultProducts = $con->query($sqlProducts);
                    if ($resultProducts->num_rows > 0) {
                        while ($rowProducts = $resultProducts->fetch_assoc()) {
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']] == "on") {
                                $check = 1;
                            } else {
                                $check = 0;
                            }
        
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']] == "on") {
                                $storage = 1;
                            } else {
                                $storage = 0;
                            }
                            $ref = $_POST['secao_' . $rowSection['orderSection'] . '_produto_ref_' . $rowProducts['orderProduct']];
                            $observacao = $_POST['secao_' . $rowSection['orderSection'] . '_produto_observacao_' . $rowProducts['orderProduct']];
                            $tamanho = $_POST['secao_' . $rowSection['orderSection'] . '_produto_tamanho_' . $rowProducts['orderProduct']];
                            if (!empty($check) || !empty($storage) || !empty($observacao) || !empty($tamanho)) {
                                $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $idProduct = $row['id'];
                                }
                                $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND idProduct = $idProduct AND orderSection = '{$rowSection['orderSection']}' AND orderProduct = '{$rowProducts['orderProduct']}';";
                                $result = $con->prepare($sql);
                                $result->execute();
        
                                $sql = "INSERT INTO worksheet_version (idVersion, idWorksheet, readyStorage, joinWork, exitWork, observation, orderSection, orderProduct, checkProduct, storageProduct, observationProduct, sizeProduct) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                $result = $con->prepare($sql);
                                if ($result) {
                                    $result->bind_param("iissssiissss", $idVersion, $idWorksheet, $readyStorage, $joinWork, $exitWork, $observacaoWorksheet, $rowSection['orderSection'], $rowProducts['orderProduct'], $check, $storage, $observacao, $tamanho);
                                }
                                $result->execute();
                            }
                        }
                    }
                }
            }
            header('Location: ../pages/fichaTrabalho.php');
        }
        elseif ($op == "editFotos") {
            // Diretório onde os arquivos serão armazenados
            $uploadDir = '../images/uploads/';
            $publicDir = '/PAP/images/uploads/'; // Caminho acessível pelo navegado

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

            

            header('Location: ../pages/fichaTrabalhoGaleria.php');
        }
        else {
            header('Location: ../pages/dashboard.php');
            exit();
        }
    }
    else {
        header('Location: ../pages/dashboard.php');
        exit();
    }
?>
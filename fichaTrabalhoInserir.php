<?php
    //Inclui a conexão à base de dados nesta pagina
    include('./db/conexao.php');

    //Obtem o id do administrador logado
    $idAdmin = $_SESSION['id'];

    //Se a variavel op obtida via GET tiver definida e o metodo de request a esta pagina for POST
    if (isset($_GET['op']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        //Obtem a operação a ser feita
        $op = $_GET['op'];
        if ($op == "save") {
            //Se o administrador não tiver permissões para criar uma nova ficha de trabalho redireciona para a dashboard
            if (adminPermissions($con, "adm_002", "inserir") == 0) {
                header('Location: dashboard.php');
                exit();
            }

            //Obtem o id do budget associado à nova ficha de trabalho via GET
            $idBudget = $_GET['idBudget'];
            
            //Seleciona o orçamento cujo id é igual ao recebido via GET
            $sql = "SELECT * FROM budget WHERE id = $idBudget;";
            $result = $con->query($sql);
            //Caso exista esse orçamento então obtem o id do cliente associado a esse orçamento
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $idClient = $row['idClient'];
            }
            //Caso contrário redireciona para a dashboard
            else {
                header('Location: dashboard.php');
                exit();
            }
            
            //Obtem os dados inseridos no cabeçalho da nova ficha de trabalho 
            $readyStorage = $_POST['prontoArmazem'];
            $joinWork = $_POST['entradaObra'];
            $exitWork = $_POST['saidaObra'];
            $observacaoWorksheet = $_POST['observation'];
            
            //Obtem o ano atual para forma o numero da nova ficha de trabalho
            $anoAtual = date("Y");
            
            //Seleciona o maior numero de todas as fichas de trabalho cujo ano é igual ao atual
            $sql = "SELECT MAX(num) AS maior_numero FROM worksheet WHERE year = $anoAtual;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $proximo_numero = $row['maior_numero'] + 1;
            } else {
                $proximo_numero = 1;
            }
            
            //query sql para inserir o cabeçalho da nova ficha de trabalho
            $sql = "INSERT INTO worksheet (idBudget, idClient, num, year, readyStorage, joinWork, exitWork, observation, createdBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $result = $con->prepare($sql);
            if ($result) {
                $result->bind_param("iiiissssi", $idBudget, $idClient, $proximo_numero, $anoAtual, $readyStorage, $joinWork, $exitWork, $observacaoWorksheet, $idAdmin);
            }
            $result->execute();
            //Obtem o id da nova ficha de trabalho inserida
            $idWorksheet = $con->insert_id;
        
            //Atualiza o orçamento associado à nova ficha de trabalho e adiciona o id da ficha de trabalho
            $sql = "UPDATE `budget` SET idWorksheet = '$idWorksheet' WHERE id = $idBudget";
            $result = $con->prepare($sql);
            $result->execute();

            //funcao log
            $username = $_SESSION['name'];
            $mensagem = "Ficha de Trabalho '$proximo_numero/$anoAtual' (ID: $idWorksheet) criado pelo administrador de ID $username.";
            registrar_log($mensagem);
        
            //Seleciona as diferentes ordens de secções
            $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
            $resultSection = $con->query($sqlSection);
            if ($resultSection->num_rows > 0) {
                //Percorre cada secção e a respetiva ordem
                while ($rowSection = $resultSection->fetch_assoc()) {
                    //Seleciona todos os produtos que estão dentro desta secção e a respetiva ordem
                    $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
                    $resultProducts = $con->query($sqlProducts);
                    if ($resultProducts->num_rows > 0) {
                        //Percorre todos os produtos
                        while ($rowProducts = $resultProducts->fetch_assoc()){
                            //Obtem o valor da checkBox "Check" via POST
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']] == "on") {
                                $check = 1;
                            } else {
                                $check = 0;
                            }
        
                            //Obtem o valor da checkBox "armazem" via POST
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']] == "on") {
                                $storage = 1;
                            } else {
                                $storage = 0;
                            }
                            //Obtem o resto dos dados do produto inseridos, via POST
                            $ref = $_POST['secao_' . $rowSection['orderSection'] . '_produto_ref_' . $rowProducts['orderProduct']];
                            $observacao = $_POST['secao_' . $rowSection['orderSection'] . '_produto_observacao_' . $rowProducts['orderProduct']];
                            $tamanho = $_POST['secao_' . $rowSection['orderSection'] . '_produto_tamanho_' . $rowProducts['orderProduct']];
                            //Se a descrição ou o tamanho não tiverem vazios
                            if (!empty($descricao) || !empty($tamanho)) {
                                //atualiza a tabela de produtos e secções e altera os dados para os inseridos
                                $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND orderSection = {$rowSection['orderSection']} AND orderProduct = '{$rowProducts['orderProduct']}';";
                                $result = $con->prepare($sql);
                                $result->execute();
                            }
                            //Insere tudo dentro da tabela das versões sendo que a versão é 1 porque é a primeira
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
            //Redireciona para a página de edit da ficha de trabalho que foi criada
            header('Location: fichaTrabalhoEdit.php?idWorksheet=' . $idWorksheet);
        } elseif ($op == "edit") {
            //Verifica se o administrador tem acesso para editar uma ficha de trabalho, caso contrario redereciona para a dashboard
            if (adminPermissions($con, "adm_002", "update") == 0) {
                header('Location: dashboard.php');
                exit();
            }
            //Obtem o id da ficha de trabalho a ser editada via GET
            $idWorksheet = $_GET['idWorksheet'];

            //Seleciona a ficha de trabalho cujo id é igual ao que foi recebido via GET
            $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
            $result = $con->query($sql);
            //caso não exista redireciona para a dashboard
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }
            //caso contrario obtem o id do orçamento associado e os dados da ficha de trabalho
            else {
                $row = $result->fetch_assoc();
                $idBudget =  $row['idBudget'];
                $numFicha = $row['num'];
                $yearFicha = $row['year'];
            }

            //Obtem todos os dados alterados e não alterados pelo administrador via POST
            $readyStorage = $_POST['prontoArmazem'];
            $joinWork = $_POST['entradaObra'];
            $exitWork = $_POST['saidaObra'];
            $observacaoWorksheet = $_POST['observation'];
            
            //Seleciona a maior versão, a atual, desta ficha de trabalho
            $sql2 = "SELECT MAX(idVersion) AS idVersion FROM worksheet_version WHERE idWorksheet = $idWorksheet;";
            $result2 = $con->query($sql2);
            if ($result2->num_rows > 0) {
                $row = $result2->fetch_assoc();
                //Incrementa essa versão para obter a proxima versão
                $idVersion =  $row['idVersion'] + 1;
            }
        
            //Atualiza o cabeçalho da ficha de trabalho
            $sql = "UPDATE `worksheet` SET readyStorage = '$readyStorage', joinWork = '$joinWork', exitWork = '$exitWork' WHERE id = $idWorksheet";
            $result = $con->prepare($sql);
            $result->execute();
        
            //Seleciona as diferentes ordens de cada secção 
            $stmt = $con->prepare("SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = ?");
            $stmt->bind_param("i", $idBudget);
            $stmt->execute();
            $resultSection = $stmt->get_result();
            if ($resultSection->num_rows > 0) {
                //Ciclo que percorre todas as secções e a respetiva ordem
                while ($rowSection = $resultSection->fetch_assoc()) {
                    //Seleciona os produtos desta secção e a devida ordem do produto
                    $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
                    $resultProducts = $con->query($sqlProducts);
                    if ($resultProducts->num_rows > 0) {
                        //Percorre todos os produtos e obtem a sua ordem
                        while ($rowProducts = $resultProducts->fetch_assoc()) {
                            //Obtem o valor da checkBox "Check" via POST
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_check_' . $rowProducts['orderProduct']] == "on") {
                                $check = 1;
                            } else {
                                $check = 0;
                            }
                            //Obtem o valor da checkBox "armazem" via POST
                            if (isset($_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']]) && $_POST['secao_' . $rowSection['orderSection'] . '_produto_armazem_' . $rowProducts['orderProduct']] == "on") {
                                $storage = 1;
                            } else {
                                $storage = 0;
                            }
                            //Obtem o resto dos dados do produto inseridos, via POST
                            $ref = $_POST['secao_' . $rowSection['orderSection'] . '_produto_ref_' . $rowProducts['orderProduct']];
                            $observacao = $_POST['secao_' . $rowSection['orderSection'] . '_produto_observacao_' . $rowProducts['orderProduct']];
                            $tamanho = $_POST['secao_' . $rowSection['orderSection'] . '_produto_tamanho_' . $rowProducts['orderProduct']];

                            //Se a descrição ou o tamanho ou a observação ou o tamanho não tiverem vazios
                            if (!empty($check) || !empty($storage) || !empty($observacao) || !empty($tamanho)) {
                                $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $idProduct = $row['id'];
                                }
                                //Atualiza a tabela de secções e produtos e coloca os novos valores
                                $sql = "UPDATE budget_sections_products SET checkProduct = $check, storageProduct = $storage, observationProduct = '$observacao', sizeProduct = '$tamanho' WHERE idBudget = $idBudget AND idProduct = $idProduct AND orderSection = '{$rowSection['orderSection']}' AND orderProduct = '{$rowProducts['orderProduct']}';";
                                $result = $con->prepare($sql);
                                $result->execute();
                                
                                //Insere tudo dentro da tabela de versões
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
            //Redireciona para a pagina de edit desta ficha de trabalho
            header('Location: fichaTrabalhoEdit.php?idWorksheet=' . $idWorksheet);
        }
        elseif ($op == "editFotos") {
            //Obtem o id da ficha de trabalho associada à galeria que foi editada
            $idWorksheet = $_GET['idWorksheet'];

            //Seleciona a ficha de trabalho cujo id é igual ao que foi recebido via GET
            $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
            $result = $con->query($sql);
            //caso não haja nenhuma ficha de trabalho com esse id redireciona para a dashboard
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }
            //Caso contrario obtem o id do orçamento associado
            else {
                $row = $result->fetch_assoc();
                $idBudget = $row['idBudget'];
            }

            //Caminhos das imagens
            $uploadDir = './images/uploads/';
            $publicDir = './images/uploads/';

            //seleciona a diferente ordem de cada secção 
            $sql = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
            $result1 = $con->query($sql);
            if ($result1->num_rows > 0) {
                //Ciclo percorre todas as secções e a devida ordem
                while ($row = $result1->fetch_assoc()) {
                    //Se tiver imagens inseridas no input desta secção então guarda essa imagem no servidor para formar um link
                    if (isset($_FILES['secao_'. $row['orderSection'] .'_foto']) && count($_FILES['secao_'. $row['orderSection'] .'_foto']['name']) > 0) {
                        $uploadedFiles = [];
                        
                        //Itera sobre todas as fotos de cada secção
                        foreach ($_FILES['secao_'. $row['orderSection'] .'_foto']['name'] as $key => $fileName) {
                            if ($_FILES['secao_'. $row['orderSection'] .'_foto']['error'][$key] === UPLOAD_ERR_OK) {
                                $fileTmpPath = $_FILES['secao_'. $row['orderSection'] .'_foto']['tmp_name'][$key];
                    
                                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                                $uniqueFileName = uniqid('photo_', true) . '.' . $extension;
                                $destinationPath = $uploadDir . $uniqueFileName;
                                
                                if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                                    // Criar thumbnail
                                    $thumbWidth = 500;
                                    $thumbHeight = 500;
                                    createThumbnail($destinationPath, $destinationPath, $thumbWidth, $thumbHeight);
                    
                                    $fileUrl = $publicDir . $uniqueFileName;
                                    
                                    $sql = "INSERT INTO worksheet_photos (idWorksheet, orderSection, img) VALUES (?, ?, ?)";
                                    $result = $con->prepare($sql);
                                    if ($result) {
                                        $result->bind_param("iis", $idWorksheet, $row['orderSection'], $fileUrl);
                                    }
                                    $result->execute();
                                } else {
                                    echo "Erro ao mover o arquivo: $fileName<br>";
                                }
                            } else {
                                echo "Erro ao enviar o arquivo: $fileName<br>";
                            }
                        }
                    } else {
                        echo "Nenhum arquivo foi enviado ou ocorreu um erro.";
                    }
                }
            }
            header('Location: fichaTrabalhoEdit.php?idWorksheet=' . $idWorksheet);
        }
        else {
            header('Location: dashboard.php');
            exit();
        }
    }
    else {
        header('Location: dashboard.php');
        exit();
    }
?>
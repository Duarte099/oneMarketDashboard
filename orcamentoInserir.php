<?php
    include("./db/conexao.php");

    if (adminPermissions($con, "adm_001", "inserir") == 0 || adminPermissions($con, "adm_001", "update") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idAdmin = $_SESSION['id'];

    if (isset($_GET['op']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $op = $_GET['op'];
        if ($op == "save") {
            $idClient = $_GET['idClient'];
            $sql = "SELECT * FROM client WHERE id = '$idClient'";
            $result = $con->query($sql);
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }

            $numProjeto = $_POST['numOrcamento'];
            $nameBudget = $_POST['nomeProjeto'];
            $laborPercent = (float) str_replace('%', '', $_POST['laborPercent']);
            $discountPercent = (float) str_replace('%', '', $_POST['discountPercent']);
            $observation = $_POST['observation'];

            $anoAtual = date("Y");

            $sql = "SELECT MAX(num) AS maior_numero FROM budget WHERE year = $anoAtual;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $proximo_numero = $row['maior_numero'] + 1;
            }
            else {
                $proximo_numero = 1;
            }

            $sql = "INSERT INTO budget (idClient, name, num, year, laborPercent, discountPercent, observation, createdBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $result = $con->prepare($sql);
            if ($result) {
                $result->bind_param("issiddsi", $idClient, $nameBudget, $proximo_numero, $anoAtual, $laborPercent, $discountPercent, $observation, $idAdmin);
            }

            $result->execute();
            $idBudget = $con->insert_id;

            //funcao log
            $idAdministrador = $_SESSION['id'];
            $username = $_SESSION['name'];
            $mensagem = "Orçamento " . $proximo_numero . "/" . $anoAtual . "(" . $idBudget . ") criado pelo administrador " . $username ."(" . $idAdministrador . ")";
            registrar_log($mensagem);
            //Inserir secções
            for ($i=1; $i <= 20; $i++) {
                $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $i]);
                if (!empty($secao)) {
                    $sql = "INSERT INTO budget_sections_products (idBudget, nameSection, orderSection) VALUES ($idBudget, '$secao', $i);";
                    $result = $con->prepare($sql);
                    $result->execute();

                    $data = date('Y-m-d');
                    $sql = "INSERT INTO budget_version (idVersion, idBudget, nameSection, orderSection, created) VALUES (1, $idBudget, '$secao', $i, '$data');";
                    $result = $con->prepare($sql);
                    $result->execute();
                }
            }

            header('Location: orcamentoEdit.php?idBudget=' . $idBudget);
        }
        elseif ($op == "edit") {
            $idBudget = $_GET['idBudget'];
            $sql = "SELECT * FROM budget WHERE id = '$idBudget'";
            $result = $con->query($sql);
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }

            $numProjeto = $_POST['numOrcamento'];
            $nameBudget = $_POST['nomeProjeto'];
            $laborPercent = (float) str_replace('%', '', $_POST['laborPercent']);
            $discountPercent = (float) str_replace('%', '', $_POST['discountPercent']);
            $observation = $_POST['observation'];

            $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $numSections =  $row['numSections'];
            }

            $sql = "SELECT MAX(idVersion) AS idVersion FROM budget_version WHERE idBudget = $idBudget;";
            $result = $con->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $versao =  $row['idVersion'] + 1;
            }

            //cabeçalho
            $sql = "UPDATE `budget` SET name = '$nameBudget', laborPercent = $laborPercent, discountPercent = $discountPercent, observation = '$observation' WHERE id = $idBudget";
            $result = $con->prepare($sql);
            $result->execute();

            //funcao log
            $username = $_SESSION['name'];
            $mensagem = "Orçamento '$nameBudget' (ID: $idBudget) editado pelo administrador de ID $username.";
            registrar_log($mensagem);

            //Ciclo for para percorrer secções
            for ($i=1; $i <= $numSections+5; $i++) {
                //Obter nome da secção do form
                $secao = mysqli_real_escape_string($con, $_POST['seccao_nome_' . $i]);
                //Se secção não estiver vazia...
                if (!empty($secao)) {
                    //Select para obter informações do nome e id  da secção que esta na base de dados
                    $sqlCheck = "SELECT nameSection FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = $i";
                    $result = $con->query($sqlCheck);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $selectNameSecao = $row['nameSection'];
                    }
                    //Verificar se a que está na base de dados não é igual à que veio do formulario
                    if ($selectNameSecao != $secao) {
                        //Atualizar na tabela de secções e produtos o nome da secção atualizada
                        $sql = "UPDATE `budget_sections_products` SET nameSection = '$secao' WHERE idBudget = $idBudget AND orderSection = $i";
                        $result = $con->prepare($sql);
                        $result->execute();
                    }

                    //Inserir novas secções 
                    if ($i > $numSections + 5) {
                        $sql = "INSERT INTO budget_sections_products (idBudget, nameSection, orderSection) VALUES ($idBudget, '$secao', $i);";
                        $result = $con->prepare($sql);
                        $result->execute();
                    }
                    $data = date('Y-m-d');
                    //Inserir a nova versão à tabela de versões
                    $sql = "INSERT INTO budget_version (idVersion, idBudget, nameSection, orderSection, created) VALUES ($versao, $idBudget, '$secao', $i, '$data');";
                    $result = $con->prepare($sql);
                    $result->execute();
                    
                    //Ciclo for para percorrer produtos
                    for ($j=1; $j <= 10; $j++) {
                        //Obter dados de cada produto de cada secção (i)
                        $ref = trim($_POST['secao_' . $i . '_produto_ref_' . $j]);
                        $designacao = $_POST['secao_' . $i . '_produto_designacao_' . $j];
                        $quantidade = $_POST['secao_' . $i . '_produto_quantidade_' . $j];
                        $descricao = $_POST['secao_' . $i . '_produto_descricao_' . $j];
                        $precoUnitario = trim($_POST['secao_' . $i . '_produto_preco_unitario_' . $j]);
                        //Se os campos referencia, nome e preço não estiverem vazios então...
                        if(!empty($ref)) {
                            //Obter id do produto cuja referencia é igual à inserida
                            $sql = "SELECT id FROM product WHERE product.reference = '$ref';";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $idProduct = $row['id'];
                            }

                            //Obter ultimos dados inseridos neste orçamento para comparar com os de agora
                            $sqlCheck = "SELECT `idProduct`,
                                                `refProduct`,
                                                `nameProduct`,
                                                `amountProduct`,
                                                `descriptionProduct`,
                                                `valueProduct`
                                        FROM budget_sections_products
                            WHERE idBudget = $idBudget AND orderSection = $i AND orderProduct = $j";
                            $result = $con->query($sqlCheck);
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                    $selectIdProduct = $row['idProduct'];
                                    $selectRefProduct = $row['refProduct'];
                                    $selectNameProduct = $row['nameProduct'];
                                    $selectAmountProduct = $row['amountProduct'];
                                    $selectDescriptionProduct = $row['descriptionProduct'];
                                    $selectValueProduct = $row['valueProduct'];
                                //Se todos os valores de cada produto forem diferentes aos que estão na base de dados ou seja, antes atualizados entao
                                if ($selectIdProduct != $idProduct || $selectRefProduct != $ref || $selectNameProduct != $designacao || $selectDescriptionProduct != $descricao || $selectValueProduct != $precoUnitario) {
                                    //Atualiza a tabela de secções e produtos para os novos valores inseridos
                                    $sql = "UPDATE `budget_sections_products` 
                                        SET idProduct = $idProduct, refProduct = '$ref', nameProduct = '$designacao', amountProduct = '$quantidade', descriptionProduct = '$descricao', valueProduct = '$precoUnitario'
                                        WHERE idBudget = $idBudget AND orderSection = $i AND orderProduct = $j";
                                    echo $sql;
                                    $result = $con->prepare($sql);
                                    $result->execute();
                                }
                            }
                            else
                            {
                                //Se não tiver produtos naquela secção então insere na taela de secções e produtos, o novo produto
                                $sql = "INSERT INTO budget_sections_products 
                                    (idBudget, nameSection, orderSection, idProduct, orderProduct, 
                                    refProduct, nameProduct, amountProduct, descriptionProduct, valueProduct) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                $result = $con->prepare($sql);
                                $result->bind_param("iiiiissisd",$idBudget, $secao, $i, $idProduct, $j,$ref, $designacao, $quantidade, $descricao, $precoUnitario);
                                $result->execute();
                            }
                            $data = date('Y-m-d');
                            //Insere todos os produtos e secções à tabela de versões
                            $sql = "INSERT INTO `budget_version`(`idVersion`,
                                                                `idBudget`,
                                                                `nameSection`,
                                                                `orderSection`,
                                                                `idProduct`,
                                                                `orderProduct`,
                                                                `refProduct`,
                                                                `nameProduct`,
                                                                `amountProduct`,
                                                                `descriptionProduct`,
                                                                `valueProduct`,
                                                                `created`)
                                    VALUES ($versao, $idBudget, '$secao', $i, $idProduct, $j,'$ref', '$designacao', '$quantidade', '$descricao', '$precoUnitario', '$data');";
                            $result = $con->prepare($sql);
                            $result->execute();
                        }
                    }
                }
            }
            header('Location: orcamentoEdit.php?idBudget=' . $idBudget);
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
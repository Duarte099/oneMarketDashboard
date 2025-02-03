<?php
    //Incluit a conexão à base de dados nesta página
    include('./db/conexao.php');

    //Se a operação tiver sido declarada e o metodo de request for POST
    if (isset($_GET['op']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        //Obtem a operação a ser feita
        $op = $_GET['op'];
        if ($op == 'save') {
            //Se o administrador não tiver permissões para criar novos clientes então redireciona para a dashboard
            if (adminPermissions($con, "adm_004", "inserir") == 0) {
                header('Location: dashboard.php');
                exit();
            }
            
            //Obtem todos os dados inserido via POST
            $email = "";
            $nif = "";
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];
            $nif = $_POST['nif'];
            $status = $_POST['status'];

            //Insere os dados inseridos na base ded dados
            $query = "INSERT INTO client (name, email, contact, nif, active) VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);

            if ($stmt) {
                $stmt->bind_param("ssssi", $nome, $email, $contacto, $nif, $status);
                if ($stmt->execute()) {
                    $idClient = $con->insert_id;
                    //funcao log
                    $username = $_SESSION['name'];
                    $iddAdmin = $_SESSION['name'];
                    $mensagem = "Cliente '$nome' ($idClient) criado pelo administrador $username ($iddAdmin).";
                    registrar_log($mensagem);
                }
            }
            //Redireciona para a pagina dos clientes
            header('Location: cliente.php');
        }
        elseif ($op == 'edit') {
            //Se o administrador não tiver permissões para editar clientes então redireciona para a dashboard
            if (adminPermissions($con, "adm_004", "update") == 0) {
                header('Location: dashboard.php');
                exit();
            }

            //Obtem o id do cliente a ser editado via GET
            $idClient = $_GET['idClient'];
            
            //seleciona o cliente cujo id foi o que recebeu via GET
            $sql = "SELECT * FROM client WHERE id = '$idClient'";
            $result = $con->query($sql);
            //Se não houver cliente com esse id redireciona para a dashboard
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }

            // Obtem os dados inseridos na pagina de edit recebidos via POST
            $nome = isset($_POST['nome']) ? trim($_POST['nome']) : $client['name'];
            $email = isset($_POST['email']) ? trim($_POST['email']) : $client['email'];
            $contact = isset($_POST['contact']) ? trim($_POST['contact']) : $client['contact'];
            $nif = isset($_POST['nif']) ? trim($_POST['nif']) : $client['nif'];
            $status = isset($_POST['status']) ? intval($_POST['status']) : $client['active'];

            // Atualiza os dados do cliente na base de dados
            $updateQuery = "UPDATE client SET name = ?, email = ?, contact = ?, nif = ?, active = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("ssssii", $nome, $email, $contact, $nif, $status, $idClient);

            if ($stmt->execute()) {
                //funcao log
                $username = $_SESSION['name'];
                $mensagem = "Cliente '$nome' (ID: $idClient) editado pelo administrador de ID $username.";
                registrar_log($mensagem);
            }
            header('Location: clienteEdit.php?idClient=' . $idClient);
        }
        else{
            header('Location: dashboard.php');
            exit(); 
        }
    }
    else {
        header('Location: dashboard.php');
        exit();
    }
?>
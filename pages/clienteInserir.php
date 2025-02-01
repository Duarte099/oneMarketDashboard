<?php
    include('../db/conexao.php');
    
    if (adminPermissions($con, "adm_004", "inserir") == 0 || adminPermissions($con, "adm_004", "update") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    if (isset($_GET['op']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $op = $_GET['op'];
        if ($op == 'save') {
            $idClient= $_GET['id'];
            $email = "";
            $nif = "";
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];
            $nif = $_POST['nif'];
            $status = $_POST['status'];

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
            header('Location: ../pages/client.php');
        }
        elseif ($op == 'edit') {
            $idClient = $_GET['idClient'];
            
            $sql = "SELECT * FROM client WHERE id = '$idClient'";
            $result = $con->query($sql);
            if ($result->num_rows <= 0) {
                header('Location: dashboard.php');
                exit();
            }
            else {
                $row = $result->fetch_assoc();
                
            }
            // Caso o formulário seja para editar
            $nome = isset($_POST['nome']) ? trim($_POST['nome']) : $client['name'];
            $email = isset($_POST['email']) ? trim($_POST['email']) : $client['email'];
            $contact = isset($_POST['contact']) ? trim($_POST['contact']) : $client['contact'];
            $nif = isset($_POST['nif']) ? trim($_POST['nif']) : $client['nif'];
            $status = isset($_POST['status']) ? intval($_POST['status']) : $client['active'];

            // Atualiza os dados na base de dados
            $updateQuery = "UPDATE client SET name = ?, email = ?, contact = ?, nif = ?, active = ? WHERE id = ?";
            $stmt = $con->prepare($updateQuery);
            $stmt->bind_param("ssssii", $nome, $email, $contact, $nif, $status, $id);

            if ($stmt->execute()) {
                //funcao log
                $username = $_SESSION['name'];
                $mensagem = "Cliente '$nome' (ID: $idClient) editado pelo administrador de ID $username.";
                registrar_log($mensagem);

                header('Location: cliente.php');  // Quando acabar, manda de volta para a página dos clientes
                exit();
            } else {
                $error = "Erro ao atualizar o cliente.";
            }

            header('Location: ../pages/clientEdit.php?idClient=' . $idClient);
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
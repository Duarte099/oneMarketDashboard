<?php
    //Inclui a base de dados e a segurança da pagina 
    include('./db/conexao.php');
    
    //query sql para obter todos os clientes e os respetivos dados
    $sql = "SELECT 
            client.id as id,
            client.name as nome,
            client.email as email,
            client.contact as contacto,
            client.nif as nif,
            client.active as status
        FROM client;";

    $data = [];
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id' => $row['id'], 
                'nome' => $row['nome'], 
                'email' => $row['email'],
                'contacto' => $row['contacto'],
                'nif' => $row['nif'],
                'status' => $row['status'],
            ];
        }
    }

    //Envia os dados via json 
    header('Content-Type: application/json');
    echo json_encode($data);
?>
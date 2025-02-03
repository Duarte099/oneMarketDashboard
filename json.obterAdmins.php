<?php
    //Inclui a base de dados e a segurança da pagina 
    include('./db/conexao.php');
    
    //query sql para obter todos os administradores e os respetivos dados
    $sql = "SELECT 
            administrator.id as id,
            administrator.name as nome,
            administrator.email as email,
            administrator.user as user,
            administrator.img as img,
            administrator.birthday as nascimento,
            administrator.active as status
        FROM administrator;";

    $data = [];
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'id' => $row['id'], 
                'nome' => $row['nome'], 
                'email' => $row['email'],
                'user' => $row['user'],
                'img' => $row['img'],
                'nascimento' => $row['nascimento'],
                'status' => $row['status'],
            ];
        }
    }
    
    //Envia os dados via json 
    header('Content-Type: application/json');
    echo json_encode($data);
?>
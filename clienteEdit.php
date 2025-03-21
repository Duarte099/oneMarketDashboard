<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php');

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 5;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_004", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Recebe o id do cliente a ser editado via GET
    $idClient = $_GET['idClient'];

    //Se o id existir, obtem os dados do cliente que esta a ser editado
    $sql = "SELECT * FROM client WHERE id = '$idClient'";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $contacto = $row['contact'];
        $nif = $row['nif'];
        $active = $row['active'];
    }
    //Se não existir redireciona para a dashboard
    else{
        header('Location: dashboard.php');
        exit();
    }
?>
    <link rel="stylesheet" href="./css/novoCliente.css">
    <title>OneMarket | <?php echo $name; ?></title>
</head>
<body>

    <?php 
        //inclui a sideBar na página
        include('sideBar.php'); 
    ?>

    <!-- Main Content -->
    <div class="content">
        <?php 
            //Inclui o header na página
            include('header.php'); 
        ?>          

        <main>
            <div class="header">
                <div class="left">
                    <h1><?php echo $name; ?></h1>
                </div>
            </div>

            <div class="bottom-data">
                <div class="administrator">
                    <form method="POST" action="clienteInserir.php?idClient=<?=$idClient?>&op=edit">
                        <section>
                            <!-- Caso o administrador não tenha permissões para editar coloca todos os campos como readonly para impossibilitar alterações -->
                            <h2>Dados do Cliente</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Nome:</label>
                                    <input type="text" name="nome" value="<?php echo $name; ?>" <?php if (adminPermissions($con, "adm_004", "update") == 0) {echo "readonly";}?> required>
                                </div>
                                <div class="section-group">
                                    <label>Email:</label>
                                    <input type="email" name="email" value="<?php echo $email; ?>" <?php if (adminPermissions($con, "adm_004", "update") == 0) {echo "readonly";}?> required>
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="text" name="contact" value="<?php echo $contacto; ?>" <?php if (adminPermissions($con, "adm_004", "update") == 0) {echo "readonly";}?> required>
                                </div>
                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>NIF:</label>
                                    <input type="text" name="nif" value="<?php echo $nif; ?>" <?php if (adminPermissions($con, "adm_004", "update") == 0) {echo "readonly";}?> required>
                                </div>
                                <div class="section-group">
                                    <label>Status:</label>
                                    <?php if (adminPermissions($con, "adm_004", "update") == 0) { ?>
                                        <input type="text" name="status" id="status" value="<?php if ($active == 0) {echo "Inativo";} else {echo "Ativo";}?>" readonly>
                                    <?php } else {?>
                                        <select name="status">
                                            <option value="1" <?php echo $active == 1 ? 'selected' : ''; ?>>Ativo</option>
                                            <option value="0" <?php echo $active == 0 ? 'selected' : ''; ?>>Inativo</option>
                                        </select>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php //Se o administrador tiver permissão para editar mostra o botão de guardar alterações
                            if (adminPermissions($con, "adm_004", "update") == 1) { 
                                ?>
                                <button type="submit">Atualizar Cliente</button>
                            <?php } ?>
                        </section>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

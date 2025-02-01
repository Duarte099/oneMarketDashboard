<?php 
    include('../pages/head.php');

    $estouEm = 5;

    if (adminPermissions($con, "adm_004", "view") == 0 || adminPermissions($con, "adm_004", "update") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idClient = $_GET['idClient'];

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
    else{
        header('Location: dashboard.php');
        exit();
    }
?>
    <link rel="stylesheet" href="../css/novoCliente.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | <?php echo $name; ?></title>
</head>
<body>

    <?php include('../pages/sideBar.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <?php include('../pages/header.php'); ?>          

        <main>
            <div class="header">
                <div class="left">
                    <h1><?php echo $name; ?></h1>
                </div>
            </div>

            <div class="bottom-data">
                <div class="administrator">
                    <form method="POST" action="../pages/clienteInserir.php?idClient=<?=$idClient?>&op=edit">
                        <section>
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
                            <?php if (adminPermissions($con, "adm_004", "update") == 1) { ?>
                                <button type="submit">Atualizar Cliente</button>
                            <?php } ?>
                        </section>
                    </form>

                    <?php
                        if (isset($error)) {
                            echo "<p style='color: red;'>$error</p>";
                        }
                    ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

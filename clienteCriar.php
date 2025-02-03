<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 5;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_004", "inserir") == 0) {
        header('Location: index.php');
        exit();
    }
?>
    <link rel="stylesheet" href="./css/novoCliente.css">
    <title>OneMarket | Novo Cliente</title>
</head>

<body>

    <?php 
        //inclui a sideBar na página
        include('sideBar.php'); 
    ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php 
            //Inclui o header na página
            include('header.php'); 
        ?>          
        <!-- End of Navbar -->

        <main>
            <div class="header">
                <div class="left">
                    <h1>Novo Cliente</h1>
                </div>
            </div>
            <div class="bottom-data">
                <div class="client">
                    <form method="POST" action="clienteInserir.php?op=save">
                        <section>
                            <h2>Dados do Cliente</h2>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>Nome:</label>
                                    <input type="text" name="nome" required>
                                </div>
                                <div class="section-group">
                                    <label>Email:</label>
                                    <input type="email" name="email">
                                </div>
                                <div class="section-group">
                                    <label>Contacto:</label>
                                    <input type="number" name="contacto" required>
                                </div>

                            </div>
                            <div class="section-row">
                                <div class="section-group">
                                    <label>NIF:</label>
                                    <input type="number" name="nif" required>
                                </div>
                                <div class="section-group">
                                    <label>Status</label>
                                    <select name="status">
          
                                    <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit">Adicionar Cliente</button>
                        </section>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
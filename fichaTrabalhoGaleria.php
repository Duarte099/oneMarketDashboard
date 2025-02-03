<?php 
    //inclui o head que inclui as páginas de js necessárias, a base de dados e segurança da página
    include('head.php'); 

    //variável para indicar à sideBar que página esta aberta para ficar como ativa na sideBar
    $estouEm = 3;

    //Verifica se o administrador tem acesso para aceder a esta pagina, caso contrario redereciona para a dashboard
    if (adminPermissions($con, "adm_002", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    //Obtem o id da ficha de trabalho associada à galeria via GET
    $idWorksheet = $_GET['idWorksheet'];

    //Obtem a ficha de trabalho cujo id foi o recebido pelo GET
    $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
    $result = $con->query($sql);
    //Caso não obtenha nenhuma ficha de trabalho com esse id redireciona para dashboard
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    } 
    //Caso haja uma ficha de trabalho com esse id obtem os dados dela
    else {
        $row = $result->fetch_assoc();
        $numWorksheet = $row['num'];
        $yearWorksheet = $row['year'];
        $idBudget = $row['idBudget'];
        $numFichaTrabalho = "$numWorksheet/$yearWorksheet";
    }

    //Obtem o numero de secções desta ficha de trabalho
    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }
?>
    <link rel="stylesheet" href="./css/fichaTrabalhoGaleria.css">
    <title>OneMarket | <?php echo $numFichaTrabalho; ?> </title>
    <link href="./css/lightbox.css" rel="stylesheet" />
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
                    <h1>Galeria de fotos <?php echo ($numFichaTrabalho) ?></h1>
                </div>
            </div>
            <form action="fichaTrabalhoInserir.php?idWorksheet=<?= $idWorksheet ?>&op=editFotos" method=post enctype="multipart/form-data">
                <div class="bottom-data">
                    <?php
                        //Ciclo para percorrer todas as secções mostra-las com o respetivo nome
                        for ($i = 1; $i <= $numSections; $i++) {
                            $sql = "SELECT nameSection FROM budget_sections_products WHERE orderSection = $i AND idBudget = $idBudget;";
                            $result = $con->query($sql);
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $nomeSecao = $row['nameSection'];
                                $displayStyle = '';
                            } else {
                                $nomeSecao = '';
                                $displayStyle = 'display: none;';
                            }
                            ?>
                        <div class="worksheet" style="<?php echo $displayStyle; ?>">
                            <section id="secoes">
                                <div class="secao">
                                    <h3>Secção <?php echo $i; ?>:</h3>
                                    <input type="search"
                                        id="search-box-<?php echo $i; ?>"
                                        name="seccao_nome_<?php echo $i; ?>"
                                        list="datalistSection"
                                        placeholder="Nome da secção"
                                        value="<?php echo $nomeSecao; ?>"
                                        style="width: 100%;"
                                        readonly>
                                    <input type="file" name="secao_<?php echo $i; ?>_foto[]" id="photo" multiple accept="image/*">
                                    <div class="grid" id="imageGrid">
                                        <?php
                                            //Mostra todas as fotos pertencentes a esta secção
                                            $sql = "SELECT img FROM worksheet_photos WHERE idWorksheet = ". $idWorksheet ." AND orderSection = ". $i .";";
                                            $result = $con->query($sql);
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "
                                                        <a href=". $row['img'] ." data-lightbox=\"image-1\" data-title=\"My caption\">
                                                            <img src=". $row['img'] ." style=\"width: 150px; cursor: pointer;\">
                                                        </a>
                                                    ";
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                </div>
                <button id=botSaveWorksheet type="submit">Guardar Alterações</button>
            </form>
        </main>
    </div>

    <script src="lightbox.js"></script> 
</body>

</html>
<?php 
    include('../pages/head.php'); 

    $estouEm = 3;

    if (adminPermissions($con, "adm_002", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idWorksheet = $_GET['idWorksheet'];
    $sql = "SELECT * FROM worksheet WHERE id = '$idWorksheet'";
    $result = $con->query($sql);
    if ($result->num_rows <= 0) {
        header('Location: dashboard.php');
        exit();
    } else {
        $row = $result->fetch_assoc();
        $numWorksheet = $row['num'];
        $yearWorksheet = $row['year'];
        $idBudget = $row['idBudget'];
        $numFichaTrabalho = "$numWorksheet/$yearWorksheet";
    }

    $inputValue = '';
    $produtosIndex = 0;

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
    include('../pages/sideBar.php');
    ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php
        include('../pages/header.php');
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
                    $produtosIndex = 0;
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
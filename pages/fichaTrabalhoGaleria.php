<?php
session_start();

$estouEm = 3;

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php');
    exit();
}

include('../db/conexao.php');

if (adminPermissions("adm_002", "view") == 0) {
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/fichaTrabalhoGaleria.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | <?php echo $numFichaTrabalho; ?> </title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            <form action="fichaTrabalhoInserir.php?idWorksheet=<?= $idWorksheet ?>&op=editFotos" method=post>
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
                                    <input type="file" name="secao_<?php echo $i; ?>_foto" id="photo" oninput="displayProfilePic()" accept="image/*">
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
                <button id=botSaveBudget type="submit">Guardar Alterações</button>
            </form>
        </main>
    </div>

    <script src="./js/lightbox.js"></script> 

    <script>
        function displayProfilePic() {
            const fileInput = document.getElementById('photo');
            const imageGrid = document.getElementById('imageGrid');

            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const reader = new FileReader();

                reader.onload = function (e) {
                    // Create a new anchor and image element
                    const anchor = document.createElement('a');
                    anchor.href = e.target.result;
                    anchor.setAttribute('data-lightbox', 'image-1');
                    anchor.setAttribute('data-title', 'My caption');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '150px';
                    img.style.cursor = 'pointer';

                    // Append the image to the anchor
                    anchor.appendChild(img);

                    // Add the new anchor to the grid
                    imageGrid.appendChild(anchor);
                };

                // Read the file as a data URL
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>
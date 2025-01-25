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
                    for ($i = 1; $i <= $numSections + 5; $i++) {
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
                                    <input type="file" name="photo" id="photo" oninput="displayProfilePic()" accept="image/*">
                                    
                                </div>
                            </section>
                        </div>
                    <?php } ?>
                </div>
                <button id=botSaveBudget type="submit">Guardar Alterações</button>
            </form>
        </main>
    </div>
    <script>
        function displayProfilePic() {
            const file = event.target.files[0]; // Obtém o primeiro arquivo selecionado
            const preview = document.getElementById('profilePic'); // Seleciona a div

            if (file) {
                const reader = new FileReader();

                // Evento para quando o arquivo for carregado
                reader.onload = function(e) {
                    // Define o background-image da div com o resultado do arquivo carregado
                    preview.style.backgroundImage = `url(${e.target.result})`;
                };

                reader.readAsDataURL(file); // Lê o arquivo como URL base64
            } else {
                // Remove a imagem de fundo se nenhum arquivo for selecionado
                preview.style.backgroundImage = "";
            }
        }
    </script>
</body>

</html>
<?php 
    session_start();

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
    $sql = "SELECT idClient, idBudget, num, year, readyStorage, joinWork, exitWork, observation FROM worksheet WHERE id = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
        $idBudget = $row['idBudget'];
        $numWorksheet = $row['num'];
        $yearWorksheet = $row['year'];
        $readyStorage = $row['readyStorage'];
        $joinWork = $row['joinWork'];
        $exitWork = $row['exitWork'];
        $observation = $row['observation'];
    } 
    else {
        header('Location: ../pages/dashboard.php');
        exit();
    }
    $numWorksheet = "$numWorksheet/$yearWorksheet";

    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }

    $sql = "SELECT name FROM administrator INNER JOIN worksheet ON administrator.id = createdBy WHERE worksheet.id = $idWorksheet;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $createdBy =  $row['name'];
    }

    $sql = "SELECT num, year FROM budget WHERE id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numOrcamento = $row['num'];
        $yearOrcamento = $row['year'];
    }
    $numOrcamento = "$numOrcamento/$yearOrcamento";

    $totalBudget = 0;
    $sql = "SELECT valueProduct, amountProduct FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    while ($row = $result->fetch_assoc()) {
        $totalBudget = $totalBudget + $row['valueProduct'] * $row['amountProduct'];
    }

    $sql = "SELECT name, contact FROM client WHERE client.id = $idClient;";
    $result = $con->query($sql);
    $row = $result->fetch_assoc();
        $clientName = $row['name'];
        $clientContact = $row['contact'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Trabalho - Impressão</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/orcamentoImpressao.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Ficha de trabalho nº <?php echo $numWorksheet?></h1>
        </div>

        <div class="info">
            <div>
                <p><strong>Cliente:</strong> <?php echo $clientName ?></p>
                <p><strong>Contacto:</strong> <?php echo $clientContact ?></p>
                <p><strong>Orçamento:</strong> <?php echo $numOrcamento ?></p>
                <p><strong>Data:</strong> <?php echo date('Y-m-d') ?></p>
            </div>
            <div>
                <p><strong>Pronto em armazém:</strong> <?php echo $readyStorage;?></p>
                <p><strong>Entrada em obra:</strong> <?php echo $joinWork;?></p>
                <p><strong>Saida de obra:</strong> <?php echo $exitWork;?></p>
                <p><strong>Elaborado por:</strong> <?php echo $createdBy;?></p>
            </div>
        </div>

        <?php 
            $produtosIndex = 0; 
            $sqlSection = "SELECT DISTINCT orderSection FROM budget_sections_products WHERE idBudget = $idBudget;";
            $resultSection = $con->query($sqlSection);
            if ($resultSection->num_rows > 0) {
                while ($rowSection = $resultSection->fetch_assoc()) {
                    $sql = "SELECT nameSection FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = {$rowSection['orderSection']} AND nameSection != '';";
                    $result = $con->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $nomeSecao = $row['nameSection'];
                    }
                    else
                    {
                        $nomeSecao = "";
                    }
                    ?>
                    <div class="section">
                        <div class="section-title"><?php echo $nomeSecao; ?></div>
                        <table id = "table">
                            <thead>
                                <tr>
                                    <th style="width: 55px;">Check</th>
                                    <th style="width: 55px;">Armazém</th>
                                    <th style="text-align: center;">Nº</th>
                                    <th>REF</th>
                                    <th>Designação</th>
                                    <th>Quant</th>
                                    <th style="text-align: center;" colspan=2 >Observações</th>
                                </tr>
                            </thead>
                            <?php 
                                $sqlProducts = "SELECT orderProduct FROM budget_sections_products WHERE idBudget = $idBudget AND orderSection = '{$rowSection['orderSection']}' AND idProduct > 0;";
                                $resultProducts = $con->query($sqlProducts);
                                if ($resultProducts->num_rows > 0) {
                                    while ($rowProducts = $resultProducts->fetch_assoc()){
                                        $produtosIndex++; 
                                        $refProduct = '';
                                        $nameProduct = '';
                                        $amountProduct = 0;
                                        $descriptionProduct = '';
                                        $valueProduct = 0;
                                        $sizeProduct = '';?>
                                        <tbody>
                                            <tr>
                                                <?php
                                                    $sql = "SELECT refProduct, nameProduct, amountProduct FROM budget_sections_products WHERE idbudget = $idBudget AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                    $result = $con->query($sql);
                                                    if ($result->num_rows > 0) {
                                                        $row = $result->fetch_assoc();
                                                        $refProduct = $row['refProduct'];
                                                        $nameProduct = $row['nameProduct'];
                                                        $amountProduct = $row['amountProduct'];
                                                    }

                                                    $sql = "SELECT checkProduct, storageProduct, observationProduct, sizeProduct FROM budget_sections_products WHERE idbudget = $idBudget AND orderProduct = '{$rowProducts['orderProduct']}' AND orderSection = '{$rowSection['orderSection']}';";
                                                    $result = $con->query($sql);
                                                    if ($result->num_rows > 0) {
                                                        $row = $result->fetch_assoc();
                                                        if (isset($row['checkProduct']) && $row['checkProduct'] == 1) {
                                                            ?> <td style="text-align: center;"><i class='bx bx-check'></i></td> <?php
                                                        }
                                                        else {
                                                            ?> <td > </td> <?php
                                                        }
                                                        if (isset($row['storageProduct']) && $row['storageProduct'] == 1) {
                                                            ?> <td style="text-align: center;"><i class='bx bx-check'></i></td> <?php
                                                        }
                                                        else {
                                                            ?> <td> </td> <?php
                                                        }
                                                        $observationProduct = $row['observationProduct'];
                                                        $sizeProduct = $row['sizeProduct'];
                                                    }
                                                ?>
                                                <td style="text-align: center;"><?php echo $produtosIndex;?></td>
                                                <td><?php echo $refProduct;?></td>
                                                <td><?php echo $nameProduct;?></td>
                                                <td><?php echo $amountProduct;?></td>
                                                <td><?php echo $observationProduct;?></td>
                                                <td><?php echo $sizeProduct;?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                <?php } 
                            ?>
                        </table>
                    </div>                                 
                <?php } ?>
            <?php } ?>
        <div class="footer">
            <strong>Observações:</strong>
            <div class="observacoes">
                <?php echo $observation; ?>
            </div>
        </div>
    </div>
</body>
</html>
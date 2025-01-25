<?php 
    session_start();

    $estouEm = 2;

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    include('../db/conexao.php');

    if (adminPermissions("adm_001", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }

    $idBudget = $_GET['idBudget'];
    $sql = "SELECT budget.idClient FROM budget WHERE budget.id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $idClient =  $row['idClient'];
    } 
    else {
        header('Location: ../pages/dashboard.php');
        exit();
    }

    $sql = "SELECT COUNT(DISTINCT orderSection) AS numSections FROM budget_sections_products WHERE idBudget = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numSections =  $row['numSections'];
    }

    $sql = "SELECT num, year, name, laborPercent , discountPercent, observation FROM budget WHERE id = $idBudget;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numBudget = $row['num'];
        $yearBudget = $row['year'];
        $nameBudget = $row['name'];
        $maoObraBudget = $row['laborPercent'];
        $descontoBudget = $row['discountPercent'];
        $observacao = $row['observation'];
    }
    $numOrçamento = "$numBudget/$yearBudget";

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
    <title>Orçamento - Impressão</title>
    <link rel="stylesheet" href="../css/orcamentoImpressao.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ORÇAMENTO n. <?php echo $numOrçamento?></h1>
        </div>

        <div class="info">
            <div>
                <p><strong>Cliente:</strong> <?php echo $clientName?></p>
                <p><strong>Projeto:</strong> <?php echo $nameBudget?></p>
            </div>
            <div>
                <p><strong>Data:</strong> <?php echo date('Y-m-d');?></p>
                <p><strong>Contato:</strong> <?php echo $clientContact?></p>
            </div>
        </div>

        <?php 
            $produtosIndex = 0; 
            for ($i=1; $i <= $numSections; $i++) {
                $sql = "SELECT COUNT(idProduct) AS numProducts FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderSection = '$i' AND idProduct > 0;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $numProducts = $row['numProducts'];
                }

                $sql = "SELECT nameSection FROM budget_sections_products WHERE orderSection = $i AND idBudget = $idBudget;";
                $result = $con->query($sql);
            
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $nomeSecao = htmlspecialchars($row['nameSection']);
                }?>
                <div class="section">
                    <div class="section-title"><?php echo $nomeSecao; ?></div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>N/REF</th>
                                <th>Designação</th>
                                <th>Quant</th>
                                <th>Descrição</th>
                                <th>P. Unit</th>
                                <th>P. Total</th>
                            </tr>
                        </thead>
                        <?php 
                            for ($j=1; $j <= $numProducts; $j++) { 
                                $produtosIndex++;
                                ?>
                                    <tbody>
                                        <tr>
                                            <?php 
                                                $sql = "SELECT refProduct, nameProduct, amountProduct, descriptionProduct, valueProduct FROM budget_sections_products WHERE budget_sections_products.idbudget = $idBudget AND orderProduct = '$j' AND orderSection = '$i';";
                                                $result = $con->query($sql);
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        $refProduct = $row['refProduct'];
                                                        $nameProduct = $row['nameProduct'];
                                                        $amountProduct = $row['amountProduct'];
                                                        $descriptionProduct = $row['descriptionProduct'];
                                                        $valueProduct = $row['valueProduct'];
                                                    }
                                                }
                                            ?>
                                            <td><?php echo $produtosIndex;?></td>
                                            <td><?php echo $refProduct;?></td>
                                            <td><?php echo $nameProduct;?></td>
                                            <td><?php echo $amountProduct;?></td>
                                            <td><?php echo $descriptionProduct;?></td>
                                            <td><?php echo $valueProduct;?></td>
                                            <td><?php echo $amountProduct * $valueProduct . "€";?></td>
                                        </tr>
                                    </tbody>
                            <?php 
                            if ($j == $numProducts) {?>
                                <script>alert(".");</script>
                            <?php }
                            }
                        ?>
                    </table>
                </div>
            <?php } 
        ?>

        <div class="footer">
            <div class="footer-row">
                <div class="footer-cell label">Deslocações e montagens <span style="padding-left: 100px;"><?php echo $maoObraBudget . "%"; ?></span></div>
                <div class="footer-cell value"><?php echo $totalBudget * ($maoObraBudget / 100) . "€"; ?></div>
            </div>
            <div class="footer-row">
                <div class="footer-cell label">TOTAL</div>
                <div class="footer-cell value"><?php echo $totalBudget . "€";?></div>
            </div>
            <div class="footer-row">
                <div class="footer-cell label" style="margin-bottom: 10px;">C/ desconto 5%</div>
                <div class="footer-cell value" style="margin-bottom: 10px;"><?php echo $totalBudget * ($descontoBudget / 100) . "€";?></div>
            </div>
            <strong>Observações:</strong>
            <div class="observacoes">
                <?php echo $observacao; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert("Atingiu o máximo de produtos criados por alteração.");
        });
    </script>
</body>
</html>
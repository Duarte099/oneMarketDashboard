<?php 
    include('../db/conexao.php'); 
    $estouEm = 3;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $idBudget = '';

    if (isset($_GET['idBudget'])) {
        $idBudget = $_GET['idBudget'];
    }
?>

<script>console.log(<?php echo $idBudget; ?>)</script>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/novoOrcamento.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Nova Ficha de Trabalho</title>
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
                    <h1>Nova Ficha de Trabalho</h1>
                </div>
            </div>

            <div class="bottom-data">
                <div class="budget">
                    <section>
                        <h2>Dados da Ficha de Trabalho</h2>
                        <label>Cliente:
                            <input type="text" name="cliente" required>
                        </label>
                        <label>Projeto:
                            <input type="text" name="projeto" required>
                        </label>
                        <label>Contato:
                            <input type="text" name="contacto" required>
                        </label>
                        <label>Data de Criação:
                            <input type="date" name="data_criacao" value="<?php echo date('Y-m-d'); ?>" required>
                        </label>
                    </section>

                    <!-- Seções e Produtos -->
                    <section id="secoes">
                        <h2>Seções</h2>
                        <div class="secao">
                            <h3>Seção: <input type="text" name="secoes[0][nome]" placeholder="Nome da seção"></h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>N/REF</th>
                                        <th>Designação</th>
                                        <th>Quantidade</th>
                                        <th>Descrição</th>
                                        <th>Preço Unitário</th>
                                        <th>Preço Total</th>
                                    </tr>
                                </thead>
                                <tbody class="produtos">
                                    <tr>
                                        <td>1</td>
                                        <td><input type="text" name="secoes[0][produtos][0][n_ref]" required></td>
                                        <td><input type="text" name="secoes[0][produtos][0][designacao]" required></td>
                                        <td><input type="number" name="secoes[0][produtos][0][quantidade]" value="1" required></td>
                                        <td><input type="text" name="secoes[0][produtos][0][descricao]"></td>
                                        <td><input type="number" step="0.01" name="secoes[0][produtos][0][preco_unitario]" required></td>
                                        <td><input type="number" step="0.01" name="secoes[0][produtos][0][preco_total]" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" onclick="adicionarProduto(this)">Adicionar Produto</button>
                        </div>
                        <button type="button" onclick="adicionarSecao()">Adicionar Seção</button>
                    </section>
                </div>
            </div>
        </main>

        <script>
            let secaoIndex = 0;

            function adicionarProduto(botao) {
                const produtos = botao.closest(".secao").querySelector(".produtos");
                const produtoIndex = produtos.children.length;
                const linha = `
                    <tr>
                        <td>${produtoIndex + 1}</td>
                        <td><input type="text" name="secoes[${secaoIndex}][produtos][${produtoIndex}][n_ref]" required></td>
                        <td><input type="text" name="secoes[${secaoIndex}][produtos][${produtoIndex}][designacao]" required></td>
                        <td><input type="number" name="secoes[${secaoIndex}][produtos][${produtoIndex}][quantidade]" value="1" required></td>
                        <td><input type="text" name="secoes[${secaoIndex}][produtos][${produtoIndex}][descricao]"></td>
                        <td><input type="number" step="0.01" name="secoes[${secaoIndex}][produtos][${produtoIndex}][preco_unitario]" required></td>
                        <td><input type="number" step="0.01" name="secoes[${secaoIndex}][produtos][${produtoIndex}][preco_total]" readonly></td>
                    </tr>
                `;
                produtos.insertAdjacentHTML("beforeend", linha);
            }

            function adicionarSecao() {
                secaoIndex++;
                const secoes = document.getElementById("secoes");
                const novaSecao = `
                    <div class="secao">
                        <h3>Seção: <input type="text" name="secoes[${secaoIndex}][nome]" placeholder="Nome da seção"></h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>N/REF</th>
                                    <th>Designação</th>
                                    <th>Quantidade</th>
                                    <th>Descrição</th>
                                    <th>Preço Unitário</th>
                                    <th>Preço Total</th>
                                </tr>
                            </thead>
                            <tbody class="produtos"></tbody>
                        </table>
                        <button type="button" onclick="adicionarProduto(this)">Adicionar Produto</button>
                    </div>
                `;
                secoes.insertAdjacentHTML("beforeend", novaSecao);
            }
        </script>

    </div>
</body>

</html>
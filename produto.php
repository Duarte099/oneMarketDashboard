<?php 
    include('head.php'); 

    $estouEm = 4;

    if (adminPermissions($con, "adm_003", "view") == 0) {
        header('Location: dashboard.php');
        exit();
    }
?>
    <link rel="stylesheet" href="./css/stock.css">
    
    <title>OneMarket | Stock</title>
</head>

<body>
    <script>
        //PESQUISA PRODUTOS
        const productsSearchData = [];
                                
        $.ajax({
            url: 'json.obterProdutos.php',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                productsSearchData.push(...data);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao buscar os dados:', error);
            }
        }); 

        function productsSearch(searchBox) {
            const dataProduct = document.getElementById('bottom-data');
            const tbody = dataProduct.querySelector('table tbody');
            const query = searchBox.value.toLowerCase();
            tbody.innerHTML = "";

            const displayResults = (results) => {
                if (results.length > 0) {
                    results.forEach((result) => {
                        const row = document.createElement("tr");
                        row.style.cursor = "pointer";

                        // Coluna da Imagem - adaptada para ficar igual ao HTML original
                        const imgCell = document.createElement("td");
                        imgCell.setAttribute("data-label", "Img");

                        const profileDiv = document.createElement("div");
                        profileDiv.id = "profilePic";
                        profileDiv.style.width = "100%";
                        profileDiv.style.maxWidth = "500px";
                        profileDiv.style.borderRadius = "250px";
                        // Se houver imagem, define o background; caso contrário, fica transparente.
                        if (result.img && result.img.trim() !== "") {
                            profileDiv.style.background = `url('${result.img}') no-repeat center center`;
                            profileDiv.style.backgroundSize = "cover";
                        } else {
                            profileDiv.style.background = "transparent";
                        }

                        const fallbackImg = document.createElement("img");
                        fallbackImg.src = "./images/semfundo.png";
                        fallbackImg.style.width = "100%";
                        fallbackImg.style.paddingBottom = "13px";
                        profileDiv.appendChild(fallbackImg);
                        imgCell.appendChild(profileDiv);
                        row.appendChild(imgCell);

                        // Colunas adicionais
                        const id = document.createElement("td");
                        id.textContent = result.id;

                        const reference = document.createElement("td");
                        reference.textContent = result.reference;

                        const name = document.createElement("td");
                        name.textContent = result.name;

                        const value = document.createElement("td");
                        value.textContent = result.value;

                        const stock = document.createElement("td");
                        stock.textContent = result.stock;

                        // Adiciona todas as células à linha
                        row.appendChild(reference);
                        row.appendChild(name);
                        row.appendChild(value);
                        row.appendChild(stock);

                        // Adiciona a linha ao corpo da tabela
                        tbody.appendChild(row);
                    });
                } else {
                    // Adiciona uma linha dizendo "Sem resultados"
                    const row = document.createElement("tr");
                    const noResultsCell = document.createElement("td");
                    noResultsCell.textContent = "Sem resultados";
                    noResultsCell.colSpan = 9; // Atualiza para incluir todas as colunas
                    noResultsCell.style.textAlign = "center";

                    row.appendChild(noResultsCell);
                    tbody.appendChild(row);
                }
            };

            if (query) {
                const filteredResults = productsSearchData.filter(item =>
                    item.reference.toLowerCase().includes(query) ||
                    item.name.toLowerCase().includes(query) ||
                    item.value.toLowerCase().includes(query) ||
                    item.stock.toLowerCase().includes(query)
                );

                displayResults(filteredResults);
            } else {
                displayResults(productsSearchData);
            }
        }
    </script>

    <?php 
        include('sideBar.php'); 
    ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <?php 
            include('header.php'); 
        ?>          
        <!-- End of Navbar -->
        
        <main>
            <div class="header">
                <div class="left">
                    <h1>Stock de produtos</h1>
                    <div class="search-bar">
                        <input type="text" id="searchBox" placeholder="Pesquisar produtos..." oninput="productsSearch(this)" />
                    </div>
                </div>
                <?php if (adminPermissions($con, "adm_003", "inserir") == 1) { ?>
                    <a href="produtoCriar.php" id="new-product" class="report">
                        <i class='bx bx-plus'></i>
                        <span>Novo Produto</span>
                    </a>
                <?php } ?>
            </div>
            <div class="bottom-data" id="bottom-data">
                <div class="products">
                    <table>
                        <thead>
                            <tr>
                                <th>Img</th>
                                <th>Nome</th>
                                <th>Referencia</th>
                                <th>Valor</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $sql = "SELECT 
                                            product.id as id,
                                            product.img as imagem,  
                                            product.name as nome, 
                                            product.reference as refProduto, 
                                            product.value as valorProduto,
                                            product.active as ativo,
                                            product_stock.quantity as stockProduto
                                        FROM product
                                        LEFT JOIN product_stock ON product.id = product_stock.idProduct
                                        ORDER BY id DESC;";
                                $result = $con->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status = $row['ativo'] == 1 ? 'Ativo' : 'Inativo';
                                        echo "<tr onclick=\"handleRowClick('{$row['id']}', 'stock')\" style=\"cursor: pointer;\">
                                            <td>
                                                <div id=\"profilePic\" style=\"width:100%; max-width:500px; background: url('{$row['imagem']}') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover; border-radius: 250px;\">
                                                    <img src=\"./images/semfundo.png\" style=\"width:100%;padding-bottom: 13px;\">
                                                </div>
                                            </td>
                                            <td>{$row['nome']}</td>
                                            <td>{$row['refProduto']}</td>
                                            <td>" . number_format((float)$row['valorProduto'], 2, '.', '.') . "€</td>
                                            <td>{$row['stockProduto']}</td>
                                            <td>{$status}</td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>Sem registros para exibir.</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    
</body>

</html>
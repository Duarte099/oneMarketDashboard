<?php
    include('../db/conexao.php'); 
?>
<link rel="stylesheet" href="../css/sideBar.css">
<script src="../index.js" defer></script>

<!-- Sidebar -->
<div class="sidebar">
    <a href="../pages/dashboard.php" class="logo">
        <img src="../images/logoOnemarketBranco.png" id="logoImage">
    </a>
    <ul class="side-menu">
        <li class="<?php echo ($estouEm == 1) ? 'active' : ''; ?>">
            <a href="../pages/dashboard.php"><i class='bx bxs-dashboard'></i>Dashboard</a>
        </li>
        <?php if (adminPermissions("adm_001", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 2) ? 'active' : ''; ?>">
                <a href="../pages/orcamento.php"><i class='bx bx-calculator'></i>Orçamentos</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions("adm_002", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 3) ? 'active' : ''; ?>">
                <a href="../pages/fichaTrabalho.php"><i class='bx bx-file'></i>Fichas de Trabalho</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions("adm_003", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 4) ? 'active' : ''; ?>">
                <a href="../pages/produto.php"><i class='bx bx-package'></i>Stock</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions("adm_004", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 5) ? 'active' : ''; ?>">
                <a href="../pages/cliente.php"><i class='bx bx-group'></i>Clientes</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions("adm_005", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 6) ? 'active' : ''; ?>">
                <a href="../pages/admin.php"><i class='bx bx-shield'></i>Admin</a>
            </li>
        <?php } ?>
    </ul>
</div>

<!-- Botão Hambúrguer -->
<div class="hamburger-menu">
    <i class='bx bx-menu'></i>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
            const sidebar = document.querySelector(".sidebar");
            const hamburgerMenu = document.querySelector(".hamburger-menu");

            let isFullscreen = false; // Flag para verificar o estado da sidebar

            hamburgerMenu.addEventListener("click", () => {
                if (sidebar.classList.contains("hidden")) {
                    // Se estiver escondida, abrir como tela cheia
                    sidebar.classList.remove("hidden");
                    sidebar.classList.add("fullscreen");
                    hamburgerMenu.classList.add("active");
                    isFullscreen = true;
                } else if (isFullscreen) {
                    // Se estiver em tela cheia, voltar ao estado escondido
                    sidebar.classList.remove("fullscreen");
                    sidebar.classList.add("hidden");
                    hamburgerMenu.classList.remove("active");
                    isFullscreen = false;
                } else {
                    // Se não estiver escondida nem em tela cheia, esconder completamente
                    sidebar.classList.add("hidden");
                }
            });
        });

</script>
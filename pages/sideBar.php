<?php
    $auxLogin = true;

    include('../db/conexao.php'); 
?>

<link rel="stylesheet" href="../css/sideBar.css">

<div id="preloader">
    <img src="../images/LogoOnemarketPretoLetras.png" alt="Carregando...">
</div>

<div class="header">
    <button id="sidebarToggle" class="sidebar-toggle">
        <i class='bx bx-menu'></i>
    </button>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <a href="../pages/dashboard.php" class="logo">
        <img src="../images/logoOnemarketBranco.png" id="logoImage">
    </a>
    <ul class="side-menu">
        <li class="<?php echo ($estouEm == 1) ? 'active' : ''; ?>">
            <a href="../pages/dashboard.php"><i class='bx bxs-dashboard'></i>Dashboard</a>
        </li>
        <?php if (adminPermissions($con, "adm_001", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 2) ? 'active' : ''; ?>">
                <a href="../pages/orcamento.php"><i class='bx bx-calculator'></i>Orçamentos</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions($con, "adm_002", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 3) ? 'active' : ''; ?>">
                <a href="../pages/fichaTrabalho.php"><i class='bx bx-file'></i>Fichas de Trabalho</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions($con, "adm_003", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 4) ? 'active' : ''; ?>">
                <a href="../pages/produto.php"><i class='bx bx-package'></i>Stock</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions($con, "adm_004", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 5) ? 'active' : ''; ?>">
                <a href="../pages/cliente.php"><i class='bx bx-group'></i>Clientes</a>
            </li>
        <?php } ?>
        <?php if (adminPermissions($con, "adm_005", "view") == 1) { ?>
            <li class="<?php echo ($estouEm == 6) ? 'active' : ''; ?>">
                <a href="../pages/admin.php"><i class='bx bx-shield'></i>Admin</a>
            </li>
        <?php } ?>
        <!-- Novas opções para mobile -->
        <li class="mobile-only">
            <a href="../pages/perfil.php"><i class='bx bx-user'></i>Perfil</a>
        </li>
        <li class="mobile-only">
            <a href="../pages/indexLogout.php"><i class='bx bx-log-out'></i>Logout</a>
        </li>
    </ul>
    <!-- Theme toggle para mobile -->
    <div class="theme-toggle mobile-only">
        <span class="icon moon">🌙</span>
        <label class="toggle">
            <input type="checkbox" id="theme-switch-mobile">
            <span class="slider"></span>
        </label>
        <span class="icon sun">☀️</span>
    </div>
</div>
<!-- Overlay Mobile -->
<div class="sidebar-overlay" onclick="closeSidebar()"></div>



<script>
// Função global para fechar sidebar
function closeSidebar() {
    document.querySelector('.sidebar').classList.remove('open');
}

</script>
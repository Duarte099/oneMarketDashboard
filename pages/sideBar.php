<?php
   include('../db/conexao.php'); 
?>

<link rel="stylesheet" href="../css/sideBar.css">
<script src="../index.js" defer></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const themeSwitch = document.getElementById('theme-switch-mobile');

    // Função para verificar se é mobile/tablet
    function isMobileOrTablet() {
        return window.innerWidth <= 768;
    }

    // Toggle do sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            if (isMobileOrTablet()) {
                sidebar.classList.toggle('open');
            }
        });
    }

    // Fechar o sidebar ao clicar em um link (apenas para mobile/tablet)
    const sidebarLinks = document.querySelectorAll('.sidebar .side-menu li a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (isMobileOrTablet()) {
                sidebar.classList.remove('open');
            }
        });
    });

    // Funcionalidade do theme switch
    if (themeSwitch) {
        themeSwitch.addEventListener('change', function() {
            document.documentElement.classList.toggle('light-mode', this.checked);
            localStorage.setItem('theme', this.checked ? 'light' : 'dark');
            const logoImage = document.querySelector('#logoImage');
            if (logoImage) {
                logoImage.src = this.checked ? '../images/LogoOnemarketPreto.png' : '../images/LogoOnemarketBranco.png';
            }
        });

        // Carregar tema salvo
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            document.documentElement.classList.add('light-mode');
            themeSwitch.checked = true;
            const logoImage = document.querySelector('#logoImage');
            if (logoImage) {
                logoImage.src = '../images/LogoOnemarketPreto.png';
            }
        }
    }

    // Ajustar o sidebar ao redimensionar a janela
    window.addEventListener('resize', function() {
        if (!isMobileOrTablet()) {
            sidebar.classList.remove('open');
        }
    });
});

</script>
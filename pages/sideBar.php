<?php
   // include('../db/conexao.php'); 
?>
<link rel="stylesheet" href="../css/sideBar.css">
<script src="../index.js" defer></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<body>
    <div class="header">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class='bx bx-menu'></i>
        </button>
    </div>

</body>
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



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });

        // Fechar o sidebar ao clicar em um link (opcional)
        const sidebarLinks = document.querySelectorAll('.sidebar .side-menu li a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('open');
                }
            });
        });
    });


</script>
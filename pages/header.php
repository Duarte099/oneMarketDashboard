<?php
    include('../db/conexao.php');

    $idAdmin = $_SESSION['id'];

    $sql = "SELECT img FROM administrator WHERE administrator.id = $idAdmin;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $img =  $row['img'];
    }
?>

<link rel="stylesheet" href="../css/header.css">

<nav>
    <div class="profile-container" style="position: relative;">
        <!-- Imagem do perfil que abre/fecha o modal -->
        <a class="profile" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <div class="theme-toggle">
                <span class="icon moon">🌙</span>
                <label class="toggle">
                    <input type="checkbox" id="theme-switch">
                    <span class="slider"></span>
                </label>
                <span class="icon sun">☀️</span>
            </div>
            <img src="<?php echo $img ?>" alt="Foto de Perfil" class="profile-img">
        </a>

        <!-- Modal -->
        <div id="profileModal" class="profileModal">
            <div class="modal-content">
                <button id="closeModalBtn" class="close-btn"> &times; </button>
                <!-- Seção superior com foto e nome -->
                <div class="user-info" style="display: flex; align-items: center; padding: 10px 20px;">
                    <img src="<?php echo $img ?>" alt="Foto de Perfil" class="profile-img" style="width: 50px; height: 50px; margin-right: 10px;">
                    <span class="user-name" style="font-size: 16px; font-weight: bold;"><?=$_SESSION['name']?></span>
                </div>
                <hr style="border: none; border-top: 1px solid #333; margin: 10px 0;">

                <!-- Opções -->
                <a href="../pages/perfil.php" class="menu-option">Perfil</a>
                <a href="../pages/indexLogout.php" class="menu-option">Logout</a>
            </div>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileModal = document.getElementById('profileModal');
        const profileLink = document.querySelector('.profile'); // Seleciona o link da foto
        const closeModalBtn = document.getElementById('closeModalBtn');

        // Função para abrir o modal
        window.openModal = function() {
            profileModal.style.display = 'block';
        };

        // Função para fechar o modal
        function closeModal() {
            profileModal.style.display = 'none';
        }

        // Adiciona evento ao botão de fechar
        closeModalBtn.addEventListener('click', () => {
            closeModal();
        });

        // Adiciona evento de clique no link ou imagem da foto
        if (profileLink) {
            profileLink.addEventListener('click', function(event) {
                const profileModal = document.getElementById('profileModal');
                
                // Alterna a exibição do modal
                if (profileModal.style.display === 'block') {
                    profileModal.style.display = 'none'; // Fecha o modal se ele estiver aberto
                } else {
                    profileModal.style.display = 'block'; // Abre o modal se ele estiver fechado
                }
            });
        }

        const themeSwitch = document.querySelector('#theme-switch');
        const logoImage = document.querySelector('#logoImage');

        // Carregar a preferência de tema do localStorage
        const savedTheme = localStorage.getItem('theme'); // Obter o tema armazenado
        if (savedTheme === 'light') {
            document.documentElement.classList.add('light-mode');
            themeSwitch.checked = true;
            logoImage.src = '../images/LogoOnemarketPreto.png';
        } else {
            document.documentElement.classList.remove('light-mode');
            themeSwitch.checked = false;
            logoImage.src = '../images/LogoOnemarketBranco.png';
        }

        // Alternar tema e salvar a preferência no localStorage
        themeSwitch.addEventListener('change', () => {
            if (themeSwitch.checked) {
                document.documentElement.classList.add('light-mode'); // Ativar modo claro
                logoImage.src = '../images/LogoOnemarketPreto.png';
                localStorage.setItem('theme', 'light'); // Salvar preferência
            } else {
                document.documentElement.classList.remove('light-mode'); // Ativar modo escuro
                logoImage.src = '../images/LogoOnemarketBranco.png';
                localStorage.setItem('theme', 'dark'); // Salvar preferência
            }
        });
    });
</script>

<?php
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }
    else {
        $idAdmin = $_SESSION['id'];

        $sql = "SELECT img FROM administrator WHERE administrator.id = $idAdmin;";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $img =  $row['img'];
        }
    }
?>

<link rel="stylesheet" href="../css/header.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- Header para desktop -->
<nav class="desktop-header">
    <div class="profile-container" style="position: fixed;">
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
            <img src="<?php echo $img ?>" class="profile-img">
        </a>

        <!-- Modal -->
        <div id="profileModal" class="profileModal">
            <div class="modal-content">
                <button id="closeModalBtn" class="close-btn"> &times; </button>
                <!-- Seção superior com foto e nome -->
                <div class="user-info" style="display: flex; align-items: center; padding: 10px 20px;">
                    <img src="<?php echo $img ?>" class="profile-img" style="width: 50px; height: 50px; margin-right: 10px;">
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

<!-- Header para mobile -->
<div class="mobile-header">
    <div class="profile-container">
        <a class="profile" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
            <div class="theme-toggle">
                <span class="icon moon">🌙</span>
                <label class="toggle">
                    <input type="checkbox" id="theme-switch-mobile">
                    <span class="slider"></span>
                </label>
                <span class="icon sun">☀️</span>
            </div>
            <img src="<?php echo $img ?>" class="profile-img">
        </a>
        <!-- Modal para mobile -->
        <div id="profileModalMobile" class="profileModal">
            <div class="modal-content">
                <button id="closeMobileModalBtn" class="close-btn"> &times; </button>
                <div class="user-info" style="display: flex; align-items: center; padding: 10px 20px;">
                    <img src="<?php echo $img ?>" class="profile-img" style="width: 50px; height: 50px; margin-right: 10px;">
                    <span class="user-name" style="font-size: 16px; font-weight: bold;"><?=$_SESSION['name']?></span>
                </div>
                <hr style="border: none; border-top: 1px solid #333; margin: 10px 0;">
                <a href="../pages/perfil.php" class="menu-option">Perfil</a>
                <a href="../pages/indexLogout.php" class="menu-option">Logout</a>
            </div>
        </div>
    </div>
</div>


<script>
    const header = document.querySelector('.content nav');
    const mobileHeader = document.querySelector('.mobile-header');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('transparent');
        } else {
            header.classList.remove('transparent');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const profileModal = document.getElementById('profileModal');
        const profileModalMobile = document.getElementById('profileModalMobile');
        const profileLink = document.querySelector('.desktop-header .profile');
        const profileLinkMobile = document.querySelector('.mobile-header .profile');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const closeMobileModalBtn = document.getElementById('closeMobileModalBtn');

        function toggleModal(modal) {
            modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
        }

        if (profileLink) {
            profileLink.addEventListener('click', () => toggleModal(profileModal));
        }

        if (profileLinkMobile) {
            profileLinkMobile.addEventListener('click', () => toggleModal(profileModalMobile));
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => profileModal.style.display = 'none');
        }

        if (closeMobileModalBtn) {
            closeMobileModalBtn.addEventListener('click', () => profileModalMobile.style.display = 'none');
        }

        const themeSwitch = document.querySelector('#theme-switch');
        const themeSwitchMobile = document.querySelector('#theme-switch-mobile');
        const logoImage = document.querySelector('#logoImage');

        function setTheme(isLight) {
            document.documentElement.classList.toggle('light-mode', isLight);
            logoImage.src = isLight ? '../images/LogoOnemarketPreto.png' : '../images/LogoOnemarketBranco.png';
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        }

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            setTheme(true);
            themeSwitch.checked = true;
            themeSwitchMobile.checked = true;
        }

        themeSwitch.addEventListener('change', () => setTheme(themeSwitch.checked));
        themeSwitchMobile.addEventListener('change', () => setTheme(themeSwitchMobile.checked));

        // Controle do header móvel
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle && mobileHeader) {
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    mobileHeader.style.display = mobileHeader.style.display === 'block' ? 'none' : 'block';
                }
            });
        }
    });
</script>

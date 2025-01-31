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
            <div class="profile-img" id="profilePic" style="width:40px; max-width:500px; background: url('<?php echo $img; ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                <img src="../images/semfundo.png" style="width:100%;padding-bottom: 13px;">
            </div>
        </a>

        <!-- Modal -->
        <div id="profileModal" class="profileModal">
            <div class="modal-content">
                <button id="closeModalBtn" class="close-btn"> &times; </button>
                <!-- Seção superior com foto e nome -->
                <div class="user-info" style="display: flex; align-items: center; padding: 10px 20px;">
                <div class="profile-img" id="profilePic" style="width:40px; max-width:500px; background: url('<?php echo $img; ?>') no-repeat center center; -webkit-background-size: cover;   -moz-background-size: cover;   -o-background-size: cover;   background-size: cover; border-radius: 250px;">
                    <img src="../images/semfundo.png" style="width:100%;padding-bottom: 13px;">
                </div>
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
        const profileLink = document.querySelector('.desktop-header .profile');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const themeSwitch = document.querySelector('#theme-switch');
        const logoImage = document.querySelector('#logoImage');

        function toggleModal(modal) {
            modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
        }

        if (profileLink) {
            profileLink.addEventListener('click', () => toggleModal(profileModal));
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => profileModal.style.display = 'none');
        }

        function setTheme(isLight) {
            document.documentElement.classList.toggle('light-mode', isLight);
            if (logoImage) {
                logoImage.src = isLight ? '../images/LogoOnemarketPreto.png' : '../images/LogoOnemarketBranco.png';
            }
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
        }

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'light') {
            setTheme(true);
            themeSwitch.checked = true;
        }

        themeSwitch.addEventListener('change', () => setTheme(themeSwitch.checked));
    });
</script>

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
<script>localStorage.removeItem('modalOpen');</script>

<nav>
    <div class="profile-container" style="position: relative;">
    <!-- Imagem do perfil que abre/fecha o modal -->
    <a class="profile" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
        <img src="<?php echo $img ?>" alt="Foto de Perfil" class="profile-img">
    </a>

    <!-- Modal -->
    <div id="budgetModal" class="modal">
        <div class="modal-content">
        <!-- Seção superior com foto e nome -->
        <div class="user-info" style="display: flex; align-items: center; padding: 10px 20px;">
            <img src="<?php echo $img ?>" alt="Foto de Perfil" class="profile-img" style="width: 50px; height: 50px; margin-right: 10px;">
            <span class="user-name" style="font-size: 16px; font-weight: bold;">Nome do Usuário</span>
        </div>
        <hr style="border: none; border-top: 1px solid #333; margin: 10px 0;">

        <!-- Opções -->
        <a href="#" class="menu-option">Ver Perfil</a>
        <a href="#" class="menu-option">Alterar Tema</a>
        </div>
    </div>
    </div>
</nav>

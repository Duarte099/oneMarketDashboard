<?php 
    include('../db/conexao.php'); 
    $estouEm = 1;

    session_start();

    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: index.php');
        exit();
    }

    $idAdmin = $_SESSION['id'];

    $sql = "SELECT name, email, user, img, birthday FROM administrator WHERE administrator.id = $idAdmin;";
    $result = $con->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name =  $row['name'];
        $email =  $row['email'];
        $user =  $row['user'];
        $img =  $row['img'];
        $birthday =  $row['birthday'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Perfil</title>
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
                    <h1>Perfil</h1>
                </div>
            </div>
            <div class="form-container">
                <form action="../pages/inserirPerfil.php" id="profileForm" method="post" enctype="multipart/form-data">
                    <div class="column-left">
                        <label for="photo">Foto de perfil:</label>
                        <img src="<?php echo $img; ?>" alt="Profile Picture" id="profilePic">
                        <input type="file" name="photo" id="photo" oninput="displayProfilePic()">
                    </div>
                    <div class="column-right">
                        <label for="name">Nome:</label>
                        <input type="text" name="name" id="name" value="<?php echo $name; ?>">

                        <label for="user">Nome de utilizador:</label>
                        <input type="text" name="user" id="user" value="<?php echo $user; ?>">

                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?php echo $email; ?>">

                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" placeholder="Nova password">
                        <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Confirmar nova password">

                        <label for="birthday">Data nascimento:</label>
                        <input type="date" name="birthday" id="birthday" value="<?php echo $birthday; ?>">

                        <button type="submit" onclick="return validarPass()">Guardar alterações</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../index.js"></script>
    <script>
        function validarPass() {
            const pass = document.querySelector('input[name="password"]');
            const passC = document.querySelector('input[name="passwordConfirm"]');

            // Verifica se as senhas são diferentes
            if (pass.value !== passC.value) {
                passC.setCustomValidity("As palavras-passe não coincidem!");
                passC.reportValidity();
                return false;
            } else {
                passC.setCustomValidity("");
                return true;
            }
        }
    </script>
</body>

</html>
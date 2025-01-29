<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="icon" href="../images/IconOnemarketBranco.png">
    <title>OneMarket | Login</title>
<link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <div class="login-form">
        <h1></h1>
        <div class="container">
            <div class="main">
                <div class="content">
                    <h2>Login</h2>
                    <p><?php
                        // echo $passwordHash = password_hash("123456", PASSWORD_DEFAULT);
                    ?></p>
                    <form action="indexLogin.php" method="post">
                        <input type="text" name="username" placeholder="Username" id="username" required>
                        <div class="password-container">
                            <input type="password" name="password" placeholder="Password" id="password" required>
                            <i class='bx bx-hide toggle-password' id="togglePassword"></i>
                        </div>
                        <button class="btn" type="submit">Login</button>
                        <div class="error-message" id="errorMessage">
                            <?php 
                                if (isset($_GET['erro'])) {
                                    echo $_GET['erro'];
                                }
                            ?>
                        </div>
                    </form>
                </div>
                <div class="form-img">
                    <img src="../images/LogoOnemarketBrancoLetras.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            // Alterna o tipo de input entre "password" e "text"
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Alterna os ícones entre "bx-hide" e "bx-show"
            this.classList.toggle('bx-hide');
            this.classList.toggle('bx-show');
        });
    </script>
</body>
</html>
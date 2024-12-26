<?php
    session_start();
    include("../db/conexao.php");

    $_SESSION['errorMessage'] = "";

    // Now we check if the data from the login form was submitted, isset() will check if the data exists.
    if ( !isset($_POST['username'], $_POST['password']) ) {
        // Could not get the data that should have been sent.
        exit('Please fill both the username and password fields!');
    }
    // Prepare our SQL, preparing the SQL statement will prevent SQL injection.
    if ($stmt = $con->prepare('SELECT id, pass, name as nomeX FROM administrator WHERE user = ? OR email = ?')) {
        // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
        $stmt->bind_param('ss', $_POST['username'], $_POST['username']);
        $stmt->execute();
        // Store the result so we can check if the account exists in the database.
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $password, $nomeX);
            $stmt->fetch();
            // Account exists, now we verify the password.
            // Note: remember to use password_hash in your registration file to store the hashed passwords.
            if (password_verify($_POST['password'], $password)) {
                // Verification success! User has logged-in!
                // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $nomeX;
                $_SESSION['id'] = $id;
                header('Location: ../pages/dashboard.php');
                exit();
            } else {
                $_SESSION['errorMessage'] = "Password ou user incorreto!";
                header('Location: ../pages/index.php');
            }
        } else {
             $_SESSION['errorMessage'] = "Password ou user incorreto!";
             header('Location: ../pages/index.php');
        }

        $stmt->close();
    }
?>
        
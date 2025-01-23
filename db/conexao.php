<?php
    // Change this to your connection info.
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'onemarket';
    // Try and connect using the info above.
    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
    // if ( mysqli_connect_errno() ) {
    //     // If there is an error with the connection, stop the script and display the error.
    //     $error_message = 'Failed to connect to MySQL: ' . mysqli_connect_error();
    //     echo "<script>console.error(" . json_encode($error_message) . ");</script>";
    //     exit;
    // }
    // echo "<script>console.error(" . json_encode("aaaaaaa") . ");</script>";
    // exit;
    

    if (!isset($auxLogin)) $auxLogin = false;
    
    if (isset($auxLogin) && !$auxLogin) {
        $sql = "SELECT * FROM administrator WHERE id = " . $_SESSION['id'] . " AND pass = '" . $_SESSION['password'] . "';";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            
        }
        else {
            header('Location: index.php');
            exit();
        }
    }
    include_once 'functions.php';
?>  
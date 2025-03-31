<?php 

if (!isset($_SESSION)) {
    session_start();
}

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["Email"])) {
    header("Location: auth/login.php");
    exit;
}

require_once '../includes/navigation.php';

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Squito</title>
    </head>
</html>
<body>

    <h1>My First PHP Page</h1>
    <?php
        echo "Welcome to Squito!";
    ?>
</body>
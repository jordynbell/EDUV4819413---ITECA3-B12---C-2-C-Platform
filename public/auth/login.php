<?php

require_once __DIR__ . '/../../lib/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Email = $_POST['email'];
    $Password = $_POST['password'];
    $stmt = $conn->prepare("SELECT Password FROM User WHERE Email = ?");
    $stmt->bind_param("s", $Email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();
        if ($Password === $db_password) {
            session_start();
            $_SESSION['Email'] = $Email;
            header("Location: ../index.php");
        } else {
            echo "Invalid email or password";
        }
    } else {
        header("Location: register.php");
        echo "Invalid email or password";
    }
}
?>

<!DOCTYPE html>

<html>

<head>
    <title>Squito Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div>
        <h1>Squito Login</h1>
        <form action="" method="post">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            <input type="submit" value="Login" class="btn btn-primary">
        </form>

    </div>
</body>

</html>
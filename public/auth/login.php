<?php

require_once __DIR__ . '/../../lib/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT password FROM User WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();
        if (password_verify($password, $db_password)) {
            session_start();
            $_SESSION['Email'] = $email;
            $_SESSION['User_ID'] = $conn->query("SELECT user_id FROM user WHERE email = '$email'")->fetch_assoc()['user_id'];
            $_SESSION['Role'] = $conn->query("SELECT role FROM user WHERE email = '$email'")->fetch_assoc()['role'];
            header("Location: ../index.php");
        } else {
            echo "Invalid email or password";
        }
    } else {
        echo "Invalid email or password.";
    }
}
$conn->close();
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
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>

    </div>

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
<?php

require_once __DIR__ . '/../../lib/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO User (Name, Surname, Email, Password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $surname, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
</head>

<body>
    <form action="" method="post">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required>
        <label for="surname">Surname</label>
        <input type="text" name="surname" id="surname" required>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <label for="confirm_password">Re-enter Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <input type="submit" value="Register">
    </form>
</body>

</html>
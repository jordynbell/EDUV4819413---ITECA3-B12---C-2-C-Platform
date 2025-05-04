<?php

require_once __DIR__ . '/../../lib/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = "Normal";

    if ($password !== $confirm_password) {
        echo "Passwords do not match";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO User (name, surname, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $surname, $email, $hashed_password, $role);

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
    <title>Register - Squito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link href="/C2CPlatform/public/assets/css/site.css" rel="stylesheet">
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

    <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
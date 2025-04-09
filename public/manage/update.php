<?php

require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../includes/navigation.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["Email"])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_data = null;

if (isset($_POST["user_id"])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare('SELECT user_id, name, surname, email, role FROM user WHERE user_id = ?');
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    } else {
        echo "Could not load the user's information.";
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update user</title>
</head>

<body>
    <?php if ($user_data): ?>
        <form action="" method="post">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_data["user_id"]; ?>" required>

            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo $user_data["name"]; ?>" required> </br>

            <label for="surname">Surname</label>
            <input type="text" name="surname" id="surname" value="<?php echo $user_data["surname"]; ?>" required> </br>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $user_data["email"]; ?>" required> </br>

            <label for="role">Role</label>
            <input type="role" name="role" id="role" value="<?php echo $user_data["role"]; ?>" required> </br>

        </form>
    <?php else: ?>
        <p>An error occurred</p>
    <?php endif; ?>
    
</body>

</html>
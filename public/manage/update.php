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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    if (isset($_POST['loadUpdate'])) {
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
    else if (isset($_POST['saveUpdate']))
    {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $stmt = $conn->prepare('UPDATE user SET name = ?, surname = ?, email = ?, role = ? WHERE user_id = ?');
        $stmt->bind_param("ssssi", $name, $surname, $email, $role, $user_id);
        if ($stmt->execute())
        {
            header("Location: users.php");
            exit;
        }
        else
        {
            echo "Failed to update user.";
        }

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
        <form action="update.php" method="post">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_data["user_id"]; ?>" required>

            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo $user_data["name"]; ?>" required> </br>

            <label for="surname">Surname</label>
            <input type="text" name="surname" id="surname" value="<?php echo $user_data["surname"]; ?>" required> </br>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $user_data["email"]; ?>" required> </br>

            <label for="role">Role</label>
            <input type="role" name="role" id="role" value="<?php echo $user_data["role"]; ?>" required> </br>

            <button type="submit" name="saveUpdate">Save</button>

        </form>
    <?php else: ?>
        <p>An error occurred</p>
    <?php endif; ?>

    <a href="users.php">Return to previous page</a>

</body>

</html>
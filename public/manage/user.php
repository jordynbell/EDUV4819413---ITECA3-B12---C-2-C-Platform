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

if ($_SESSION['Role'] != 'Admin') {
    header("Location: ../index.php");
    exit;
}

$stmt = $conn->prepare('SELECT * FROM user');
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']))
{
    $user_id = $_POST["user_id"];
    $stmt = $conn->prepare('DELETE FROM user WHERE user_id = ?');
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete']))
{
    $user_id = $_POST["user_id"];
    $stmt = $conn->prepare('DELETE FROM user WHERE user_id = ?');
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
</head>

<body>
<h1>Users</h1>
<table border="1">
    <th>ID</th>
<th>Name</th>
<th>Surname</th>
<th>Email</th>
<th>Role</th>
<th colspan="2">Actions</th>

<!-- Try thissssssssssssssssssssssssss -->
 <!-- echo ' ' with form elements inbetween. -->
<?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["user_id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["surname"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["role"] . "</td>";
        if ($user_id != $row["user_id"])
        {
            echo "<td><form action='' method='POST'><input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'><input type='submit' name ='delete' value='Delete'></form></td>";
        }
        else
        {
            echo "<td></td>";
        }
        echo "<td></td>"; // Try input button, javascript to enable inputs, then click save button to post.
        echo "</tr>";
    }
    ?>

</table>
</body>

</html>
<?php

require_once __DIR__ . '/../../lib/db.php';

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

$pageTitle = "Users - Squito";

$stmt = $conn->prepare('SELECT * FROM user');
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $user_id = $_POST["user_id"];
    $stmt = $conn->prepare('DELETE FROM user WHERE user_id = ?');
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

require_once __DIR__ . '/../../includes/header.php';

?>

<h1>Users</h1>
<table border="1">
    <th>ID</th>
<th>Name</th>
<th>Surname</th>
<th>Email</th>
<th>Role</th>
<th colspan="2">Actions</th>

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
        echo "<td><form action='update.php' method='POST'><input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'><button type='submit' name='loadUpdate'>Update</button></form></td>";
        echo "</tr>";
    }
    $stmt->close();
    ?>

</table>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
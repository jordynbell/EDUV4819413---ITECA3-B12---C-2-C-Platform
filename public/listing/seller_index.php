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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $seller_id = $_SESSION['User_ID'];

    $stmt = $conn->prepare('DELETE FROM product WHERE product_id = ? AND seller_id = ?');
    $stmt->bind_param("ii", $product_id, $seller_id);
    if (!$stmt->execute())
    {
        echo "Error: " / $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Listings</title>
</head>

<body>
    <h1>Seller Listings</h1>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Category</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php

        $seller_id = $_SESSION["User_ID"];
        $stmt = $conn->prepare('SELECT product_id, title, description, category, price FROM product WHERE seller_id = ?');
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
            echo "<td>R" . htmlspecialchars($row['price']) . "</td>";
            echo "<td><form action='' method='POST'><input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id']) . "'><input type='submit' value='Delete'></form></td>";
            echo "</tr>";
        }
        ?>
    </table>
    <br>
</body>

</html>
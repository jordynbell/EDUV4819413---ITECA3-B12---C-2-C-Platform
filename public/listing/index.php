<?php

require_once __DIR__ . '/../../lib/db.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["Email"])) {
    header("Location: ../auth/login.php");
    exit;
}

$pageTitle = "View Listings - Squito";

$stmt = $conn->prepare('SELECT product_id, title, description, category, price, status FROM product WHERE status = "Active" AND seller_id != ?');
$stmt->bind_param("i", $_SESSION['User_ID']);
$stmt->execute();
$result = $stmt->get_result();

require_once __DIR__ . '/../../includes/header.php';

?>

    <h1>View Listings</h1>
    <table border="1">
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Category</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
            echo "<td>R " . htmlspecialchars($row['price']) . "</td>";
            echo "<td><form action='../order/create.php' method='POST'><input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id']) . "'><button type='submit'>Order</button></form></td>";
            echo "</tr>";
        }
        ?>
    </table>
    <br>
<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
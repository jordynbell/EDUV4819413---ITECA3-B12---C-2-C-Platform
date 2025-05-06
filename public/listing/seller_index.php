<?php

require_once __DIR__ . '/../../lib/db.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["Email"])) {
    header("Location: ../auth/login.php");
    exit;
}

$pageTitle = "Seller Listings - Squito";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $seller_id = $_SESSION['User_ID'];

    $stmt = $conn->prepare('DELETE FROM product WHERE product_id = ? AND seller_id = ?');
    $stmt->bind_param("ii", $product_id, $seller_id);
    if (!$stmt->execute()) {
        echo "Error: " / $stmt->error;
    }
}

require_once __DIR__ . '/../../includes/header.php';

?>

<div class="container">
    <h1 class="text-center">Seller Listings</h1>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered" border="1">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php

            $seller_id = $_SESSION["User_ID"];
            $stmt = $conn->prepare('SELECT product_id, title, description, category, price, status FROM product WHERE seller_id = ?');
            $stmt->bind_param("i", $seller_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                echo "<td>R " . htmlspecialchars($row['price']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td><form action='' method='POST'><input type='hidden' name='product_id' value='" . htmlspecialchars($row['product_id']) . "'><button type='submit' class='btn btn-primary'>Delete</button></form></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
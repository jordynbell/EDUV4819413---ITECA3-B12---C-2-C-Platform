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

$product_data = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;

    if ($product_id) {

        $stmt = $conn->prepare('SELECT product_id, product.title, product.description, product.category, product.price, product.status, product.seller_id, user.name, user.surname FROM product INNER JOIN user ON product.seller_id = user.user_id WHERE product.product_id = ?');
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_data = $result->fetch_assoc();
        $stmt->close();
        
        if (isset($_POST['action']) && $_POST['action'] == 'confirm') {
            $order_date = (new DateTime('now', new DateTimeZone('GMT+2')))->format('Y-m-d H:i:s');
            $price = $product_data['price'];
            $status = 'Ordered';
            $customer_id = $_SESSION['User_ID'];

            $insert_stmt = $conn->prepare('INSERT INTO `order` (order_date, price, status, customer_id, product_id) VALUES(?,?,?,?,?)');
            $insert_stmt->bind_param("sdsii", $order_date, $price, $status, $customer_id, $product_id);

            if ($insert_stmt->execute()) {
                $order_id = $insert_stmt->insert_id;
                $insert_stmt->close();

                header("Location: ../payment/create.php?order_id=$order_id&price=$price&product_id=$product_id");
                exit;
            } else {
                echo "Failed to place order: " . $insert_stmt->error;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Product</title>
</head>

<body>
    <?php if ($product_data): ?>
        <h1>Order Details</h1>
        <table border="1">
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Seller Name</th>
                <th>Seller Surname</th>
            </tr>
            <?php
            echo "<tr>";
            echo "<td>" . htmlspecialchars($product_data['title']) . "</td>";
            echo "<td>" . htmlspecialchars($product_data['description']) . "</td>";
            echo "<td>" . htmlspecialchars($product_data['category']) . "</td>";
            echo "<td>R " . htmlspecialchars($product_data['price']) . "</td>";
            echo "<td>" . htmlspecialchars($product_data['status']) . "</td>";
            echo "<td>" . htmlspecialchars($product_data['name']) . "</td>";
            echo "<td>" . htmlspecialchars($product_data['surname']) . "</td>";
            echo "</tr>";
            ?>
        </table>
        <form action="" method="post">
            <input type="hidden" name="action" value="confirm">
            <input type="hidden" name="product_id" value="<?php echo $product_data['product_id'] ?>">
            <input type="hidden" name="price" value="<?php echo $product_data['price'] ?>">
            <button type="submit">Confirm Order</button>
        </form>
    <?php else: ?>
        <p>No product selected.</p>
    <?php endif; ?>
</body>

</html>
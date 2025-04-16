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

$user_id = $_SESSION['User_ID'];

$stmt=$conn->prepare('SELECT * FROM `order` WHERE customer_id = ?');
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My orders</title>
</head>
<body>
    
    <h1>My Orders</h1>
    <table border="1">
        <tr>
            <th>Order Number</th>
            <th>Order Date</th>
            <th>Price</th>
            <th>Status</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo $order['order_date']; ?></td>
                <td><?php echo 'R ' . $order['price']; ?></td>
                <td><?php echo $order['status']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
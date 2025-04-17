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

$stmt = $conn->prepare('SELECT * FROM shipment');
$stmt->execute();
$result = $stmt->get_result();
$shipments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipments</title>
</head>
<body>
    
    <h1>Shipments</h1>
    <table border="1">
        <tr>
            <th>Shipment ID</th>
            <th>Product ID</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Date Shipped</th>
        </tr>
        <?php foreach ($shipments as $shipment): ?>
            <tr>
                <td><?php echo htmlspecialchars($shipment['shipment_id']); ?></td>
                <td><?php echo htmlspecialchars($shipment['product_id']); ?></td>
                <td><?php echo htmlspecialchars($shipment['quantity']); ?></td>
                <td><?php echo htmlspecialchars($shipment['status']); ?></td>
                <td><?php echo htmlspecialchars($shipment['date_shipped']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
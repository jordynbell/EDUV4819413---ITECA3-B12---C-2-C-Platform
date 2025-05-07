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

$pageTitle = "Shipments - Squito";

$stmt = $conn->prepare('SELECT * FROM shipment');
$stmt->execute();
$result = $stmt->get_result();
$shipments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require_once __DIR__ . '/../../includes/header.php';

?>
<div class="container">
    <h1 class="text-center">Shipments</h1>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered" border="1">
            <tr>
                <th>Shipment ID</th>
                <th>Delivery Method</th>
                <th>Status</th>
                <th>Order ID</th>
                <th>Address ID</th>
                <th>Date Shipped</th>
            </tr>
            <?php foreach ($shipments as $shipment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($shipment['shipment_id']); ?></td>
                    <td><?php echo htmlspecialchars($shipment['delivery_method']); ?></td>
                    <td><?php echo htmlspecialchars($shipment['delivery_status']); ?></td>
                    <td><?php echo htmlspecialchars($shipment['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($shipment['address_id']); ?></td>
                    <td><?php echo htmlspecialchars($shipment['shipment_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
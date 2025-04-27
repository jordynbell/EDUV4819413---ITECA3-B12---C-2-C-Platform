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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
</head>
<body>
    
    <h1>Sales</h1>
    <table border="1">
        <tr>
            <th>Sale ID</th>
            <th>Product ID</th>
            <th>Total Price</th>
            <th>Date Sold</th>
        </tr>
        <?php
        $stmt = $conn->prepare('SELECT * FROM sale');
        $stmt->execute();
        $result = $stmt->get_result();
        $sales = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        foreach ($sales as $sale): ?>
            <tr>
                <td><?php echo htmlspecialchars($sale['sale_id']); ?></td>
                <td><?php echo htmlspecialchars($sale['product_id']); ?></td>
                <td><?php echo htmlspecialchars("R " . $sale['price']); ?></td>
                <td><?php echo htmlspecialchars($sale['date_sold']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
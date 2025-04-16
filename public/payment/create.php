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

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;
$amount = isset($_POST['price']) ? $_POST['price'] : null;
$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $payment_date = (new DateTime('now', new DateTimeZone('GMT+2')))->format('Y-m-d H:i:s');
    if ($order_id <= 0) {
        header("Location: ../order/index.php");
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] == 'confirm') {
        $stmt = $conn->prepare('INSERT INTO payment (order_id, payment_date, amount) VALUES (?, ?, ?)');
        $stmt->bind_param("isi", $order_id, $payment_date, $amount);

        if ($stmt->execute()) {
            $stmt->close();

            $update_stmt = $conn->prepare('UPDATE `order` SET status = ? WHERE order_id = ?');
            $new_status = 'Paid';
            $update_stmt->bind_param("si", $new_status, $order_id);
            if ($update_stmt->execute())
            {
                $update_stmt->close();
            } else {
                echo "Failed to update order status: " . $update_stmt->error;
            }

            $update_stmt = $conn->prepare('UPDATE product SET status = ? WHERE product_id = ?');
            $new_status = 'Sold';
            $update_stmt->bind_param("si", $new_status, $product_id);
            if ($update_stmt->execute())
            {
                $update_stmt->close();
            } else {
                echo "Failed to update product status: " . $update_stmt->error;
            }

            $shipment_stmt = $conn->prepare('UPDATE shipment SET delivery_status = ? where order_id = ?');
            $shipment_status = 'Shipped';
            $shipment_stmt->bind_param("si", $shipment_status, $order_id);
            if ($shipment_stmt->execute())
            {
                $shipment_stmt->close();
            } else {
                echo "Failed to update shipment status: " . $shipment_stmt->error;
            }

            header("Location: ../order/index.php");
        } else {
            echo "Failed to process payment: " . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make payment</title>
</head>
<body>
    
<form action="" method="post">
    <h1>Make Payment</h1>
    <label for="price">Total:</label>
    <input type="text" name="price" id="price" value="<?php echo $amount ?>" readonly><br>

    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    <input type="hidden" name="price" value="<?php echo $amount; ?>">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
    <input type="hidden" name="action" value="confirm">

    <input type="submit" value="Confirm Payment">
</form>

</body>
</html>
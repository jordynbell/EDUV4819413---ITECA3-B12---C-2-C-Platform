<?php

require_once __DIR__ . '/../../lib/db.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["Email"])) {
    header("Location: ../auth/login.php");
    exit;
}

$pageTitle = "Payment - Squito";

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
                header("Location: ../index.php");
            } else {
                echo "Failed to update product status: " . $update_stmt->error;
            }

            $shipment_stmt = $conn->prepare('UPDATE shipment SET delivery_status = ?, shipment_date = ? where order_id = ?');
            $shipment_status = 'Shipped';
            $shipment_date = (new DateTime('now', new DateTimeZone('GMT+2')))->format('Y-m-d H:i:s');
            $shipment_stmt->bind_param("ssi", $shipment_status, $shipment_date, $order_id);

            if ($shipment_stmt->execute())
            {
                $shipment_stmt->close();
            } else {
                echo "Failed to update shipment status: " . $shipment_stmt->error;
            }

            $sale_stmt = $conn->prepare('INSERT INTO sale (product_id, price, date_sold) VALUES (?, ?, ?)');
            $sale_stmt->bind_param("ids", $product_id, $amount, $payment_date);
            if ($sale_stmt->execute())
            {
                $sale_stmt->close();
            } else {
                echo "Failed to insert sale record: " . $sale_stmt->error;
            }

            header("Location: ../order/index.php");
        } else {
            echo "Failed to process payment: " . $stmt->error;
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';

?>
    
<form action="" method="post">
    <h1>Make Payment</h1>

    <label for="Card Name"></label>
    <input type="text" name="Card Name" id="cardName" placeholder="John Doe" required>
    <br>
    
    <label for="Card Number"></label>
    <input type="text" name="Card Number" id="cardNumber" placeholder="1234 5678 9012 3456" required>
    <br>

    <label for="Expiry Date"></label>
    <input type="text" name="Expiry Date" id="expiryDate" placeholder="MM/YY" required>
    <br>

    <label for="CVV"></label>
    <input type="text" name="CVV" id="cvv" placeholder="123" required>
    <br>

    <label for="price">Total:</label>
    <input type="text" name="price" id="price" value="<?php echo $amount ?>" readonly><br>

    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
    <input type="hidden" name="price" value="<?php echo $amount; ?>">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
    <input type="hidden" name="action" value="confirm">

    <input type="submit" value="Confirm Payment">
</form>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
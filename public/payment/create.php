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

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : 0;
$amount = isset($_GET['price']) ? $_GET['price'] : null;
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

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
        } else {
            echo "Failed to process payment: " . $stmt->error;
        }

        $stmt->close();
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

    <label for="Card Name"></label>
    <input type="text" name="Card Name" id="cardName" placeholder="John Doe" required>

    <label for="price">Total:</label>
    <input type="text" name="price" id="price" value="<?php echo "R " . $amount ?>" readonly><br>

    <input type="hidden" name="action" value="confirm">
    <input type="submit" value="Confirm Payment">
</form>

</body>
</html>
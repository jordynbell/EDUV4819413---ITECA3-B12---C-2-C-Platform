<?php

require_once __DIR__ . '/../../lib/db.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["Email"])) {
    header("Location: ../auth/login.php");
    exit;
}

$pageTitle = "Create Order - Squito";

$user_id = $_SESSION['User_ID'];
$product_data = null;
$addresses = null;

$address_stmt = $conn->prepare('SELECT * FROM address WHERE user_id = ?');
$address_stmt->bind_param("i", $user_id);
$address_stmt->execute();
$address_result = $address_stmt->get_result();
$addresses = $address_result->fetch_all(MYSQLI_ASSOC);
$address_stmt->close();

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
            $address_id = null;
            if (!empty($_POST['existing_address'])) {
                $address_id = $_POST['existing_address'];
            } else {
                $new_address_stmt = $conn->prepare('INSERT INTO address (user_id, address_line, city, province, country, postal_code) VALUES (?, ?, ?, ?, ?, ?)');
                $new_address_stmt->bind_param("issssi", $user_id, $_POST['address_line'], $_POST['city'], $_POST['province'], $_POST['country'], $_POST['postal_code']);
                if ($new_address_stmt->execute()) {
                    $address_id = $new_address_stmt->insert_id;
                    $new_address_stmt->close();
                } else {
                    echo "Failed to insert new address: " . $new_address_stmt->error;
                }
            }


            $order_date = (new DateTime('now', new DateTimeZone('GMT+2')))->format('Y-m-d H:i:s');
            $price = $product_data['price'];
            $status = 'Pending payment';

            $insert_stmt = $conn->prepare('INSERT INTO `order` (order_date, price, status, customer_id, product_id) VALUES(?,?,?,?,?)');
            $insert_stmt->bind_param("sdsii", $order_date, $price, $status, $user_id, $product_id);

            if ($insert_stmt->execute()) {
                $order_id = $insert_stmt->insert_id;
                $insert_stmt->close();

                $delivery_method = $_POST['delivery_method'];
                $delivery_status = 'Pending payment';

                if ($delivery_method == 'Delivery') {
                    $shipment_stmt = $conn->prepare('INSERT INTO shipment (order_id, address_id, delivery_method, delivery_status) VALUES (?, ?, ?, ?)');
                    $shipment_stmt->bind_param("iiss", $order_id, $address_id, $delivery_method, $delivery_status);
                } else {
                    $shipment_stmt = $conn->prepare('INSERT INTO shipment (order_id, delivery_method, delivery_status) VALUES (?, ?, ?)');
                    $shipment_stmt->bind_param("iss", $order_id, $delivery_method, $delivery_status);
                }
                if ($shipment_stmt->execute()) {
                    $shipment_stmt->close();
                } else {
                    echo "Failed to insert shipment: " . $shipment_stmt->error;
                }

                echo '
                        <form id="redirectToPaymentForm" action="../payment/create.php" method="post">
                            <input type="hidden" name="order_id" value="' . $order_id . '">
                            <input type="hidden" name="price" value="' . $price . '">
                            <input type="hidden" name="product_id" value="' . $product_id . '">
                        </form>
                        <script>
                            document.getElementById("redirectToPaymentForm").submit();
                        </script>
                    ';
                exit;
            } else {
                echo "Failed to place order: " . $insert_stmt->error;
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';

?>

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

            <input type="radio" name="delivery_method" value="Delivery" checked>Delivery<br>
            <input type="radio" name="delivery_method" value="Collection">Collection<br><br>

            <div id="deliveryAddress">

                <h2>Delivery Address</h2>

                <?php if (count($addresses) > 0): ?>
                    <label for="existing_address">Select Existing Address:</label>
                    <select name="existing_address" id="existing_address">
                        <option value="">Select an address</option>
                        <?php foreach ($addresses as $address): ?>
                            <option
                                value="<?= $address['address_id']; ?>"
                                data-line="<?= htmlspecialchars($address['address_line']); ?>"
                                data-city="<?= htmlspecialchars($address['city']); ?>"
                                data-province="<?= htmlspecialchars($address['province']); ?>"
                                data-country="<?= htmlspecialchars($address['country']); ?>"
                                data-postal="<?= htmlspecialchars($address['postal_code']); ?>">
                                <?= htmlspecialchars(
                                    "{$address['address_line']}, {$address['city']}, {$address['province']}, {$address['country']}, {$address['postal_code']}"
                                ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <br><br>

                <label for="Address">Address Line:</label>
                <input type="text" name="address_line" id="address_line" placeholder="123 Steyn Road, Grape Village" value="<?php echo $address['address_line'][$address["address_id"]] ?? null ?>"><br><br>
                <label for="City">City:</label>
                <input type="text" name="city" id="city" placeholder="Cape Town"><br><br>
                <label for="Province">Province:</label>
                <input type="text" name="province" id="province" placeholder="Western Cape"><br><br>
                <label for="Country">Country:</label>
                <input type="text" name="country" id="country" placeholder="South Africa"><br><br>
                <label for="Postal Code">Postal Code:</label>
                <input type="text" name="postal_code" id="postal_code" placeholder="4321"><br><br>

            </div>

            <button type="submit">Confirm Order</button>
        </form>
    <?php else: ?>
        <p>No product selected.</p>
    <?php endif; ?>

    <?php
    require_once __DIR__ . '/../../includes/footer.php';
    ?>

<script>
document.getElementById('existing_address')
  .addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (!this.value) {
      // clear if “Select an address”
      document.getElementById('address_line').value = '';
      document.getElementById('city').value         = '';
      document.getElementById('province').value     = '';
      document.getElementById('country').value      = '';
      document.getElementById('postal_code').value  = '';
      return;
    }
    // populate from data- attributes
    document.getElementById('address_line').value   = opt.dataset.line;
    document.getElementById('city').value           = opt.dataset.city;
    document.getElementById('province').value       = opt.dataset.province;
    document.getElementById('country').value        = opt.dataset.country;
    document.getElementById('postal_code').value    = opt.dataset.postal;
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryMethodRadios = document.querySelectorAll('input[name="delivery_method"]');
        const deliveryAddressDiv = document.getElementById('deliveryAddress');

        deliveryMethodRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'Delivery') {
                    deliveryAddressDiv.style.display = 'block';
                } else {
                    deliveryAddressDiv.style.display = 'none';
                }
            });
        });
    });
</script>
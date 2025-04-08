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

$sql = "SELECT product_id, title FROM product WHERE seller_id = '{$_SESSION['User_ID']}' AND status = 'active'";
$result = $conn->query($sql);

// Initalise the product data variable before loading the form
$product_data = null;

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * from product where product_id = ? AND seller_id = ? AND status = 'active'");
    $stmt->bind_param("ii", $product_id, $_SESSION['User_ID']);
    $stmt->execute();
    $product_result = $stmt->get_result();

    if ($product_result->num_rows > 0) {
        $product_data = $product_result->fetch_assoc();
    } else {
        echo "No product found or you do not have permission to edit this product.";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $product_id = $_POST['product_id'];
    $seller_id = $_SESSION['User_ID'];

    $stmt = $conn->prepare("UPDATE product SET title = ?, description = ?, price = ?, category = ?, seller_id = ? WHERE product_id = ? and status = 'active'");
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
    $stmt->bind_param("ssdsii", $title, $description, $price, $category, $seller_id, $product_id);

    if ($stmt->execute()) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update listing</title>
</head>

<body>

    <h1>Update Listing</h1>

    <form action="" method="get">
        <div class="form-group">
            <label for="id">Product to update</label>
            <select name="id" id="id" required>
                <option value="">Select a product</option>
                <?php

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Check if the product is already selected
                        $selected = (isset($_GET['id']) && $_GET['id'] == $row["product_id"]) ? "selected" : "";
                        echo "<option value='{$row["product_id"]}' $selected>{$row["title"]}</option>";
                    }
                } else {
                    echo "<option value=''>No products found</option>";
                }
                ?>
            </select><br>
            <button type="submit">Load Product</button>
        </div>
    </form>
    <?php if ($product_data): ?>
        <form action="" method="post">
            <input type="hidden" name="product_id" value="<?php echo $product_data["product_id"]; ?>">

            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?php echo $product_data["title"]; ?>" required><br>

            <label for="description">Description</label>
            <textarea name="description" id="description"
                required><?php echo $product_data["description"]; ?></textarea><br>

            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" id="price" value="<?php echo $product_data["price"]; ?>"
                required><br>

            <label for="category">Category</label>
            <select name="category" id="category" required>
                <option value="">Select a category</option>
                <option value="Electronics" <?php echo ($product_data["category"] == "electronics") ? "selected" : ""; ?>>Electronics</option>
                <option value="Furniture" <?php echo ($product_data["category"] == "furniture") ? "selected" : ""; ?>>Furniture</option>
                <option value="Clothing" <?php echo ($product_data["category"] == "clothing") ? "selected" : ""; ?>>Clothing</option>
                <option value="Toys" <?php echo ($product_data["category"] == "toys") ? "selected" : ""; ?>>Toys</option>
                <option value="Books" <?php echo ($product_data["category"] == "books") ? "selected" : ""; ?>>Books</option>
                <option value="Vehicles" <?php echo ($product_data["category"] == "vehicles") ? "selected" : ""; ?>>Vehicles</option>
            </select><br>
            <input type="submit" value="Update Listing">
        </form>
    <?php else: ?>
        <p>Please select a product to update.</p>
    <?php endif; ?>
</body>

</html>

<?php
$conn->close();
?>
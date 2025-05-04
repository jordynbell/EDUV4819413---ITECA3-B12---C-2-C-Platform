<?php

require_once __DIR__ . '/../../lib/db.php';

$pageTitle = "Create Listing - Squito";

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["Email"])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $seller_id = $_SESSION['User_ID'];
    $status = "Active";

    $stmt = $conn->prepare("INSERT INTO product (title, description, price, category, seller_id, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsis", $title, $description, $price, $category, $seller_id, $status);

    if ($stmt->execute()) {
        header("Location: index.php");
        $stmt->close();
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

require_once __DIR__ . '/../../includes/header.php';
?>
    
<h1>Create Listing</h1>

<form action="" method="post">

    <label for="title">Title</label>
    <input type="text" name="title" id="title" required><br>

    <label for="description">Description</label>
    <textarea name="description" id="description" required></textarea><br>

    <label for="price">Price</label>
    <input type="number" step="0.01" name="price" id="price" required><br>

    <label for="category">Category</label>
    <select name="category" id="category" required>
        <option value="">Select a category</option>
        <option value="Electronics">Electronics</option>
        <option value="Furniture">Furniture</option>
        <option value="Clothing">Clothing</option>
        <option value="Toys">Toys</option>
        <option value="Books">Books</option>
        <option value="Vehicles">Vehicles</option>
    </select><br>

    <input type="submit" value="Create Listing">

</form>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>
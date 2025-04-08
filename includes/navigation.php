<?php

require_once __DIR__ . '/../lib/db.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["User_ID"])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION["User_ID"];

$stmt = $conn->prepare('SELECT role FROM user WHERE user_id = ?');
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

?>

<div class="navbar">
    <a href="/C2CPlatform/public/index.php">Home</a>
    <a href="/C2CPlatform/public/listing/index.php">View Listing</a>
    <a href="/C2CPlatform/public/listing/create.php">Create Listing</a>
    <a href="/C2CPlatform/public/listing/update.php">Update Listing</a>
    <a href="/C2CPlatform/public/listing/seller_index.php">My Listings</a>
    <?php
    if ($row['role'] == "Admin") {
        echo " <a href='/C2CPlatform/public/manage/user.php'>Manage Users</a>";
    }
    ?>
    <a href="/C2CPlatform/public/auth/logout.php">Logout</a>
</div>
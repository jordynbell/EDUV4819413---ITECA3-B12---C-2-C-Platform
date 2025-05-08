<?php

require_once __DIR__ . '/../../lib/db.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["Email"])) {
    header("Location: ../auth/login.php");
    exit;
}

$pageTitle = "View Order - Squito";

require_once __DIR__ . '/../../includes/header.php';

?>

<div class="container">
    <div class="card mt-4 mb-4">
        <div class="card-header">
            <h2 class="text-center">Order Details</h2>
        </div>
        <div class="card-body">
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
<?php 
require_once '../config/config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else
{
    echo "Connected successfully";
    $conn->close();
}
?> 
<br>
Test connection
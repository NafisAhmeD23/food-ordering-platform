<?php
// Database connection settings
$host = "localhost";
$user = "root"; // default XAMPP user
$pass = "";     // default XAMPP password is empty
$db   = "food_ordering_platform";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

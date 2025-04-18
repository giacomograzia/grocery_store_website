<?php
$servername = "localhost"; // Hostname (default for XAMPP)
$username = "root";        // Default username for XAMPP
$password = "";            // Default password for XAMPP (empty string)
$dbname = "assignment1"; // Name of the database you created in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
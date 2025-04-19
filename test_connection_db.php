<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/database.php';

if ($conn) {
    echo "Database connected successfully!";
} else {
    echo "Database connection failed: " . mysqli_connect_error();
}
?>

<?php
session_start();

// Check if a product name is provided in the query string and if it exists in the cart
if (isset($_GET['name']) && isset($_SESSION['cart'][$_GET['name']])) {
    // Remove the item from the cart based on its name
    unset($_SESSION['cart'][$_GET['name']]);
}

// Redirect the user back to the cart page after removing the item
header('Location: cart.php');
exit;


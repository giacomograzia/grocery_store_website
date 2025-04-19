<?php
session_start();

// Check if the 'quantities' POST data exists and is an array
if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    // Loop through each submitted quantity, where $name is the product identifier and $qty is the new quantity
    foreach ($_POST['quantities'] as $name => $qty) {
        // Convert the quantity to an integer and ensure it is at least 1 (prevents zero or negative values)
        $qty = max(1, intval($qty));

        // Check if the product exists in the session cart
        if (isset($_SESSION['cart'][$name])) {
            // Update the product's quantity in the cart
            $_SESSION['cart'][$name]['quantity'] = $qty;
        }
    }
}

// Redirect the user back to the cart page after updating quantities
header('Location: cart.php');
exit;

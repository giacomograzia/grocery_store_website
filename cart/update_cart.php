<?php
session_start();

// Check if the 'quantities' POST data exists and is an array
if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    // Loop through each submitted quantity
    foreach ($_POST['quantities'] as $name => $qty) {
        $qty = max(1, intval($qty));

        if (isset($_SESSION['cart'][$name])) {
            $_SESSION['cart'][$name]['quantity'] = $qty;
        }
    }

    // Clear stock error flag after successful quantity update
    unset($_SESSION['stock_error']);
}

// Redirect back to the cart page
header('Location: cart.php');
exit;

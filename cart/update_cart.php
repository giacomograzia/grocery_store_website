<?php
session_start();

if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $name => $qty) {
        $qty = max(1, intval($qty)); // prevent zero or negative
        if (isset($_SESSION['cart'][$name])) {
            $_SESSION['cart'][$name]['quantity'] = $qty;
        }
    }
}

header('Location: cart.php');
exit;

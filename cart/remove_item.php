<?php
session_start();

if (isset($_GET['name']) && isset($_SESSION['cart'][$_GET['name']])) {
    unset($_SESSION['cart'][$_GET['name']]);
}

header('Location: cart.php');
exit;

<?php
session_start(); 

unset($_SESSION['cart']); // Remove the entire cart from session

header('Location: cart.php'); // Redirect to cart page

exit; // Stop script execution

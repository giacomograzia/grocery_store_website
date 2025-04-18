<?php
session_start();
require_once '../database.php';

if (empty($_SESSION['cart']) || !isset($_POST)) {
    header('Location: ../cart/cart.php');
    exit;
}

$cart = $_SESSION['cart'];
$errors = [];

foreach ($cart as $key => $item) {
    $productName = $item['name'];
    $unitQuantity = $item['unit_quantity'];
    $desiredQty = $item['quantity'];

    $stmt = $conn->prepare("SELECT in_stock FROM products WHERE product_name = ? AND unit_quantity = ?");
    $stmt->bind_param("ss", $productName, $unitQuantity);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product || $product['in_stock'] < $desiredQty) {
        $_SESSION['error'] = "Sorry, '$productName ($unitQuantity)' is no longer available in the requested quantity.";
        header('Location: ../cart/cart.php');
        exit;
    }
}

// If stock is valid for all items, update quantities
foreach ($cart as $key => $item) {
    $productName = $item['name'];
    $unitQuantity = $item['unit_quantity'];
    $desiredQty = $item['quantity'];

    $stmt = $conn->prepare("UPDATE products SET in_stock = in_stock - ? WHERE product_name = ? AND unit_quantity = ?");
    $stmt->bind_param("iss", $desiredQty, $productName, $unitQuantity);
    $stmt->execute();
}


// Store form info in session for order confirmation display
$_SESSION['form_data'] = [
    'first_name' => $_POST['first_name'],
    'surname' => $_POST['surname'],
    'email' => $_POST['email'],
    'street' => $_POST['street'],
    'city' => $_POST['city'],
    'state' => $_POST['state']
];

// Store order summary temporarily for confirmation display
$_SESSION['order_summary'] = $cart;

// Clear the cart
unset($_SESSION['cart']);

header("Location: order_success.php");
exit;

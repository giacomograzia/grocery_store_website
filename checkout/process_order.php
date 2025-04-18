<?php
session_start();
require_once '../database.php';

// Redirect if cart is empty or the request is not POST
if (empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cart/cart.php');
    exit;
}

$cart = $_SESSION['cart'];

// Validate delivery form inputs
$requiredFields = ['first_name', 'surname', 'email', 'street', 'city', 'state'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        // Redirect back with error if any field is missing
        $_SESSION['error'] = 'Please complete all required delivery fields.';
        header('Location: ../cart/cart.php');
        exit;
    }
}

// Validate product stock availability
foreach ($cart as $item) {
    $productName = $item['name'];
    $unitQuantity = $item['unit_quantity'];
    $desiredQty = $item['quantity'];

    // Prepare and execute SELECT query to check stock
    $stmt = $conn->prepare("SELECT in_stock FROM products WHERE product_name = ? AND unit_quantity = ?");
    $stmt->bind_param("ss", $productName, $unitQuantity);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // If stock is not sufficient, redirect back with error
    if (!$product || $product['in_stock'] < $desiredQty) {
        $_SESSION['error'] = "Sorry, '$productName ($unitQuantity)' is no longer available in the requested quantity.";
        header('Location: ../cart/cart.php');
        exit;
    }
}

// Update product quantities in database
foreach ($cart as $item) {
    $productName = $item['name'];
    $unitQuantity = $item['unit_quantity'];
    $desiredQty = $item['quantity'];

    // Subtract purchased quantity from in_stock
    $stmt = $conn->prepare("UPDATE products SET in_stock = in_stock - ? WHERE product_name = ? AND unit_quantity = ?");
    $stmt->bind_param("iss", $desiredQty, $productName, $unitQuantity);
    $stmt->execute();
}

// Store delivery form info in session for order confirmation
$_SESSION['form_data'] = [
    'first_name' => $_POST['first_name'],
    'surname'    => $_POST['surname'],
    'email'      => $_POST['email'],
    'street'     => $_POST['street'],
    'city'       => $_POST['city'],
    'state'      => $_POST['state']
];

// Store cart summary temporarily for display on success page
$_SESSION['order_summary'] = $cart;

// Clear the cart session
unset($_SESSION['cart']);

// Redirect to success confirmation page
header("Location: order_success.php");
exit;

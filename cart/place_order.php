<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/database.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header("Location: cart.php");
    exit;
}

// 1. Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// 2. Save to database (simplified version)
$orderDetails = json_encode($cart); // or normalize later
$stmt = $conn->prepare("INSERT INTO orders (order_details, total_price, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("sd", $orderDetails, $total);
$stmt->execute();

// 3. Clear cart
unset($_SESSION['cart']);

header("Location: order_confirmation.php");
exit;

<?php
session_start();

// Get and unset the order data
$order = $_SESSION['order_summary'] ?? [];
$firstName = htmlspecialchars($_SESSION['form_data']['first_name'] ?? '');
$email = htmlspecialchars($_SESSION['form_data']['email'] ?? '');
unset($_SESSION['order_summary'], $_SESSION['form_data']);

$totalAmount = 0;
$totalItems = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - Grocery Store</title>
    <link rel="stylesheet" href="/grocery_store/styles.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/templates/header_simple.php'; ?>

    <div class="page-wrapper" style="flex: 1; display: flex; flex-direction: column;">
        <div class="flex-grow-content" style="flex: 1; display: flex; justify-content: center; align-items: center;">
            <div class="confirmation-section">

                <!-- Order Success image -->
                <img src="/grocery_store/assets/confirmation.png" alt="Illustration of a confirmed order"
                    style="max-width: 300px; margin-bottom: 30px; border-radius: 5px; box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);">

                <!-- Thank You -->
                <h2>Thank you for your order, <?= $firstName ?>!</h2>
                <div style="margin-bottom: 10px;"></div>

                <!-- Email Simulation -->
                <p style="width: 100%; max-width: 500px; margin: 10px auto 30px; text-align: center; font-size: 16px;">
                    A confirmation email has been sent to <strong><?= $email ?></strong> with your order summary:
                </p>

                <!-- Order Summary -->
                <div
                    style="width: 100%; max-width: 600px; display: flex; flex-direction: column; gap: 20px; margin-bottom: 20px;">
                    <?php foreach ($order as $item):
                        $itemTotal = $item['price'] * $item['quantity'];
                        $totalAmount += $itemTotal;
                        $totalItems += $item['quantity'];
                        ?>
                        <div class="order-card">
                            <div style="flex: 2;">
                                <strong style="font-size: 18px;"><?php echo htmlspecialchars($item['name']); ?></strong>
                                <p style="margin: 4px 0;"><?php echo htmlspecialchars($item['unit_quantity']); ?></p>
                                <p style="margin: 4px 0;">$<?php echo number_format($item['price'], 2); ?> each</p>
                            </div>
                            <div style="flex: 1; display: flex; flex-direction: column; align-items: flex-end;">
                                <p style="margin: 4px 0;">Qty: <?php echo $item['quantity']; ?></p>
                                <p style="margin: 4px 0; font-weight: bold;">Total:
                                    $<?php echo number_format($itemTotal, 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totals -->
                <div class="summary-totals">
                    Total items: <?= $totalItems ?><br>
                    Total amount: $<?= number_format($totalAmount, 2) ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../templates/footer.php'; ?>
</body>

</html>

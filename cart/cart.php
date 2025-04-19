<?php
session_start();
$cart = $_SESSION['cart'] ?? [];
$total = 0;

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart - Grocery Store</title>
    <link rel="stylesheet" href="/grocery_store/styles.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/templates/header_simple.php'; ?>

    <div class="page-wrapper">

        <!-- PAGE TITLE: Full Width, Below Site Header -->
        <?php if (empty($cart)): ?>
            <div style="width: 100%; padding: 0px 0 0px; text-align: center;">
            </div>
        <?php else: ?>
            <div style="width: 100%; padding: 30px 0 20px; text-align: center;">
                <img src="/grocery_store/assets/cart.png" alt="Order Confirmed"
                    style="max-width: 100px; margin-bottom: 30px; border-radius: 5px; box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);">
                <h2
                    style="font-size: 28px; font-weight: bold; color: #314401; margin: 0; animation: fadeInTop 0.6s ease-out;">
                    Your Shopping Cart
                </h2>
                <?php if (!empty($_SESSION['error'])): ?>
                    <div style="
            background-color: #fdecea;
            color: #c0392b;
            padding: 16px 20px;
            margin-top: 20px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            font-size: 15px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        ">
                        <?= strip_tags($_SESSION['error'], '<strong>') ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </div>

        <?php endif; ?>


        <!-- MAIN PAGE CONTENT -->
        <div class="main-content main-content--narrow">
            <?php if (empty($cart)): ?>
                <!-- EMPTY CART VIEW -->
                <div style="display: flex; justify-content: center; align-items: center; width: 100%; min-height: 70vh;">
                    <div class="empty-cart"
                        style="text-align: center; display: flex; flex-direction: column; align-items: center;">
                        <img src="/grocery_store/assets/empty_cart.png" alt="Empty cart"
                            style="max-width: 280px; margin-bottom: 30px; border-radius: 5px; box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);">
                        <p style="max-width: 600px; margin: 0 auto 30px;">
                            Browse products and add something to your cart!
                        </p>
                        <a href="/grocery_store/index.php" class="add-to-cart" style="text-decoration: none;">Back to
                            Shop</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- OUTER FLEX CONTAINER: Full Width -->
                <div
                    style="display: flex; flex-wrap: wrap; gap: 40px; width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 16px; box-sizing: border-box;">

                    <!-- INNER WRAPPER: Fixed max-width, centered -->
                    <div
                        style="display: flex; gap: 40px; width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 20px; align-items: flex-start; flex-wrap: wrap;">

                        <!-- FORM WRAPPING ITEM LIST + UPDATE BUTTON -->
                        <form action="update_cart.php" method="POST"
                            style="flex: 8; min-width: 300px; display: flex; flex-direction: column; gap: 20px;">

                            <?php
                            $total = 0;
                            foreach ($cart as $name => $details):
                                $itemTotal = $details['price'] * $details['quantity'];
                                $total += $itemTotal;
                                ?>
                                <div
                                    style="background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; gap: 20px; align-items: center; animation: slideUp 0.3s ease;">

                                    <!-- IMAGE -->
                                    <div
                                        style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        <img src="/grocery_store/images/<?php echo htmlspecialchars($details['image']); ?>"
                                            alt="<?php echo htmlspecialchars($details['name']); ?>"
                                            style="max-width: 100%; max-height: 100%; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.2));">
                                    </div>

                                    <!-- PRODUCT INFO -->
                                    <div style="flex: 2; display: flex; flex-direction: column; justify-content: center;">
                                        <strong style="font-size: 18px;"><?php echo htmlspecialchars($name); ?></strong>
                                        <p style="margin: 4px 0;">$<?php echo number_format($details['price'], 2); ?> each</p>
                                        <p style="margin: 4px 0;">Total: $<?php echo number_format($itemTotal, 2); ?></p>
                                    </div>

                                    <!-- CONTROLS -->
                                    <div
                                        style="flex: 1; display: flex; align-items: center; gap: 5px; justify-content: flex-end;">
                                        <input type="number" name="quantities[<?php echo $name; ?>]"
                                            value="<?php echo $details['quantity']; ?>" min="1"
                                            style="width: 60px; padding: 6px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;">
                                        <a href="remove_item.php?name=<?php echo urlencode($name); ?>"
                                            style="color: #c0392b; text-decoration: none; font-size: 14px;">Remove</a>
                                    </div>
                                </div>


                            <?php endforeach; ?>

                            <!-- UPDATE BUTTON (inside form) -->
                            <button type="submit" class="add-to-cart" style="width: 100%; margin-top: 10px;">
                                Save Updated Quantities
                            </button>
                        </form>

                        <!-- SUMMARY SECTION -->
                        <div
                            style="flex: 3; min-width: 280px; height: auto; display: flex; flex-direction: column; gap: 20px; background: #fdfdfb; border-radius: 8px; padding: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); animation: slideUp 0.4s ease;">
                            <div style="text-align: center;">
                                <h3 style="color: #314401; margin-bottom: 10px;">Order Summary</h3>
                                <p>Total items: <?php echo array_sum(array_column($cart, 'quantity')); ?></p>
                                <p>Total amount: $<?php echo number_format($total, 2); ?></p>
                            </div>

                            <!-- PLACE ORDER FORM -->
                            <form action="/grocery_store/checkout/checkout.php" method="POST" style="width: 100%;">
                                <button type="submit" class="add-to-cart"
                                    style="width: 100%; <?php echo !empty($_SESSION['stock_error']) ? 'background-color: #ccc; color: #666; cursor: not-allowed;' : ''; ?>"
                                    <?php echo !empty($_SESSION['stock_error']) ? 'disabled' : ''; ?>>
                                    Proceed to Checkout
                                </button>
                            </form>

                            <?php if (!empty($_SESSION['stock_error'])): ?>
                                <p style="color: #c0392b; font-size: 14px; margin-top: 10px; text-align: center;">
                                    Please reduce item quantities and update your cart before checking out.
                                </p>
                            <?php endif; ?>


                            <!-- CLEAR CART FORM -->
                            <form action="clear_cart.php" method="POST" style="width: 100%;">
                                <button type="submit" class="add-to-cart"
                                    style="width: 100%; background-color: #ccc; color: #333;">Clear Cart</button>
                            </form>
                        </div>

                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div style="min-height: 40px;"></div>

        <?php include '../templates/footer.php'; ?>

    </div>

    <?php unset($_SESSION['stock_error']); ?>

</body>

</html>


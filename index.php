<!DOCTYPE html>
<html lang="en">

<?php
// Start session and handle cart logic
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['add_to_cart'] === '1') {
    $productName = trim($_POST['product_name']);
    $unitPrice = floatval($_POST['unit_price']);
    $unitQuantity = trim($_POST['unit_quantity']);
    $quantity = intval($_POST['quantity']);
    $productKey = $productName . ', ' . $unitQuantity;
    $image = trim($_POST['image_']);



    $_SESSION['cart'] ??= [];

    if (isset($_SESSION['cart'][$productKey])) {
        $_SESSION['cart'][$productKey]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productKey] = [
            'name' => $productName,
            'unit_quantity' => $unitQuantity,
            'price' => $unitPrice,
            'quantity' => $quantity,
            'image' => $image
        ];
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Store</title>
    <link rel="stylesheet" href="/grocery_store/styles.css?v=1.2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

    <!-- JS for increment/decrement logic -->
    <script>
        function adjustQuantity(btn, delta) {
            const input = btn.parentElement.querySelector('input[name="quantity"]');
            let current = parseInt(input.value, 10);
            input.value = Math.max(1, (isNaN(current) ? 1 : current + delta));
        }
    </script>

    <?php
    // Connect to DB
    include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/database.php';
    if (!$conn)
        die("Database connection failed: " . mysqli_connect_error());

    // Get category/sub/search values
    $selectedCategory = $_GET['cat'] ?? '';
    $selectedSub = $_GET['sub'] ?? '';
    $searchTerm = trim($_GET['search'] ?? '');

    // Prepare product query
    if ($selectedCategory && $selectedSub && $searchTerm) {
        $stmt = $conn->prepare("SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE category = ? AND subcategory = ? AND product_name LIKE ?");
        $like = "%$searchTerm%";
        $stmt->bind_param("sss", $selectedCategory, $selectedSub, $like);
    } elseif ($selectedCategory && $selectedSub) {
        $stmt = $conn->prepare("SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE category = ? AND subcategory = ?");
        $stmt->bind_param("ss", $selectedCategory, $selectedSub);
    } elseif ($selectedCategory) {
        $stmt = $conn->prepare("SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE category = ?");
        $stmt->bind_param("s", $selectedCategory);
    } elseif ($searchTerm) {
        $stmt = $conn->prepare("SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE product_name LIKE ?");
        $like = "%$searchTerm%";
        $stmt->bind_param("s", $like);
    } else {
        $productResult = $conn->query("SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products");
    }

    if (isset($stmt)) {
        $stmt->execute();
        $productResult = $stmt->get_result();
    }

    // Include header
    include 'templates/header.php';
    ?>

    <!-- BREADCRUMB NAVIGATION -->
    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <?php if ($searchTerm): ?>
                <span class="breadcrumb-current">🔍 Search: "<?php echo htmlspecialchars($searchTerm); ?>"</span>
            <?php elseif ($selectedCategory && $selectedSub): ?>
                <a href="/grocery_store/index.php" class="breadcrumb-link">All Products</a>
                <span class="breadcrumb-separator">›</span>
                <a href="/grocery_store/index.php?cat=<?php echo urlencode($selectedCategory); ?>"
                    class="breadcrumb-link"><?php echo htmlspecialchars($selectedCategory); ?></a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-current"><?php echo htmlspecialchars($selectedSub); ?></span>
            <?php elseif ($selectedCategory): ?>
                <a href="/grocery_store/index.php" class="breadcrumb-link">Home</a>
                <span class="breadcrumb-separator">›</span>
                <span class="breadcrumb-current"><?php echo htmlspecialchars($selectedCategory); ?></span>
            <?php else: ?>
                <span class="breadcrumb-current">All Products</span>
            <?php endif; ?>
        </nav>
    </div>

    <!-- PRODUCT GRID -->
    <div class="main-content main-content--wide">
        <?php if ($productResult && $productResult->num_rows > 0): ?>
            <section class="product-grid">
                <?php $index = 0; ?>
                <?php while ($product = $productResult->fetch_assoc()): ?>
                    <?php $delay = $index * 0.1;
                    $index++; ?>
                    <div class="product" style="animation-delay: <?php echo $delay; ?>s;">
                        <!-- IMAGE + INFO -->
                        <div class="product-image-wrapper">
                            <img src="/grocery_store/images/<?php echo htmlspecialchars($product['image_']); ?>"
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image"
                                onerror="this.style.display='none';">
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-price">$<?php echo number_format($product['unit_price'], 2); ?></p>
                        <p class="product-quantity">Quantity: <?php echo htmlspecialchars($product['unit_quantity']); ?></p>

                        <!-- STOCK STATUS -->
                        <?php if ($product['in_stock'] > 200): ?>
                            <p class="stock-status" style="color: green; margin: 0;">In Stock</p>
                        <?php elseif ($product['in_stock'] == 0): ?>
                            <p class="stock-status" style="color: red; margin: 0;">Out of Stock</p>
                        <?php else: ?>
                            <p class="stock-status" style="color: orange; margin: 0;">Low Stock</p>
                        <?php endif; ?>

                        <!-- ADD TO CART -->
                        <?php if ($product['in_stock'] == 0): ?>
                            <button class="add-to-cart disabled" disabled>Out of Stock</button>
                        <?php else: ?>
                            <form action="" method="POST"
                                style="margin-top: auto; display: flex; flex-direction: column; gap: 5px;">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="product_name"
                                    value="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <input type="hidden" name="unit_price" value="<?php echo $product['unit_price']; ?>">
                                <input type="hidden" name="unit_quantity"
                                    value="<?php echo htmlspecialchars($product['unit_quantity']); ?>">
                                <input type="hidden" name="image_" value="<?php echo htmlspecialchars($product['image_']); ?>">


                                <div class="quantity-wrapper">
                                    <button type="button" onclick="adjustQuantity(this, -1)" class="quantity-btn">−</button>
                                    <input type="text" name="quantity" value="1" class="quantity-input" required />
                                    <button type="button" onclick="adjustQuantity(this, 1)" class="quantity-btn">+</button>
                                </div>

                                <button type="submit" class="add-to-cart" style="margin-top: 10px;">Add to Cart</button>
                            </form>

                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </section>
        <?php else: ?>
            <!-- CENTERED EMPTY STATE -->
            <div class="empty-category-wrapper">
                <div class="empty-category">
                    <img src="/grocery_store/assets/empty_category.png" alt="No products"
                        style="max-width: 280px; margin-bottom: 30px; border-radius: 5px; box-shadow: 0 8px 12px rgba(0,0,0,0.1);">
                    <p class="empty-message-title">No products found...</p>
                    <p class="empty-message-sub">
                        <?php echo $selectedCategory
                            ? "Looks like there’s nothing in this category right now."
                            : "Try searching for something else or check back later."; ?>
                    </p>
                    <a href="/grocery_store/index.php" class="add-to-cart" style="text-decoration: none;">Back to Shop</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    </section>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>

</html>
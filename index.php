<!DOCTYPE html>
<html lang="en">

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart']) && $_POST['add_to_cart'] === '1') {
    $productName = trim($_POST['product_name']);
    $unitPrice = floatval($_POST['unit_price']);
    $unitQuantity = trim($_POST['unit_quantity']);
    $quantity = intval($_POST['quantity']);

    $productKey = $productName . ', ' . $unitQuantity;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productKey])) {
        $_SESSION['cart'][$productKey]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productKey] = [
            'name' => $productName,
            'unit_quantity' => $unitQuantity,
            'price' => $unitPrice,
            'quantity' => $quantity
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
    <link rel="stylesheet" href="/grocery_store/styles.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            justify-content: start;
            align-items: stretch;
        }

        .product {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: 85%;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            opacity: 0;
            animation: fadeSlideUp 0.6s ease-out forwards;
        }

        .product:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
        }

        .product-image-wrapper {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            // background-color: #f5f5f5;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .product-image {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.4s ease;
        }

        .product:hover .product-image {
            transform: scale(1.05);
        }

        .add-to-cart {
            background-color: #314401;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: auto;
        }

        .add-to-cart.disabled {
            background-color: #ccc;
            color: #666;
            cursor: not-allowed;
            pointer-events: none;
        }

        .add-to-cart.disabled:hover {
            background-color: #ccc;
        }

        .add-to-cart:hover {
            background-color: #94a84e;
        }

        .category-heading {
            text-align: center;
            margin: 50px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #314401;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .category-heading h2 {
            font-size: 28px;
            color: #314401;
            margin: 0;
        }

        .category-heading em {
            font-style: normal;
            color: #94a84e;
            font-weight: 600;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .quantity-wrapper {
            display: flex;
            align-items: stretch;
            /* top-align the buttons and input */
            justify-content: center;
            gap: 6px;
            margin: 10px 0;
        }

        .quantity-input {
            width: 52px;
            height: 40px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-align: center;
            padding: 0;
            margin: 0;
            line-height: 40px;
            /* center text vertically */
            box-sizing: border-box;
            appearance: textfield;
        }

        .quantity-input::-webkit-inner-spin-button,
        .quantity-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            font-size: 20px;
            font-weight: bold;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #eaeaea;
            cursor: pointer;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            transition: background-color 0.2s;
            line-height: 1;
            /* prevent button symbol from misaligning */
        }

        .quantity-btn:hover {
            background-color: #d5d5d5;
        }
    </style>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>

    <script>
        function adjustQuantity(btn, delta) {
            const input = btn.parentElement.querySelector('input[name="quantity"]');
            let current = parseInt(input.value, 10);
            if (isNaN(current)) current = 1;
            const newVal = Math.max(1, current + delta);
            input.value = newVal;
        }
    </script>



    <?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/database.php';

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $selectedCategory = $_GET['cat'] ?? '';
    $selectedSub = $_GET['sub'] ?? '';
    $searchTerm = trim($_GET['search'] ?? '');

    if ($selectedCategory && $selectedSub && $searchTerm) {
        $productQuery = "SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE category = ? AND subcategory = ? AND product_name LIKE ?";
        $stmt = $conn->prepare($productQuery);
        $like = '%' . $searchTerm . '%';
        $stmt->bind_param("sss", $selectedCategory, $selectedSub, $like);
        $stmt->execute();
        $productResult = $stmt->get_result();
    } elseif ($selectedCategory && $selectedSub) {
        $productQuery = "SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE category = ? AND subcategory = ?";
        $stmt = $conn->prepare($productQuery);
        $stmt->bind_param("ss", $selectedCategory, $selectedSub);
        $stmt->execute();
        $productResult = $stmt->get_result();
    } elseif ($selectedCategory) {
        $productQuery = "SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE category = ?";
        $stmt = $conn->prepare($productQuery);
        $stmt->bind_param("s", $selectedCategory);
        $stmt->execute();
        $productResult = $stmt->get_result();
    } elseif ($searchTerm) {
        $productQuery = "SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products WHERE product_name LIKE ?";
        $stmt = $conn->prepare($productQuery);
        $like = '%' . $searchTerm . '%';
        $stmt->bind_param("s", $like);
        $stmt->execute();
        $productResult = $stmt->get_result();
    } else {
        $productQuery = "SELECT product_name, unit_price, unit_quantity, image_, in_stock FROM products";
        $productResult = $conn->query($productQuery);
    }

    include 'templates/header.php';
    ?>

    <div class="breadcrumb-wrapper">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <?php if ($searchTerm): ?>
                <span class="breadcrumb-current">🔍 Search: "<?php echo htmlspecialchars($searchTerm); ?>"</span>
            <?php elseif ($selectedCategory && $selectedSub): ?>
                <a href="/grocery_store/index.php" class="breadcrumb-link">All Products</a>
                <span class="breadcrumb-separator">›</span>
                <a href="/grocery_store/index.php?cat=<?php echo urlencode($selectedCategory); ?>" class="breadcrumb-link">
                    <?php echo htmlspecialchars($selectedCategory); ?>
                </a>
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


    <div class="main-content">
        <section class="product-grid">
            <?php if ($productResult && $productResult->num_rows > 0): ?>
                <?php $index = 0; ?>
                <?php while ($product = $productResult->fetch_assoc()): ?>
                    <?php $delay = $index * 0.1;
                    $index++; ?>
                    <div class="product" style="animation-delay: <?php echo $delay; ?>s;">
                        <div class="product-image-wrapper">
                            <img src="/grocery_store/images/<?php echo htmlspecialchars($product['image_']); ?>"
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="product-image"
                                onerror="this.style.display='none';">
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-price">$<?php echo number_format($product['unit_price'], 2); ?></p>
                        <p class="product-quantity">Quantity: <?php echo htmlspecialchars($product['unit_quantity']); ?></p>

                        <div style="height: 15px;"></div>

                        <?php if ($product['in_stock'] > 200): ?>
                            <p class="stock-status" style="color: green; margin: 0;">In Stock</p>
                        <?php elseif ($product['in_stock'] == 0): ?>
                            <p class="stock-status" style="color: red; margin: 0;">Out of Stock</p>
                        <?php else: ?>
                            <p class="stock-status" style="color: orange; margin: 0;">Low Stock</p>
                        <?php endif; ?>

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
                                <form action="" method="POST" style="margin-top: 10px;">
                                    <input type="hidden" name="add_to_cart" value="1">
                                    <input type="hidden" name="product_name"
                                        value="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <input type="hidden" name="unit_price" value="<?php echo $product['unit_price']; ?>">
                                    <input type="hidden" name="unit_quantity"
                                        value="<?php echo htmlspecialchars($product['unit_quantity']); ?>">

                                    <div class="relative flex items-center max-w-[8rem] mx-auto">
                                        <button type="button" onclick="adjustQuantity(this, -1)"
                                            class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-2 focus:ring-gray-100 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>

                                        <input type="text" name="quantity" value="1" min="1"
                                            class="bg-gray-50 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm block w-full py-2.5 focus:ring-blue-500 focus:border-blue-500"
                                            required />

                                        <button type="button" onclick="adjustQuantity(this, 1)"
                                            class="bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-2 focus:ring-gray-100 focus:outline-none">
                                            <svg class="w-3 h-3 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>

                                    <button type="submit" class="add-to-cart" style="margin-top: 10px;">Add to Cart</button>
                                </form>


                            </form>

                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div
                    style="display: flex; justify-content: center; align-items: center; width: 100%; min-height: 40vh; padding: 20px 20px;">
                    <div class="empty-category"
                        style="text-align: center; display: flex; flex-direction: column; align-items: center;">

                        <img src="/grocery_store/assets/empty_category.png" alt="No products"
                            style="max-width: 280px; margin-bottom: 30px; border-radius: 5px; box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);">

                        <p style="font-size: 22px; font-weight: bold; margin-bottom: 10px; color: #444;">
                            No products found...
                        </p>
                        <p style="max-width: 600px; margin: 0 auto 30px; font-size: 16px; color: #555;">
                            <?php echo $selectedCategory
                                ? "Looks like there’s nothing in this category right now."
                                : "Try searching for something else or check back later."; ?>
                        </p>

                        <a href="/grocery_store/index.php" class="add-to-cart" style="text-decoration: none;">Back to
                            Shop</a>
                    </div>
                </div>


            <?php endif; ?>
        </section>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>

</html>
<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/database.php';

// Prepare structured categories
$categoryQuery = "
    SELECT DISTINCT category, subcategory 
    FROM products 
    WHERE category IS NOT NULL AND category != ''
    ORDER BY category, subcategory
";
$categoryResult = $conn->query($categoryQuery);

$structured = [];
while ($row = $categoryResult->fetch_assoc()) {
    $cat = $row['category'];
    $sub = $row['subcategory'];
    if (!isset($structured[$cat])) {
        $structured[$cat] = [];
    }
    if ($sub && !in_array($sub, $structured[$cat])) {
        $structured[$cat][] = $sub;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grocery Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/grocery_store/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <header>
        <a href="/grocery_store/index.php">
            <img src="/grocery_store/assets/logo_horizontal.png" alt="Grocery Store Logo" class="logo">
        </a>

        <div class="navbar">
            <a href="/grocery_store/index.php">Home</a>

            <!-- Category Dropdown -->
            <div class="dropdown">
                <button class="dropbtn" onclick="toggleDropdown()">Categories <i class="fa fa-caret-down"></i></button>
                <div class="dropdown-content" id="categoryDropdown">
                    <a href="/grocery_store/index.php" class="category-link<?php if (!isset($_GET['cat']) && !isset($_GET['sub'])) echo ' active'; ?>">
                        All Products
                    </a>

                    <?php foreach ($structured as $category => $subcategories): ?>
                        <div class="submenu-parent">
                            <a href="/grocery_store/index.php?cat=<?php echo urlencode($category); ?>"
                                class="category-link<?php echo (isset($_GET['cat']) && $_GET['cat'] === $category && !isset($_GET['sub'])) ? ' active' : ''; ?>">
                                <?php echo htmlspecialchars($category); ?>
                            </a>
                            <?php if (!empty($subcategories)): ?>
                                <div class="submenu">
                                    <?php foreach ($subcategories as $sub): ?>
                                        <a href="/grocery_store/index.php?cat=<?php echo urlencode($category); ?>&sub=<?php echo urlencode($sub); ?>"
                                            class="<?php echo (isset($_GET['sub']) && $_GET['sub'] === $sub) ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($sub); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="/grocery_store/cart/cart.php">Shopping Cart</a>
        </div>

        <!-- Search Bar -->
        <form class="search-bar" action="/grocery_store/index.php" method="GET" autocomplete="off">
            <input type="text" name="search" id="searchInput" placeholder="Search products..." required>
            <button type="submit"><i class="fa fa-search"></i></button>
            <div class="search-hints" id="searchHints">
                <p>try...</p>
                <ul>
                    <li onclick="fillSearch('Apples')">Apples</li>
                    <li onclick="fillSearch('Dog')">Dog</li>
                    <li onclick="fillSearch('Food')">Food</li>
                </ul>
            </div>
        </form>
    </header>

    <!-- JS Logic -->
    <script>
        const input = document.getElementById('searchInput');
        const hints = document.getElementById('searchHints');

        input.addEventListener('focus', () => {
            if (input.value.trim() === '') {
                hints.style.display = 'block';
            }
        });

        input.addEventListener('input', () => {
            hints.style.display = input.value.trim() === '' ? 'block' : 'none';
        });

        input.addEventListener('blur', () => {
            setTimeout(() => { hints.style.display = 'none'; }, 100);
        });

        function fillSearch(value) {
            input.value = value;
            hints.style.display = 'none';
        }

        function toggleDropdown() {
            const dropdown = document.getElementById("categoryDropdown");
            dropdown.classList.toggle("show");
        }

        window.onclick = function (event) {
            if (!event.target.matches('.dropbtn')) {
                const dropdown = document.getElementById("categoryDropdown");
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        };
    </script>
</body>
</html>

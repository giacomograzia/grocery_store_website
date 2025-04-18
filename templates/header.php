<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/database.php';

// Prepare categories
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
    <link rel="stylesheet" href="/grocery_store/styles.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #314401;
        }

        .logo {
            height: 75px;
            width: auto;
            border-radius: 5px;
        }

        .navbar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar a,
        .dropbtn {
            font-size: 16px;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
            border-radius: 5px;
        }

        .navbar a:hover,
        .dropbtn:hover {
            background-color: #94a84e;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 5px;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .show {
            display: block;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            justify-content: center;
        }

        .product {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: box-shadow 0.3s ease;
        }

        .product:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            transition: transform 0.3s ease;
        }

        .product:hover .product-image {
            transform: scale(1.05);
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 20px;
        }

        .search-bar input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-bar button {
            background-color: #94a84e;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .search-bar button:hover {
            background-color: #7f8f3f;
        }

        .search-bar {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 20px;
        }

        .search-bar input[type="text"] {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 200px;
        }

        .search-hints {
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 4px;
            width: 77%;
            display: none;
            z-index: 1000;
            padding: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .search-hints p {
            margin: 0 0 4px;
            font-size: 14px;
            color: #888;
        }

        .search-hints ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .search-hints li {
            padding: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .search-hints li:hover {
            background-color: #f0f0f0;
        }

        .category-group a {
            padding-left: 16px;
            display: block;
            transition: background 0.2s;
        }

        .category-link {
            font-weight: bold;
            padding-left: 12px;
        }

        .subcategory-link {
            font-size: 14px;
            color: #333;
            padding-left: 24px;
        }

        .dropdown-content a:hover {
            background-color: #e7e7e7;
            color: #314401;
        }

        .dropdown-content a:active {
            background-color: #d3d3d3;
        }

        /* Submenu container */
        .submenu-parent {
            position: relative;
        }

        /* Submenu itself (initially hidden) */
        .submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            min-width: 160px;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            z-index: 1001;
        }

        /* Show submenu on hover */
        .submenu-parent:hover .submenu {
            display: block;
        }

        /* Category links styling */
        .category-link {
            display: block;
            padding: 12px 16px;
            color: black;
            text-decoration: none;
            transition: background 0.2s;
        }

        /* Highlighting */
        .category-link:hover,
        .submenu a:hover {
            background-color: #eaeaea;
            color: #314401;
        }

        .submenu a {
            display: block;
            padding: 10px 16px;
            color: black;
            text-decoration: none;
        }

        /* Active selection styling */
        .category-link.active,
        .submenu a.active {
            font-weight: bold;
            background-color: #ddecd2;
            color: #314401;
        }
    </style>
</head>

<body>
    <header>
        <a href="/grocery_store/index.php">
            <img src="/grocery_store/assets/logo_horizontal.png" alt="Grocery Store Logo" class="logo">
        </a>
        <div class="navbar">
            <a href="/grocery_store/index.php">Home</a>


            <div class="dropdown">
                <button class="dropbtn" onclick="toggleDropdown()">Categories <i class="fa fa-caret-down"></i></button>
                <div class="dropdown-content" id="categoryDropdown">
                    <!-- All Products link -->
                    <a href="/grocery_store/index.php" class="category-link" style="<?php if (!isset($_GET['cat']) && !isset($_GET['sub']))
                        echo 'font-weight: bold;'; ?>">
                        All Products
                    </a>

                    <!-- Dynamic categories and subcategories -->
                    <?php
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
                        if (!isset($structured[$cat]))
                            $structured[$cat] = [];
                        if ($sub && !in_array($sub, $structured[$cat]))
                            $structured[$cat][] = $sub;
                    }
                    ?>

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

        <script>
            const input = document.getElementById('searchInput');
            const hints = document.getElementById('searchHints');

            input.addEventListener('focus', () => {
                if (input.value.trim() === '') {
                    hints.style.display = 'block';
                }
            });

            input.addEventListener('input', () => {
                if (input.value.trim() === '') {
                    hints.style.display = 'block';
                } else {
                    hints.style.display = 'none';
                }
            });

            input.addEventListener('blur', () => {
                setTimeout(() => { hints.style.display = 'none'; }, 100); // delay to allow click
            });

            function fillSearch(value) {
                input.value = value;
                hints.style.display = 'none';
            }
        </script>



    </header>

    <script>
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
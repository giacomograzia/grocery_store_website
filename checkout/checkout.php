<?php
session_start();

if (empty($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: ../cart/cart.php');
    exit;
}

require_once '../database.php';

foreach ($_SESSION['cart'] as $key => $details) {
    $productName = $details['name'];
    $unitQuantity = $details['unit_quantity'];
    $desiredQty = $details['quantity'];

    $stmt = $conn->prepare("SELECT in_stock FROM products WHERE product_name = ? AND unit_quantity = ?");
    $stmt->bind_param("ss", $productName, $unitQuantity);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || $desiredQty > $row['in_stock']) {
        $_SESSION['error'] = "Sorry, '$productName ($unitQuantity)' is no longer available in the requested quantity.";
        header('Location: ../cart/cart.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout - Grocery Store</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f4f0;
            display: flex;
            flex-direction: column;
        }

        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .checkout-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 20px 40px;
            /* top | sides | bottom */
        }

        .checkout-card {
            width: 100%;
            max-width: 800px;
            background: transparent;
            padding: 30px;
            border-radius: 12px;
            animation: fadeSlideUp 0.6s ease;
        }

        .checkout-card h2 {
            text-align: center;
            font-size: 28px;
            color: #314401;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 240px;
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #314401;
            margin-bottom: 6px;
        }

        input,
        select {
            padding: 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: rgba(255, 255, 255, 0.9);
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        input:focus,
        select:focus {
            border-color: #94a84e;
            box-shadow: 0 0 0 3px rgba(148, 168, 78, 0.2);
            outline: none;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='16'%20height='16'%20fill='gray'%20class='bi%20bi-chevron-down'%20viewBox='0%200%2016%2016'%3E%3Cpath%20fill-rule='evenodd'%20d='M1.646%204.646a.5.5%200%200%201%20.708%200L8%2010.293l5.646-5.647a.5.5%200%200%201%20.708.708l-6%206a.5.5%200%200%201-.708%200l-6-6a.5.5%200%200%201%200-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
        }

        .error-msg {
            color: #c0392b;
            font-size: 13px;
            margin-top: 4px;
            display: none;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .form-button {
            background-color: #314401;
            color: white;
            padding: 12px 18px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .form-button:hover {
            background-color: #94a84e;
        }

        .back-button {
            background-color: #ccc;
            color: #333;
            padding: 12px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .back-button:hover {
            background-color: #bbb;
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
    </style>
</head>

<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/templates/header_simple.php'; ?>

    <div class="page-wrapper">
        <div class="checkout-container">
            <div class="checkout-card">
                <h2>Delivery Details</h2>
                <form action="process_order.php" method="POST" id="checkoutForm" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" required>
                            <div class="error-msg" id="firstNameError">Please enter your first name.</div>
                        </div>
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <input type="text" name="surname" id="surname" required>
                            <div class="error-msg" id="surnameError">Please enter your surname.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="street">Street Address</label>
                        <input type="text" name="street" id="street" required>
                        <div class="error-msg" id="streetError">Please enter your street address.</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City / Suburb</label>
                            <input type="text" name="city" id="city" required>
                            <div class="error-msg" id="cityError">Please enter your city or suburb.</div>
                        </div>
                        <div class="form-group">
                            <label for="state">State / Territory</label>
                            <select name="state" id="state" required>
                                <option value="">Select State</option>
                                <option value="NSW">NSW</option>
                                <option value="VIC">VIC</option>
                                <option value="QLD">QLD</option>
                                <option value="WA">WA</option>
                                <option value="SA">SA</option>
                                <option value="TAS">TAS</option>
                                <option value="ACT">ACT</option>
                                <option value="NT">NT</option>
                                <option value="Others">Others</option>
                            </select>
                            <div class="error-msg" id="stateError">Please select a state or territory.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mobile">Australian Mobile Number</label>
                        <input type="text" name="mobile" id="mobile" required>
                        <div class="error-msg" id="mobileError">Enter a valid Australian mobile number (e.g. 04XXXXXXXX
                            or +614XXXXXXXX).</div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" required>
                        <div class="error-msg" id="emailError">Please enter a valid email address.</div>
                    </div>

                    <div class="form-actions">
                        <a href="../cart/cart.php" class="back-button">← Back to Cart</a>
                        <button type="submit" class="form-button">Place Order</button>
                    </div>
                </form>
            </div>
        </div>

        <?php include '../templates/footer.php'; ?>
    </div>

    <script>
        const form = document.getElementById('checkoutForm');

        function validateMobile(value) {
            return /^(\+61|0)4\d{8}$/.test(value);
        }

        function validateEmail(value) {
            const pattern = /^[^\s@]+@[^\s@]+\.(com|org|net|edu(\.au)?|gov(\.au)?|com\.au|co\.uk|it|de|fr|nl|ca|au)$/i;
            return pattern.test(value);
        }

        form.addEventListener('submit', function (e) {
            let valid = true;

            const fields = [
                { id: "first_name", errorId: "firstNameError" },
                { id: "surname", errorId: "surnameError" },
                { id: "street", errorId: "streetError" },
                { id: "city", errorId: "cityError" },
                { id: "state", errorId: "stateError", isSelect: true },
                { id: "mobile", errorId: "mobileError", customCheck: validateMobile },
                { id: "email", errorId: "emailError", customCheck: validateEmail }
            ];

            fields.forEach(field => {
                const input = document.getElementById(field.id);
                const error = document.getElementById(field.errorId);
                const value = input.value.trim();

                if (
                    (!field.isSelect && value === "") ||
                    (field.isSelect && value === "") ||
                    (field.customCheck && !field.customCheck(value))
                ) {
                    error.style.display = 'block';
                    valid = false;
                } else {
                    error.style.display = 'none';
                }
            });

            if (!valid) e.preventDefault();
        });
    </script>

</body>

</html>
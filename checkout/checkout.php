<?php
session_start();

// Redirect to cart if it's empty or not set
if (empty($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: ../cart/cart.php');
    exit;
}

require_once '../database.php';

// Re-check stock availability for each item in the cart
foreach ($_SESSION['cart'] as $key => $details) {
    $productName = $details['name'];
    $unitQuantity = $details['unit_quantity'];
    $desiredQty = $details['quantity'];

    // Query to get current stock
    $stmt = $conn->prepare("SELECT in_stock FROM products WHERE product_name = ? AND unit_quantity = ?");
    $stmt->bind_param("ss", $productName, $unitQuantity);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Redirect back if not enough stock
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
    <!-- Load main CSS -->
    <link rel="stylesheet" href="/grocery_store/styles.css?v=<?php echo time(); ?>">
</head>

<body>
    <!-- Shared header -->
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/grocery_store/templates/header_simple.php'; ?>

    <!-- Page wrapper to center content -->
    <div class="page-wrapper">
        <div class="checkout-container">
            <div class="checkout-card fade-slide-up">
                <h2>Delivery Details</h2>

                <!-- Checkout form -->
                <form action="process_order.php" method="POST" id="checkoutForm" novalidate>
                    <!-- Name fields -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name <span style="color: red;">*</span></label>
                            <input type="text" name="first_name" id="first_name" required>
                            <div class="error-msg" id="firstNameError">Please enter your first name.</div>
                        </div>

                        <div class="form-group">
                            <label for="surname">Surname <span style="color: red;">*</span></label>
                            <input type="text" name="surname" id="surname" required>
                            <div class="error-msg" id="surnameError">Please enter your surname.</div>
                        </div>
                    </div>

                    <!-- Street address -->
                    <div class="form-group">
                        <label for="street">Street Address <span style="color: red;">*</span></label>
                        <input type="text" name="street" id="street" required>
                        <div class="error-msg" id="streetError">Please enter your street address.</div>
                    </div>

                    <!-- City and State -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City / Suburb <span style="color: red;">*</span></label>
                            <input type="text" name="city" id="city" required>
                            <div class="error-msg" id="cityError">Please enter your city or suburb.</div>
                        </div>

                        <div class="form-group">
                            <label for="state">State / Territory <span style="color: red;">*</span></label>
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

                    <!-- Mobile number -->
                    <div class="form-group">
                        <label for="mobile">Australian Mobile Number <span style="color: red;">*</span></label>
                        <input type="text" name="mobile" id="mobile" required>
                        <div class="error-msg" id="mobileError">
                            Enter a valid Australian mobile number (e.g. 04XXXXXXXX or +614XXXXXXXX).
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email Address <span style="color: red;">*</span></label>
                        <input type="email" name="email" id="email" required>
                        <div class="error-msg" id="emailError"> Please enter a valid email address.</div>
                    </div>

                    <p style="font-size: 14px; color: #666; margin-bottom: 20px;">
                        <span style="color: red;">*</span> Required Information.
                    </p>

                    <!-- Form actions: back + submit -->
                    <div class="form-actions">
                        <a href="../cart/cart.php" class="back-button">← Back to Cart</a>
                        <button type="submit" class="form-button">Place Order</button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Footer -->
        <?php include '../templates/footer.php'; ?>
    </div>

    <!-- JavaScript for client-side form validation -->
    <script>
        const form = document.getElementById('checkoutForm');

        // Validates AU mobile numbers
        function validateMobile(value) {
            return /^(\+61|0)4\d{8}$/.test(value);
        }

        // Basic email validator with common TLDs
        function validateEmail(value) {
            const pattern = /^[^\s@]+@[^\s@]+\.(com|org|net|edu(\.au)?|gov(\.au)?|com\.au|co\.uk|it|de|fr|nl|ca|au)$/i;
            return pattern.test(value);
        }

        // Validate form fields on submit
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

                // Display error if empty or invalid
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

            if (!valid) e.preventDefault(); // Prevent form submission
        });
    </script>
</body>

</html>
<?php
// Include database connection
include('db_connect.php');

// Fetch full details for the selected model based on model_name
$model_name = isset($_POST['selectedModel']) ? $_POST['selectedModel'] : 'Unknown Model';
$stmt = $conn->prepare("SELECT id, category, model_name, power, acceleration, top_speed, price, image_path FROM porsche_models WHERE model_name = ?");
$stmt->bind_param("s", $model_name);
$stmt->execute();
$result = $stmt->get_result();

// Default values for model details
$id = null;
$category = "Default Category";
$power = "Unknown";
$acceleration = "Unknown";
$top_speed = "Unknown";
$total_price = 50.00; // Default price
$image_path = "default_image.jpg"; // Default image path

if ($result->num_rows > 0) {
    // Fetch the model details
    $model = $result->fetch_assoc();
    $id = $model['id'];
    $category = $model['category'];
    $model_name = $model['model_name'];
    $power = $model['power'];
    $acceleration = $model['acceleration'];
    $top_speed = $model['top_speed'];
    $total_price = (float)$model['price'];
    $image_path = $model['image_path'];
}

// Generate random order number
$orderNumber = mt_rand(10000000, 99999999);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitPayment'])) {
    // Sanitize and validate input data
    $card_number = isset($_POST['credit-card']) ? preg_replace('/\D/', '', $_POST['credit-card']) : '';
    $card_holder_name = isset($_POST['card-name']) ? trim($_POST['card-name']) : '';
    $cvv = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';
    $exp_month = isset($_POST['exp-month']) ? trim($_POST['exp-month']) : '';
    $exp_year = isset($_POST['exp-year']) ? trim($_POST['exp-year']) : '';

    // Add these lines to capture customer information
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
    $customer_address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : '';

    // Get the model name and total price from the hidden input
    $selectedModel = isset($_POST['selectedModel']) ? htmlspecialchars($_POST['selectedModel']) : '';

    // Insert the payment details into the database
    $stmt = $conn->prepare(
        "INSERT INTO payments (order_number, model_name, card_number, card_holder_name, exp_month, exp_year, cvv, total_price, customer_names, customer_email, customer_addresses, payment_date) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
    );

    // Update bind_param to use the correct variables and their types
    $stmt->bind_param(
        "sssssssdsss",
        $orderNumber,
        $selectedModel,  // Use selectedModel instead of model_name
        $card_number,
        $card_holder_name,
        $exp_month,
        $exp_year,
        $cvv,
        $total_price,   // Make sure total_price is available from earlier in the script
        $customer_name,
        $customer_email,
        $customer_address
    );

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to invoice page after successful payment
        header("Location: invoice.php?orderNumber=$orderNumber");
        exit; // Always call exit after header redirect
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway | Porsche MY</title>
    <link rel="stylesheet" href="stylescard.css">
</head>

<body>
    <div class="container">
        <div class="payment-container">
            <h1>Complete Your Purchase</h1>

            <div class="order-details">
                <h2>Order Summary</h2>
                <p><strong>Model:</strong> <?php echo htmlspecialchars($model_name); ?></p>
                <p><strong>Power:</strong> <?php echo htmlspecialchars($power); ?></p>
                <p><strong>Acceleration:</strong> <?php echo htmlspecialchars($acceleration); ?></p>
                <p><strong>Top Speed:</strong> <?php echo htmlspecialchars($top_speed); ?></p>
                <p><strong>Total Price:</strong> RM <?php echo number_format($total_price, 2); ?></p>
            </div>

            <form action="" method="POST">
                <input type="hidden" name="selectedModel" value="<?php echo htmlspecialchars($model_name); ?>">
                <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">

                <h3>Payment Details</h3>

                <div>
                    <label for="credit-card">Credit Card Number</label>
                    <input type="text" placeholder="XXXX - XXXX - XXXX - XXXX" name="credit-card" id="credit-card" required>
                </div>

                <div>
                    <label for="card-name">Cardholder's Name</label>
                    <input type="text" name="card-name" id="card-name" required>
                </div>

                <div class="expiration-date">
                    <label for="exp-month">Expiration Date</label>
                    <div class="exp-inputs">
                        <input type="number" placeholder="MM" name="exp-month" id="exp-month" min="1" max="12" required>
                        <span>/</span> <!-- Slash between the inputs -->
                        <input type="number" placeholder="YY" name="exp-year" id="exp-year" required>
                    </div>
                </div>

                <div class="cvv">
                    <label for="cvv">CVV</label>
                    <input type="number" name="cvv" id="cvv" required>
                </div>

                <div class="card-virtual">
                    <img src="images/credit card.png">
                    <p class="name-holder">Card Holder's Name</p>
                    <p class="highlight">
                        <span class="last-digit" id="card-number">1234 1234 1234 1234</span>
                        <span class="expiry">
                            <span class="exp-month" id="exp-month">XX</span> /
                            <span class="exp-year" id="exp-year">XX</span>
                        </span>
                    </p>
                </div>

                <h3>Personal Information</h3>
                <div>
                    <label for="customer-name">Full Name</label>
                    <input type="text" name="customer_name" id="customer-name" required>
                </div>

                <div>
                    <label for="customer-email">Email</label>
                    <input type="email" name="customer_email" id="customer-email" required>
                </div>

                <div>
                    <label for="customer-address">Address</label>
                    <textarea name="customer_address" id="customer-address" rows="4" required></textarea>
                </div>

                <button type="submit" name="submitPayment">Pay Now</button>
            </form>
        </div>
    </div>
    <script src="cardscript.js"></script>
</body>

</html>
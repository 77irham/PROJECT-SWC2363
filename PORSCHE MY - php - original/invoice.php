<?php
// Include database connection
include('db_connect.php');

// Fetch the payment details using the order number
$orderNumber = isset($_GET['orderNumber']) ? $_GET['orderNumber'] : '';

if ($orderNumber) {
    // Join `payments` table with `porsche_models` table to get car model details
    $stmt = $conn->prepare("SELECT p.*, m.power, m.acceleration, m.top_speed 
                            FROM payments p 
                            LEFT JOIN porsche_models m ON p.model_name = m.model_name 
                            WHERE p.order_number = ?");
    $stmt->bind_param("s", $orderNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $paymentDetails = $result->fetch_assoc();
        $paymentSuccess = true;  // Flag to indicate payment success
    } else {
        echo "<p>No payment details found for this order number.</p>";
        $paymentSuccess = false;
    }
} else {
    echo "<p>Order number is required.</p>";
    $paymentSuccess = false;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice | Porsche MY</title>
    <link rel="stylesheet" href="stylesinvoice.css">
</head>

<body>
    <div class="invoice-wrapper">
        <div class="invoice-container">
            <header class="invoice-header">
                <div class="company-info">
                    <h1>Porsche MY</h1>
                    <p>INVOICE FROM : ADMIN IRHAM</p>
                    <p><strong>CONTACT:</strong> +6018 297 0920</p>
                    <p><strong>EMAIL:</strong> admin@porsche.my</p>
                </div>
                <div class="invoice-details">
                    <h2>Invoice:</h2>
                    <p><strong>ORDER NUMBER :</strong> #<?php echo htmlspecialchars($paymentDetails['order_number']); ?></p>
                    <p><strong>DATE :</strong> <?php echo htmlspecialchars($paymentDetails['payment_date']); ?></p>
                </div>
            </header>

            <div class="order-summary">
                <h3>ORDER DETAILS</h3>
                <table>
                    <tr>
                        <td><strong>Model:</strong> <?php echo htmlspecialchars($paymentDetails['model_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Power:</strong> <?php echo htmlspecialchars($paymentDetails['power']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Acceleration:</strong> <?php echo htmlspecialchars($paymentDetails['acceleration']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Top Speed:</strong> <?php echo htmlspecialchars($paymentDetails['top_speed']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Price:</strong> RM <?php echo number_format($paymentDetails['total_price'], 2); ?></td>
                    </tr>
                </table>
            </div>

            <div class="payment-details">
                <h3>PAYMENT INFORMATION</h3>
                <table>
                    <tr>
                        <td><strong>Card Number:</strong> <?php echo '**** **** **** ' . substr($paymentDetails['card_number'], -4); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Cardholder Name:</strong> <?php echo htmlspecialchars($paymentDetails['card_holder_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Expiration Date:</strong> <?php echo htmlspecialchars($paymentDetails['exp_month']) . '/' . htmlspecialchars($paymentDetails['exp_year']); ?></td>
                    </tr>
                </table>
            </div>

            <div class="customer-info">
                <h3>CUSTOMER INFORMATION</h3>
                <table>
                    <tr>
                        <td><strong>Name:</strong> <?php echo htmlspecialchars($paymentDetails['customer_names']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong> <?php echo htmlspecialchars($paymentDetails['customer_email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($paymentDetails['customer_addresses'])); ?></td>
                    </tr>
                </table>
            </div>

            <footer class="invoice-footer">
                <p>Thank you for your purchase! We hope you enjoy your Porsche.</p>
            </footer>

            <!-- Buttons Section -->
            <div class="invoice-buttons">
                <button onclick="backHomepage()">< Back to Home</button>
                <button onclick="printInvoice()">Print</button>
            </div>
        </div>
    </div>

    <!-- Toast message -->
    <div id="toast" class="toast">Payment Successful !</div>

    <script>
        // JavaScript function to trigger the print dialog
        function printInvoice() {
            window.print();  // This triggers the print dialog of the browser
        }
        
        // Function to navigate back to home page
        function backHomepage() {
            window.location.href = 'index.php';
        }

        // Show toast message when payment is successful
        <?php if ($paymentSuccess): ?>
            window.onload = function() {
                var toast = document.getElementById("toast");
                toast.classList.add("show");
                setTimeout(function() {
                    toast.classList.remove("show");
                }, 3000); // Toast will disappear after 3 seconds
            }
        <?php endif; ?>
    </script>

</body>
</html>

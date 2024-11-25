<?php
// Start session and include database connection
session_start();
include 'db_connect.php';

// Check if user is an admin
$isAdmin = true; // Replace with actual admin verification logic

if (!$isAdmin) {
    header('Location: loginAndRegister.php');
    exit();
}

// Retrieve all data from the payments table
$sql = "SELECT id, order_number, model_name, card_number, card_holder_name, exp_month, exp_year, cvv, total_price, customer_names, customer_email, customer_addresses, payment_date FROM payments";
$result = $conn->query($sql);
$paymentsData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $paymentsData[] = $row;
    }
}

// Retrieve sales data from payments table, based on model_name count (quantity of each model sold)
$salesSql = "SELECT model_name, COUNT(*) AS quantity_sold
             FROM payments
             GROUP BY model_name";
$salesResult = $conn->query($salesSql);
$salesData = [];

if ($salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        $salesData[] = [
            'model_name' => $row['model_name'],
            'quantity_sold' => $row['quantity_sold']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche MY | Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #333;
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            transition: transform 0.3s ease;
        }

        .sidebar h2 {
            text-align: center;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            margin-top: 70px;
        }

        .sidebar.hidden {
            transform: translateX(-250px);
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px;
        }

        .sidebar ul li a {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            color: #fff;
            text-decoration: none;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .main-content h1 {
            margin-top: 20px;
            text-align: center;
        }

        .main-content.expanded {
            margin-left: 0;
            width: 100%;
        }

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

th{
    background-color: #f4f4f4;
    border: 1px solid #ddd;
    padding: 15px 20px;
    text-align: left;
    word-wrap: break-word;
    white-space: nowrap;  /* Prevent word wrapping */
}

td{
    background-color: #fff;
    border: 1px solid #ddd;
    padding: 15px 20px;
    text-align: left;
    word-wrap: break-word;
    white-space: nowrap;  /* Prevent word wrapping */
}


/* No fixed width for columns */
table th, table td {
    min-width: 50px; /* Set a min-width to ensure columns don't collapse completely */
}

/* Responsive Styles for Mobile */
@media screen and (max-width: 768px) {
    table th, table td {
        padding: 10px 15px;
        font-size: 12px;
    }

    /* Allow the table to auto adjust based on screen size */
    table {
        width: 100%;
        overflow-x: auto;
    }
}

table td {
    font-size: 14px;
    color: #333;
}
        /* Toggle Button */
        .toggle-btn {
            position: fixed;
            background-color: #fff;
            border-radius: 10px;
            top: 20px;
            left: 20px;
            color: #000;
            border: 3px solid #000;
            padding: 10px 15px;
            cursor: pointer;
            z-index: 1000;
        }

        /* Chart Container */
        .chart-container {
            padding-bottom: 100px;
            border-bottom: 2px solid #000;
            width: 80%;
            margin: 40px auto;
        }

        /* Center align h3 elements */
        h3 {
            font-weight: 900;
            text-align: center;
            margin-bottom: 20px; /* Optional: Adds space below the heading */
            font-family: 'Arial', sans-serif; /* Customize font if needed */
            font-size: 1.5em; /* Adjust the size */
            color: #333; /* Set text color */
        }

    </style>
</head>
<body>
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="adminDashboard.php">Overview</a></li>
                <li><a href="userManagement.php">User Management</a></li>
                <li><a href="carManagement.php">Car Inventory Management</a></li>
                <li><a href="#">Sales Performance Report</a></li>
                <li><a href="customerContact.php">Customer Contacts</a></li>
                <li><a href="index.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <header>
                <h1>SALES PERFORMANCE REPORT</h1>
            </header>

            <!-- Bar Chart -->
            <div class="chart-container">
                <h3>BAR CHART</h3>
                <canvas id="salesBarChart"></canvas>
            </div>

            <!-- Data Table -->
            <div class="table-container">
                <table>
                    <h3>SALES REPORT</h3>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order Number</th>
                            <th>Model Name</th>
                            <th>Card Number</th>
                            <th>Card Holder</th>
                            <th>Exp Month</th>
                            <th>Exp Year</th>
                            <th>CVV</th>
                            <th>Total Price</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paymentsData as $payment) { ?>
                            <tr>
                                <td><?php echo $payment['id']; ?></td>
                                <td><?php echo $payment['order_number']; ?></td>
                                <td><?php echo $payment['model_name']; ?></td>
                                <td><?php echo $payment['card_number']; ?></td>
                                <td><?php echo $payment['card_holder_name']; ?></td>
                                <td><?php echo $payment['exp_month']; ?></td>
                                <td><?php echo $payment['exp_year']; ?></td>
                                <td><?php echo $payment['cvv']; ?></td>
                                <td><?php echo $payment['total_price']; ?></td>
                                <td><?php echo $payment['customer_names']; ?></td>
                                <td><?php echo $payment['customer_email']; ?></td>
                                <td><?php echo $payment['customer_addresses']; ?></td>
                                <td><?php echo $payment['payment_date']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle sidebar visibility
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
        }

        // Extract sales data from PHP into JavaScript
        const salesData = <?php echo json_encode($salesData); ?>;

        const modelNames = salesData.map(data => data.model_name);
        const quantitySold = salesData.map(data => data.quantity_sold);

        // Create the Bar chart (Sales Performance)
        const ctx = document.getElementById('salesBarChart').getContext('2d');
        const salesBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: modelNames,
                datasets: [{
                    label: 'Total Units Sold',
                    data: quantitySold,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Units Sold'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Model Name'
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });
    </script>
</body>
</html>

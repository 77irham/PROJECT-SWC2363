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

// Count new users (last 30 days)
$totalUsersQuery = $conn->query("SELECT COUNT(*) AS total FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
$totalUsers = $totalUsersQuery->fetch_assoc()['total'];

$totalCarsQuery = $conn->query("SELECT COUNT(DISTINCT id) AS total FROM payments");
$totalCars = $totalCarsQuery->fetch_assoc()['total'];

$topCarQuery = $conn->query("SELECT model_name, COUNT(*) AS count 
                             FROM payments 
                             GROUP BY model_name 
                             ORDER BY count DESC 
                             LIMIT 1");
$topCar = $topCarQuery->fetch_assoc();
$topCarModel = $topCar ? $topCar['model_name'] : 'No data';

$totalSalesQuery = $conn->query("SELECT SUM(total_price) AS total_sales FROM payments");
$totalSales = $totalSalesQuery->fetch_assoc()['total_sales'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche MY | Admin Dashboard</title>
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
            margin-left: 250px; /* Align to the right of the sidebar */
            padding: 20px;
            width: calc(100% - 250px); /* Adjust width to fit with the sidebar */
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
            width: 100%; /* Full width when sidebar is hidden */
        }

        header {
            margin-bottom: 20px;
        }

        header h1 {
            margin-top: 70px;
            font-size: 28px;
            color: #2d3436;
        }

        /* Stats Section */
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex: 1;
        }

        .stat-box h3 {
            font-size: 18px;
            color: #555;
        }

        .stat-box p {
            font-size: 24px;
            color: #333;
            margin-top: 10px;
        }

        /* Toggle Button */
        .toggle-btn {
            position: fixed;
            background-color: #fff;
            border-radius: 10px;
            top: 20px;
            left: 20px;
            color: #000; /* Default color when sidebar is closed */
            border: 3px solid #000;;
            padding: 10px 15px;
            cursor: pointer;
            z-index: 1000;
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
                <li><a href="#">Overview</a></li>
                <li><a href="userManagement.php">User Management</a></li>
                <li><a href="carManagement.php">Car Inventory Management</a></li>
                <li><a href="salesPerformance.php">Sales Performance Report</a></li>
                <li><a href="customerContact.php">Customer Contacts</a></li>
                <li><a href="index.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <header>
                <h1>Welcome, Admin</h1>
            </header>

            <section class="stats">
                <div class="stat-box">
                    <h3>Total Sales</h3>
                    <p>RM <?php echo number_format($totalSales, 2); ?></p> <!-- Display total sales -->
                </div>
                <div class="stat-box">
                    <h3>Number of Cars Sold</h3>
                    <p><?php echo $totalCars; ?></p> <!-- Display total cars sold -->
                </div>
                <div class="stat-box">
                    <h3>Top Selling Car</h3>
                    <p><?php echo htmlspecialchars($topCarModel); ?></p> <!-- Display top-selling car -->
                </div>
                <div class="stat-box">
                    <h3>New Users (Last 30 Days)</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
            </section>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
        }
    </script>
</body>
</html>

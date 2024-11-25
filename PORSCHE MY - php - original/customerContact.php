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

// Retrieve all contacts
$contactsQuery = $conn->query("SELECT * FROM contact_us");

// Handle Search Request
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Modify the SQL query to search for contacts
$sql = "SELECT * FROM contact_us WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR subject LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%" . $searchQuery . "%";
$stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$contactsQuery = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Contacts</title>
    <link rel="stylesheet" href="adminDashboard.css">
    <style>
        /* Sidebar and Main Content styles from car.php */
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

        header {
            margin-bottom: 20px;
        }

        header h1 {
            margin-top: 70px;
            font-size: 28px;
            color: #2d3436;
        }

        /* Stats Section */
        table {
            margin: auto;
            font-family: 'Times New Roman', serif;
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }

        .action-btn {
            padding: 5px 10px;
            margin: 5px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
        }

        /* Search Form Styles */
        .search-form {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .search-form input[type="text"] {
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 50px;
            width: 250px;
            margin-right: 10px;
            transition: border-color 0.3s ease;
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
        }

        .search-form .search-btn {
            padding: 8px 15px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-form .search-btn:hover {
            background-color: #0056b3;
        }

        .search-form .search-btn:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
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
            <li><a href="adminDashboard.php">Overview</a></li>
            <li><a href="userManagement.php">User Management</a></li>
            <li><a href="carManagement.php">Car Inventory Management</a></li>
            <li><a href="salesPerformance.php">Sales Performance Report</a></li>
            <li><a href="#">Customer Contacts</a></li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <header>
            <h1>CUSTOMER CONTACTS</h1>
            <form method="POST" action="" class="search-form">
                <input type="text" name="search" placeholder="Search by name, email, or subject" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="search-btn" type="submit">Search</button>
            </form>
        </header>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($contact = $contactsQuery->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $contact['id']; ?></td>
                        <td><?php echo $contact['first_name']; ?></td>
                        <td><?php echo $contact['last_name']; ?></td>
                        <td><?php echo $contact['email']; ?></td>
                        <td><?php echo $contact['subject']; ?></td>
                        <td><?php echo nl2br(htmlspecialchars($contact['message'])); ?></td>
                        <td><?php echo $contact['submitted_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Toggle Sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        sidebar.classList.toggle('hidden');
        mainContent.classList.toggle('expanded');
    }
</script>
</body>
</html>

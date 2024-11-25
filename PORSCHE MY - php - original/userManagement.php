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

// Handle Edit or Delete Requests
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $numbers = $_POST['numbers'];
    $addresses = $_POST['addresses'];
    $names = $_POST['names'];
    $role = $_POST['role'];

    // Check if password is provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $updateQuery = $conn->prepare("UPDATE users SET username=?, email=?, numbers=?, addresses=?, names=?, role=?, passwords=? WHERE id=?");
        $updateQuery->bind_param("sssssssi", $username, $email, $numbers, $addresses, $names, $role, $password, $id);
    } else {
        $updateQuery = $conn->prepare("UPDATE users SET username=?, email=?, numbers=?, addresses=?, names=?, role=? WHERE id=?");
        $updateQuery->bind_param("ssssssi", $username, $email, $numbers, $addresses, $names, $role, $id);
    }
    $updateQuery->execute();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // SQL query to delete user
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE id=?");
    $deleteQuery->bind_param("i", $id);
    $deleteQuery->execute();

    // Redirect back to the same page after deletion
    header('Location: userManagement.php');
    exit();
}

// Handle Add New User Request
if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $numbers = $_POST['numbers'];
    $addresses = $_POST['addresses'];
    $names = $_POST['names'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the provided password

    // SQL query to insert new user
    $insertQuery = $conn->prepare("INSERT INTO users (username, email, numbers, addresses, names, role, passwords) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertQuery->bind_param("sssssss", $username, $email, $numbers, $addresses, $names, $role, $password);
    $insertQuery->execute();
}

// Retrieve all users
$usersQuery = $conn->query("SELECT * FROM users");

// Handle Search Request
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Modify the SQL query to search for users
$sql = "SELECT * FROM users WHERE username LIKE ? OR email LIKE ? OR names LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%" . $searchQuery . "%";
$stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
$stmt->execute();
$usersQuery = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="adminDashboard.css">
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

        .main-content h1 {
            margin-top: 20px;
            text-align: center;
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
        .delete-btn {
            background-color: #ff4d4d;
        }
        .edit-btn {
            background-color: #4caf50;
        }

        .add-btn {
            display: block;
            width: 100px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
            z-index: 1000;
        }

        /* Modal Header */
        .modal h2 {
            margin-bottom: 20px;
        }

        /* Modal Input Fields */
        .modal label {
            display: block;
            margin: 10px 0 5px;
        }

        .modal input, .modal select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Modal Buttons */
        .modal button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }

        .modal button[type="button"] {
            background-color: #ccc;
        }

        /* Modal Close (Cancel) Button */
        .modal button[type="button"]:hover, .modal button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Active Modal and Overlay */
        .modal.active, .modal-overlay.active {
            display: block;
        }

        /* Delete Confirmation Modal */
        .confirmation-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
            z-index: 1000;
        }

        .confirmation-modal.active {
            display: block;
        }

        .confirmation-modal p {
            font-size: 18px;
            text-align: center;
        }

        .confirmation-modal button {
            margin: 5px;
        }
        
        /* Search Form Styles */
        .search-form {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px; /* Space above the form */
        }

        .search-form input[type="text"] {

            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 50px;
            width: 250px; /* Set width of input field */
            margin-right: 10px; /* Space between input field and button */
            transition: border-color 0.3s ease;
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #007bff; /* Highlight input when focused */
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
            <li><a href="#">User Management</a></li>
            <li><a href="carManagement.php">Car Inventory Management</a></li>
            <li><a href="salesPerformance.php">Sales Performance Report</a></li>
            <li><a href="customerContact.php">Customer Contacts</a></li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <header>
            <h1>USER MANAGEMENT</h1>
            <form method="POST" action="" class="search-form">
                <input type="text" name="search" placeholder="Search by username or email" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="search-btn" type="submit">Search</button>
            </form>
            <a href="javascript:void(0);" class="add-btn" onclick="openAddUserModal()">Add New User</a>
        </header>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usersQuery->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['numbers']; ?></td>
                        <td><?php echo $user['addresses']; ?></td>
                        <td><?php echo $user['names']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td><?php echo $user['created_at']; ?></td>
                        <td>
                            <a href="#" class="action-btn edit-btn" onclick="openEditModal(
                                <?php echo $user['id']; ?>, 
                                '<?php echo addslashes($user['username']); ?>', 
                                '<?php echo addslashes($user['email']); ?>', 
                                '<?php echo addslashes($user['numbers']); ?>', 
                                '<?php echo addslashes($user['addresses']); ?>', 
                                '<?php echo addslashes($user['names']); ?>', 
                                '<?php echo addslashes($user['role']); ?>'
                            )">Edit</a>
                            <a href="javascript:void(0);" class="action-btn delete-btn" onclick="openDeleteConfirmationModal(<?php echo $user['id']; ?>)">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

 
<!-- Add User Modal -->
<div id="addUserModal" class="modal">
        <h2>Add New User</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="numbers">Phone Number:</label>
            <input type="text" id="numbers" name="numbers" required><br>

            <label for="addresses">Address:</label>
            <input type="text" id="addresses" name="addresses" required><br>

            <label for="names">Name:</label>
            <input type="text" id="names" name="names" required><br>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="User">User</option>
                <option value="Administrator">Administrator</option>
            </select><br>

            <button type="submit" name="add">Add User</button>
            <button type="button" onclick="closeAddUserModal()">Cancel</button>
        </form>
    </div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
        <h2>Edit User</h2>
        <form method="POST" action="">
            <input type="hidden" id="id" name="id">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="numbers">Phone Number:</label>
            <input type="text" id="numbers" name="numbers" required><br>

            <label for="addresses">Address:</label>
            <input type="text" id="addresses" name="addresses" required><br>

            <label for="names">Name:</label>
            <input type="text" id="names" name="names" required><br>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="User">User</option>
                <option value="Administrator">Administrator</option>
            </select><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Leave blank to keep current password"><br>

            <button type="submit" name="edit">Save Changes</button>
            <button type="button" onclick="closeEditUserModal()">Cancel</button>
        </form>
    </div>
<!-- Delete Confirmation Modal -->
<div id="confirmationModal" class="confirmation-modal">
        <p>Are you sure you want to delete this user?</p>
        <button id="confirmDelete" onclick="confirmDelete()">Yes</button>
        <button type="button" onclick="closeDeleteConfirmationModal()">No</button>
    </div>

    <!-- Modal Overlay -->
    <div id="modalOverlay" class="modal-overlay"></div>

<script>
    // Toggle Sidebar
    function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
        }

        // Open Edit Modal
        function openEditModal(id, username, email, numbers, addresses, names, role) {
            document.getElementById('editUserModal').querySelector('#id').value = id;
            document.getElementById('editUserModal').querySelector('#username').value = username;
            document.getElementById('editUserModal').querySelector('#email').value = email;
            document.getElementById('editUserModal').querySelector('#numbers').value = numbers;
            document.getElementById('editUserModal').querySelector('#addresses').value = addresses;
            document.getElementById('editUserModal').querySelector('#names').value = names;
            document.getElementById('editUserModal').querySelector('#role').value = role;

            // Open modal and overlay
            document.getElementById('editUserModal').classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
        }

        // Close Edit User Modal
        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.remove('active');
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Open Add User Modal
        function openAddUserModal() {
            document.getElementById('addUserModal').classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
        }

        // Close Add User Modal
        function closeAddUserModal() {
            document.getElementById('addUserModal').classList.remove('active');
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Open Delete Confirmation Modal
        function openDeleteConfirmationModal(userId) {
            document.getElementById('confirmationModal').classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('confirmDelete').setAttribute('data-id', userId);
        }

        // Close Delete Confirmation Modal
        function closeDeleteConfirmationModal() {
            document.getElementById('confirmationModal').classList.remove('active');
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Confirm Delete
        function confirmDelete() {
            const userId = document.getElementById('confirmDelete').getAttribute('data-id');
            window.location.href = "?delete=" + userId;
        }

        // Instant Search Function (optional)
        const searchInput = document.querySelector('[name="search"]');
        searchInput.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');
        
        rows.forEach(row => {
        const username = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const name = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
        
        if (username.includes(searchValue) || email.includes(searchValue) || name.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
</body>
</html>

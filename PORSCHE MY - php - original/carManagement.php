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

// Handle Add Car Request
if (isset($_POST['add'])) {
    $category = $_POST['category'];
    $model_name = $_POST['model_name'];
    $power = $_POST['power'];
    $acceleration = $_POST['acceleration'];
    $top_speed = $_POST['top_speed'];
    $price = $_POST['price'];
    $image_path = $_POST['image_path'];

    // SQL query to insert new car model
    $insertQuery = $conn->prepare("INSERT INTO porsche_models (category, model_name, power, acceleration, top_speed, price, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertQuery->bind_param("sssssss", $category, $model_name, $power, $acceleration, $top_speed, $price, $image_path);
    $insertQuery->execute();

    // Redirect to the car management page after adding
    header('Location: carManagement.php');
    exit();
}


// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // SQL query to delete car model
    $deleteQuery = $conn->prepare("DELETE FROM porsche_models WHERE id=?");
    $deleteQuery->bind_param("i", $id);
    $deleteQuery->execute();

    // Redirect back to the same page after deletion
    header('Location: carManagement.php');
    exit();
}

// Retrieve all car models
$modelsQuery = $conn->query("SELECT * FROM porsche_models");

// Handle Search Request
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Modify the SQL query to search for car models
$sql = "SELECT * FROM porsche_models WHERE model_name LIKE ? OR category LIKE ?";
$stmt = $conn->prepare($sql);
$searchParam = "%" . $searchQuery . "%";
$stmt->bind_param("ss", $searchParam, $searchParam);
$stmt->execute();
$modelsQuery = $stmt->get_result();

// Handle Edit Request
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $category = $_POST['category'];
    $model_name = $_POST['model_name'];
    $power = $_POST['power'];
    $acceleration = $_POST['acceleration'];
    $top_speed = $_POST['top_speed'];
    $price = $_POST['price'];
    $image_path = $_POST['image_path'];

    // SQL query to update car details
    $updateQuery = $conn->prepare("UPDATE porsche_models SET category=?, model_name=?, power=?, acceleration=?, top_speed=?, price=?, image_path=? WHERE id=?");
    $updateQuery->bind_param("sssssssi", $category, $model_name, $power, $acceleration, $top_speed, $price, $image_path, $id);
    $updateQuery->execute();

    // Redirect to the car management page
    header('Location: carManagement.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Management</title>
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
            margin-left: 250px;
            /* Align to the right of the sidebar */
            padding: 20px;
            width: calc(100% - 250px);
            /* Adjust width to fit with the sidebar */
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .main-content h1 {
            margin-top: 20px;
            text-align: center;
        }

        .main-content.expanded {
            margin-left: 0;
            width: 100%;
            /* Full width when sidebar is hidden */
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

        th,
        td {
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

        .modal input,
        .modal select {
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
        .modal button[type="button"]:hover,
        .modal button[type="submit"]:hover {
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
        .modal.active,
        .modal-overlay.active {
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
            margin-top: 20px;
            /* Space above the form */
        }

        .search-form input[type="text"] {

            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 50px;
            width: 250px;
            /* Set width of input field */
            margin-right: 10px;
            /* Space between input field and button */
            transition: border-color 0.3s ease;
        }

        .search-form input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
            /* Highlight input when focused */
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

        .delete-btn {
            background-color: #ff4d4d;
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
                <li><a href="#">Car Inventory Management</a></li>
                <li><a href="salesPerformance.php">Sales Performance Report</a></li>
                <li><a href="customerContact.php">Customer Contacts</a></li>
                <li><a href="index.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <header>
                <h1>CAR INVENTORY MANAGEMENT</h1>
                <form class="search-form">
                    <input type="text" id="searchInput" onkeyup="instantSearch()" placeholder="Search by model name or category">
                    <button class="search-btn" type="button" onclick="instantSearch()">Search</button>
                </form>

                <a href="javascript:void(0);" class="add-btn" onclick="openAddCarModal()">Add New Car</a>
            </header>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Model Name</th>
                        <th>Power</th>
                        <th>Acceleration</th>
                        <th>Top Speed</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($model = $modelsQuery->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $model['id']; ?></td>
                            <td><?php echo $model['category']; ?></td>
                            <td><?php echo $model['model_name']; ?></td>
                            <td><?php echo $model['power']; ?></td>
                            <td><?php echo $model['acceleration']; ?></td>
                            <td><?php echo $model['top_speed']; ?></td>
                            <td><?php echo $model['price']; ?></td>
                            <td><img src="<?php echo $model['image_path']; ?>" alt="Porsche MY" width="100"></td>
                            <td>
                                <a href="#" class="action-btn edit-btn" onclick="openEditModal(
                                        <?php echo $model['id']; ?>, 
                                        '<?php echo addslashes($model['category']); ?>', 
                                        '<?php echo addslashes($model['model_name']); ?>', 
                                        '<?php echo addslashes($model['power']); ?>', 
                                        '<?php echo addslashes($model['acceleration']); ?>', 
                                        '<?php echo addslashes($model['top_speed']); ?>', 
                                        '<?php echo addslashes($model['price']); ?>'
                                    )">Edit</a>
                                <a href="javascript:void(0);" class="action-btn delete-btn" onclick="openDeleteConfirmationModal(<?php echo $model['id']; ?>)">Delete</a>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add New Car Modal -->
    <div id="addCarModal" class="modal">
        <h2>Add New Car</h2>
        <form method="POST" action="carManagement.php">
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required><br>

            <label for="model_name">Model Name:</label>
            <input type="text" id="model_name" name="model_name" required><br>

            <label for="power">Power:</label>
            <input type="text" id="power" name="power" required><br>

            <label for="acceleration">Acceleration:</label>
            <input type="text" id="acceleration" name="acceleration" required><br>

            <label for="top_speed">Top Speed:</label>
            <input type="text" id="top_speed" name="top_speed" required><br>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required><br>

            <label for="image_path">Image Path:</label>
            <input type="text" id="image_path" name="image_path"><br>

            <button type="submit" name="add">Add Car</button>
            <button type="button" onclick="closeAddCarModal()">Cancel</button>
        </form>
    </div>


    <!-- Delete Confirmation Modal -->
    <div id="confirmationModal" class="confirmation-modal">
        <p>Are you sure you want to delete this car model?</p>
        <button id="confirmDelete" onclick="confirmDelete()">Yes</button>
        <button type="button" onclick="closeDeleteConfirmationModal()">No</button>
    </div>

    <!-- Modal Overlay -->
    <div id="modalOverlay" class="modal-overlay"></div>

    <!-- Edit Car Modal -->
    <div id="editCarModal" class="modal">
        <h2>Edit Car</h2>
        <form method="POST" action="carManagement.php">
            <input type="hidden" id="id" name="id">

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required><br>

            <label for="model_name">Model Name:</label>
            <input type="text" id="model_name" name="model_name" required><br>

            <label for="power">Power:</label>
            <input type="text" id="power" name="power" required><br>

            <label for="acceleration">Acceleration:</label>
            <input type="text" id="acceleration" name="acceleration" required><br>

            <label for="top_speed">Top Speed:</label>
            <input type="text" id="top_speed" name="top_speed" required><br>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required><br>

            <label for="image_path">Image Path:</label>
            <input type="text" id="image_path" name="image_path"><br>

            <button type="submit" name="edit">Save Changes</button>
            <button type="button" onclick="closeEditCarModal()">Cancel</button>
        </form>
    </div>


    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
        }

        // Open Add Car Modal
        function openAddCarModal() {
            document.getElementById('addCarModal').classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
        }

        // Close Add Car Modal
        function closeAddCarModal() {
            document.getElementById('addCarModal').classList.remove('active');
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Open Edit Modal and populate fields
        function openEditModal(id, category, model_name, power, acceleration, top_speed, price, image_path) {
            document.getElementById('editCarModal').querySelector('#id').value = id;
            document.getElementById('editCarModal').querySelector('#category').value = category;
            document.getElementById('editCarModal').querySelector('#model_name').value = model_name;
            document.getElementById('editCarModal').querySelector('#power').value = power;
            document.getElementById('editCarModal').querySelector('#acceleration').value = acceleration;
            document.getElementById('editCarModal').querySelector('#top_speed').value = top_speed;
            document.getElementById('editCarModal').querySelector('#price').value = price;
            document.getElementById('editCarModal').querySelector('#image_path').value = image_path;

            // Open modal and overlay
            document.getElementById('editCarModal').classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
        }

        // Close Edit Car Modal
        function closeEditCarModal() {
            document.getElementById('editCarModal').classList.remove('active');
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Open Delete Confirmation Modal
        function openDeleteConfirmationModal(carId) {
            document.getElementById('confirmationModal').classList.add('active');
            document.getElementById('modalOverlay').classList.add('active');
            document.getElementById('confirmDelete').setAttribute('data-id', carId);
        }

        // Close Delete Confirmation Modal
        function closeDeleteConfirmationModal() {
            document.getElementById('confirmationModal').classList.remove('active');
            document.getElementById('modalOverlay').classList.remove('active');
        }

        // Confirm Delete
        function confirmDelete() {
            const carId = document.getElementById('confirmDelete').getAttribute('data-id');
            window.location.href = "?delete=" + carId;
        }

        // Function to filter car models based on search input
        function instantSearch() {
            // Get the search input value
            const searchQuery = document.getElementById('searchInput').value.toLowerCase();

            // Get all table rows (excluding the header)
            const rows = document.querySelectorAll('table tbody tr');

            // Loop through all rows and hide those that don't match the search query
            rows.forEach(row => {
                const modelName = row.cells[2].textContent.toLowerCase();
                const category = row.cells[1].textContent.toLowerCase();

                // Check if the model name or category matches the search query
                if (modelName.includes(searchQuery) || category.includes(searchQuery)) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        }
    </script>
</body>
</html>
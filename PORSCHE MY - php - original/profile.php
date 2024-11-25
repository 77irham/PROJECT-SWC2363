<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    die("User not logged in. Redirecting...");
    header("Location: loginAndRegister.php");
    exit();
}

// Get logged-in user ID
$userId = $_SESSION['id'];

// Fetch user details
$query = "SELECT username, passwords, names, email, numbers, addresses, images FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("i", $userId);
if (!$stmt->execute()) {
    die("Query execution failed: " . $stmt->error);
}
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found in the database. User ID: $userId");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    // Sanitize input
    $names = trim($_POST['names']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $numbers = trim($_POST['numbers']);
    $addresses = trim($_POST['addresses']);
    $new_username = trim($_POST['username']);

    if (empty($names) || empty($email) || empty($numbers) || empty($new_username)) {
        die("All required fields must be filled out.");
    }

    // File upload
    $image_path = $user['images'];
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = uniqid() . '.' . pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION);
        $image_path = $upload_dir . $file_name;
        if (!move_uploaded_file($_FILES['profileImage']['tmp_name'], $image_path)) {
            die("Failed to upload file.");
        }
    }

    // Update database
    $update_query = "UPDATE users SET names = ?, email = ?, numbers = ?, addresses = ?, username = ?, images = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("ssssssi", $names, $email, $numbers, $addresses, $new_username, $image_path, $userId);
    if ($stmt->execute()) {
        echo "Profile updated successfully.";
    } else {
        die("Update failed: " . $stmt->error);
    }
}

// Check if there's a success message in the URL
if (isset($_GET['message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['message']) . '</div>';
}

// Check if there's an error message in the URL
if (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="stylesProfile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .back-to-home {
            position: absolute;
            font-size: 40px;
            top: 15px;
            right: 15px;
            color: #FFFFFF;
            padding: 10px 20px;
            font-weight: bold;
        }

        .back-to-home:focus,
        .back-to-home:hover {
            color: #F4CC6F;
            background-color: transparent;
            border: none;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php
    include 'db_connect.php'; // Include your database connection file

    // Fetch user data based on logged-in user ID
    $userId = $_SESSION['id']; // Replace this with session user ID or dynamic input
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    ?>

    <a href="index.php" class="back-to-home">X</a>
    <div class="container light-style flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-4">
            Account Settings
        </h4>
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#account-general">General</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#account-change-password">Change password</a>
                        <div class="logo">
                            <img src="images/Porsche Logo BnW.png" alt="logo">
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">
                        <!-- General Settings Tab -->
                        <div class="tab-pane fade active show" id="account-general">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                                <div class="card-body media align-items-center">
                                    <img src="<?php echo htmlspecialchars($user['images']); ?>" alt="Profile" class="d-block ui-w-80" id="profilePicture">
                                    <div class="media-body ml-4">
                                        <label class="btn btn-outline-primary">
                                            Upload new photo
                                            <input type="file" class="account-settings-fileinput" name="profileImage" id="fileInput" accept="image/*" hidden>
                                        </label> &nbsp;
                                        <button type="button" class="btn btn-default md-btn-flat" id="resetImageBtn">Reset</button>
                                    </div>
                                </div>
                                <hr class="border-light m-0">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control mb-1" name="username" id="username" placeholder="Username"
                                            value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control mb-1" name="names" id="names" placeholder="Full Name"
                                            value="<?php echo htmlspecialchars($user['names'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control mb-1" name="email" id="email" placeholder="Email"
                                            value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" class="form-control mb-1" name="numbers" id="numbers" placeholder="Phone Number"
                                            value="<?php echo htmlspecialchars($user['numbers'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Address</label>
                                        <input type="text" class="form-control mb-1" name="addresses" id="addresses" placeholder="Address"
                                            value="<?php echo htmlspecialchars($user['addresses'] ?? ''); ?>">
                                    </div>
                                    <input type="hidden" name="update_profile" value="1">
                                </div>
                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary">Save changes</button>&nbsp;
                                    <button type="reset" class="btn btn-default" id="resetFormBtn">Clear All</button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="account-change-password">
                            <form action="update_password.php" method="POST">
                                <div class="card-body pb-2">
                                    <div class="form-group">
                                        <label class="form-label">Current password</label>
                                        <input type="password" class="form-control" name="currentPassword" id="currentPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">New password</label>
                                        <input type="password" class="form-control" name="newPassword" id="newPassword" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Repeat new password</label>
                                        <input type="password" class="form-control" name="repeatNewPassword" id="repeatNewPassword" required>
                                        <div id="passwordMatchMessage" style="color: red; display: none;">Passwords do not match!</div>
                                    </div>
                                </div>
                                <div class="text-right mt-3">
                                    <button type="submit" class="btn btn-primary">Save changes</button>&nbsp;
                                    <button type="reset" class="btn btn-default" id="resetPasswordFormBtn">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#repeatNewPassword').on('input', function() {
                const newPassword = $('#newPassword').val();
                const repeatPassword = $(this).val();
                if (newPassword !== repeatPassword) {
                    $('#passwordMatchMessage').show();
                } else {
                    $('#passwordMatchMessage').hide();
                }
            });

            $('#resetImageBtn').click(function() {
                $('#profilePicture').attr('src', 'https://bootdey.com/img/Content/avatar/avatar1.png');
                $('#fileInput').val('');
            });
        });

        document.getElementById('fileInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Set the preview image to the selected file
                    document.getElementById('profilePicture').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Reset image
        document.getElementById('resetImageBtn').addEventListener('click', function() {
            document.getElementById('profilePicture').src = 'https://bootdey.com/img/Content/avatar/avatar1.png'; // default image
            document.getElementById('fileInput').value = ''; // Reset file input
        });
    </script>
</body>

</html>
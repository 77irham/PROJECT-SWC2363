<?php
session_start(); // Start the session if you have a session-based login system

// Include database connection
require_once 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    header('Location: loginAndRegister.php');
    exit();
}

// Get user ID from session (assuming it's stored in session after login)
$id = $_SESSION['id'];

// Initialize variables for the user data
$fullNames = '';
$email = '';
$phone = '';
$address = '';
$imagePath = '';

// Fetch current user data from the database
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If form is submitted, update the user data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $fullNames = $_POST['fullNames'] ?? $user['names'];
    $email = $_POST['email'] ?? $user['email'];
    $phone = $_POST['phone'] ?? $user['numbers'];
    $address = $_POST['address'] ?? $user['addresses'];

    // Handle image upload
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $imageName = $_FILES['profileImage']['name'];
        $imageTmpName = $_FILES['profileImage']['tmp_name'];
        $imageSize = $_FILES['profileImage']['size'];
        $imageType = $_FILES['profileImage']['type'];

        // Set a unique name for the image file
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $newImageName = uniqid('profile_', true) . '.' . $imageExtension;
        $imageDirectory = 'uploads/'; // Directory to store images

        // Move the uploaded file to the designated directory
        if (move_uploaded_file($imageTmpName, $imageDirectory . $newImageName)) {
            $imagePath = $imageDirectory . $newImageName; // Save image path for database
        } else {
            echo "Error uploading image.";
            exit();
        }
    } else {
        // If no new image, retain the current image path
        $imagePath = $user['images'];
    }

    // Prepare the SQL query to update the user's profile data
    $sql = "UPDATE users SET names = ?, email = ?, numbers = ?, addresses = ?, images = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssi', $fullNames, $email, $phone, $address, $imagePath, $id);

    if ($stmt->execute()) {
        // Redirect to a confirmation page or back to the profile page
        header('Location: profile.php?update=success');
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
</head>

<body>

    <h2>Update Your Profile</h2>
    <form action="update_user.php" method="POST" enctype="multipart/form-data">
        <label for="fullNames">Full Name:</label>
        <input type="text" name="fullNames" id="fullNames" value="<?php echo htmlspecialchars($user['names']); ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['numbers']); ?>" required><br><br>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user['addresses']); ?>"><br><br>

        <label for="profileImage">Upload Profile Image:</label>
        <input type="file" name="profileImage" id="profileImage" accept="image/*"><br><br>

        <img id="profilePicture" src="<?php echo htmlspecialchars($user['images']); ?>" alt="Profile Picture" width="150" height="150"><br><br>

        <input type="submit" value="Update Profile">
    </form>

</body>

</html>

<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: loginAndRegister.php");
    exit();
}

// Get logged-in user ID
$userId = $_SESSION['id'];

// Fetch user details
$query = "SELECT passwords FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    header("Location: profile.php?error=Query preparation failed.");
    exit();
}
$stmt->bind_param("i", $userId);
if (!$stmt->execute()) {
    header("Location: profile.php?error=Query execution failed.");
    exit();
}
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: profile.php?error=User not found.");
    exit();
}

// Handle password change form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required fields exist before accessing them
    if (!isset($_POST['currentPassword'], $_POST['newPassword'], $_POST['repeatNewPassword'])) {
        header("Location: profile.php?error=All fields are required.");
        exit();
    }

    // Get the current and new passwords from the form
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $repeatNewPassword = $_POST['repeatNewPassword'];

    // Validate form inputs
    if (empty($currentPassword) || empty($newPassword) || empty($repeatNewPassword)) {
        header("Location: profile.php?error=All fields are required.");
        exit();
    }

    if ($newPassword !== $repeatNewPassword) {
        header("Location: profile.php?error=New passwords do not match.");
        exit();
    }

    // Verify the current password
    if (!password_verify($currentPassword, $user['passwords'])) {
        header("Location: profile.php?error=Current password is incorrect.");
        exit();
    }

    // Hash the new password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $updateQuery = "UPDATE users SET passwords = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    if (!$stmt) {
        header("Location: profile.php?error=Query preparation failed.");
        exit();
    }
    $stmt->bind_param("si", $newPasswordHash, $userId);
    if ($stmt->execute()) {
        // Redirect to profile.php on success
        header("Location: profile.php?message=Password updated successfully.");
        exit();
    } else {
        header("Location: profile.php?error=Failed to update password.");
        exit();
    }

    $stmt->close();
}
?>

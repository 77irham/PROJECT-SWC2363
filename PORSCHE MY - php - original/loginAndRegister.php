<?php
// Include database connection
include 'db_connect.php';

// Initialize session
session_start();

// Handle registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt the password
    $role = 'User'; // Default role for new users

    // Insert new user into the database directly without checking for duplicate email
    // NOTE: The query doesn't check for duplicates; it will insert whatever values are provided
    $stmt = $conn->prepare("INSERT INTO users (username, email, passwords, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    // Error handling: try-catch block to catch any errors in insertion
    try {
        $stmt->execute();
        $stmt->close();

        // Set session message
        $_SESSION['message'] = 'Registration successful! Please log in to continue.';
        // Redirect to the same page to show the form
        header("Location: loginAndRegister.php");
        exit;
    } catch (mysqli_sql_exception $e) {
        // Handle the error here (e.g., email already exists or any other issue)
        $_SESSION['message'] = 'Error: ' . $e->getMessage(); // Display the error message
        header("Location: loginAndRegister.php");
        exit;
    }
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['passwords'])) {
            // Start session and redirect based on role
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'Administrator') {
                header("Location: adminDashboard.php");
            } else {
                header("Location: profile.php");
            }
            exit;
        }
    }
    $stmt->close();
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Porsche MY</title>

  <link rel="stylesheet" href="stylesLogin.css">

  <style>
    /* Success message style */
    .success-message {
      background-color: #4CAF50; /* Green background for success */
      color: white; /* White text */
      padding: 10px; /* Padding around the text */
      text-align: center; /* Center-align text */
      margin-bottom: 20px; /* Space below the message */
      border-radius: 5px; /* Rounded corners */
      font-size: 16px; /* Font size */
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo">
      <img src="images/Porsche Black and White Logo.png" alt="Porsche MY Logo">
    </div>
  </header>

  <div class="wrapper">
    <!-- Display success message if available -->
    <?php if (isset($_SESSION['message'])): ?>
      <div class="success-message" id="successMessage">
        <p><?php echo $_SESSION['message']; ?></p>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Login Form -->
    <div class="form-wrapper sign-in">
      <form method="POST" action="loginAndRegister.php">
        <h2>Login</h2>
        <div class="input-group">
          <input type="text" name="username" required>
          <label>Username</label>
        </div>
        <div class="input-group">
          <input type="password" name="password" required>
          <label>Password</label>
        </div>
        <input type="hidden" name="action" value="login">
        <button type="submit">Login</button>
        <div class="signUp-link">
          <p>Don't have an account? <a href="#" class="signUpBtn-link">Sign Up</a></p>
        </div>
      </form>
    </div>

    <!-- Registration Form -->
    <div class="form-wrapper sign-up">
      <form method="POST" action="loginAndRegister.php">
        <h2>Sign Up</h2>
        <div class="input-group">
          <input type="text" name="username" required>
          <label>Username</label>
        </div>
        <div class="input-group">
          <input type="email" name="email" required>
          <label>Email</label>
        </div>
        <div class="input-group">
          <input type="password" name="password" required>
          <label>Password</label>
        </div>
        <div class="remember">
          <label><input type="checkbox" id="agreeTerms" required> I agree to the <a href="#" id="termsLink">terms & conditions</a></label>
        </div>
        <input type="hidden" name="action" value="register">
        <button type="submit">Sign Up</button>
        <div class="signUp-link">
          <p>Already have an account? <a href="#" class="signInBtn-link">Sign In</a></p>
        </div>
      </form>
    </div>
  </div>

  <!-- Back Button (outside of form, positioned to the right) -->
  <button class="back-btn" onclick="window.location.href='index.php';">Back ></button>

  <script>
    // Form toggle behavior
    const signInBtnLink = document.querySelector('.signInBtn-link');
    const signUpBtnLink = document.querySelector('.signUpBtn-link');
    const wrapper = document.querySelector('.wrapper');

    signUpBtnLink.addEventListener('click', () => {
      wrapper.classList.add('active');
    });

    signInBtnLink.addEventListener('click', () => {
      wrapper.classList.remove('active');
    });

    // Check if the success message exists and hide it after 3 seconds
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
      setTimeout(() => {
        successMessage.style.display = 'none';
      }, 3000); // 3000 milliseconds = 3 seconds
    }
  </script>
</body>
</html>

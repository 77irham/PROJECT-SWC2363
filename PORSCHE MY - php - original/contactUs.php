<?php
// Include the database connection
include('db_connect.php');

// Initialize variables to store form data and any messages
$successMessage = "";
$errorMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $firstName = trim($_POST['fName']);
    $lastName = trim($_POST['lName']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate fields are not empty
    if ($firstName && $lastName && $email && $subject && $message) {
        // Prepare SQL to insert data into the contact_us table
        $stmt = $conn->prepare("INSERT INTO contact_us (first_name, last_name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $firstName, $lastName, $email, $subject, $message);

        // Execute and check for successful insertion
        if ($stmt->execute()) {
            $successMessage = "Form submitted successfully!";
        } else {
            $errorMessage = "Error submitting form. Please try again.";
        }

        // Close the statement
        $stmt->close();
    } else {
        $errorMessage = "Please fill out all fields before submitting.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche MY - Contact Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="stylesContactUs.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        /* Toast message styles */
        .toast {
            visibility: hidden;
            max-width: 50%;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
        }

        .toast.success {
            background-color: green;
        }

        .toast.error {
            background-color: red;
        }

        /* Show the toast message */
        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 3s;
        }

        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }

        /* Modal */
        .modal {
            display: none; /* Initially hide the modal */
            position: fixed; /* Fixed positioning */
            z-index: 10; /* Sit on top */
            left: 50%; /* Center horizontally */
            top: 50%; /* Center vertically */
            transform: translate(-50%, -50%); /* Offset by 50% of its own width and height */
            width: 60%; /* You can adjust the width as needed */
            max-height: 60%; /* Prevent the modal from being too tall */
            overflow-y: auto; /* Make content scrollable if it exceeds max height */
            background-color: #000; /* Light background with opacity */
            border: 3px solid #444; /* Add border to the modal itself */
            border-radius: 10px; /* Optional: rounds the corners of the modal */
        }

        /* Modal Content Box */
        .modal-content {
            background-color: #333; /* Dark background */
            color: #fff; /* White text for contrast */
            padding: 20px;
            border: 5px solid #888; /* Add border to content box */
            border-radius: 8px; /* Optional: rounds the corners of the content box */
            position: relative; /* To position the close button */
        }

        /* The Close Button */
        .close-btn {
            color: #fff; /* White text for the close button */
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: #ccc; /* Light gray on hover */
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <img src="images/Porsche Black and White Logo.png" class="logo" alt="Car Logo">
            <div class="menu" id="menu">
                <a href="index.php">Home</a>
                <a href="aboutUs.php">Porsche Lifestyle</a>
                <a href="models.php">Models</a>
                <a href="#">Contact Us</a>
            </div>

            <div class="social">
                <a href="https://www.facebook.com/porsche/" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="https://x.com/Porsche?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                <a href="https://www.instagram.com/porsche/" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </nav>
    </header>

    <form class="contact-container" id="contactForm" method="post" action="contactUs.php">
        <h1>Contact Us</h1>
        
        <div class="input-name">
            <input type="text" class="first-name" name="fName" placeholder="First Name" required>
            <input type="text" class="last-name" name="lName" placeholder="Last Name" required>
        </div>
        <div class="input-email">
            <input type="email" class="email" name="email" placeholder="Email" required>
        </div>
        <div class="input-subject">
            <input type="text" class="subject" name="subject" placeholder="Subject" required>
        </div>
        <div class="input-message">
            <input type="text" class="message" name="message" placeholder="Message" required>
        </div>
        <div class="action-btn">
            <button type="submit" class="btn-submit">Submit</button>
        </div>
                        
        <div class="or-text">Or contact us on social media</div>
        <div class="social-contact">
            <div class="social-bottom">
                <div class="handle">@Porsche MY</div>
                <div class="social-action">
                    <a href="https://www.facebook.com/porsche/" target="_blank" aria-label="Facebook">Facebook</a>
                    <span class="material-symbols-rounded">arrow_right_alt</span>
                </div>
            </div>
            <div class="social-bottom">
                <div class="handle">@porsche.my</div>
                <div class="social-action">
                    <a href="https://x.com/Porsche?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" target="_blank" aria-label="Twitter">Twitter</a>
                    <span class="material-symbols-rounded">arrow_right_alt</span>
                </div>
            </div>
            <div class="social-bottom">
                <div class="handle">@porsche.my</div>
                <div class="social-action">
                    <a href="https://www.instagram.com/porsche/" target="_blank" aria-label="Instagram">Instagram</a>
                    <span class="material-symbols-rounded">arrow_right_alt</span>
                </div>
            </div>
        </div>
    </form>

    <!-- Toast Message -->
    <div id="toast" class="toast"></div>

    <footer>
        <p>&copy; 2024 Porsche MY Inc. All rights reserved.</p>
        <div class="footer-links">
            <a href="javascript:void(0);" onclick="openModal('termsModal')">Terms of Service</a> | 
            <a href="javascript:void(0);" onclick="openModal('privacyPolicyModal')">Privacy Policy</a>
        </div>
    </footer>

    <!-- Modal for Terms of Service -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('termsModal')">&times;</span>
            <h2>Porsche MY-Terms of Service</h2>
            <div class="modal-body">
                <p>

Last updated: 1 November 2024<br><br>

Welcome to Porsche MY (referred to as "we," "us," or "our"). By using our website, services, and platforms, including the Porsche MY website, you agree to comply with and be bound by these Terms of Service (the "Agreement"). Please read these terms carefully before using our services.<br><br>

1. Acceptance of Terms<br>

By accessing or using Porsche MY services, you agree to the terms set forth in this Agreement. If you do not agree with these terms, you must not access or use our website or services.<br><br>

2. Services Provided<br>

Porsche MY provides an online platform for viewing, customizing, and purchasing Porsche vehicles, along with related services. Our services include the browsing of Porsche models, reviewing specifications, purchasing options, and other functionalities as offered through our website.<br><br>

3. Account Registration and Security<br>

To access certain features of our services, you may be required to create an account. You agree to provide accurate, current, and complete information during the registration process and to update your account information as necessary. You are responsible for maintaining the confidentiality of your account details, including your username and password.<br>

You agree to immediately notify us of any unauthorized use or suspected breach of your account security.<br><br>

4. User Obligations<br>

You agree to use our website and services in compliance with all applicable laws and regulations. You must not:<br><br>

Use our services for any illegal or unauthorized purpose.<br>
Interfere with or disrupt the operation of our website.<br>
Use automated systems or scripts to access or interact with our website in an unauthorized manner.<br><br>

5. Customization and Purchases<br>

Porsche MY allows you to customize Porsche vehicles and make purchases directly through our platform. All vehicle pricing, specifications, and features are subject to change and availability.<br>

By placing an order through our website, you are making an offer to purchase a vehicle or related service. The completion of your order does not constitute an acceptance by Porsche MY until the order has been confirmed and payment processed.<br><br>

6. Payment<br>

Payments for vehicles or services through Porsche MY are processed using secure payment systems. You agree to provide accurate payment information, and authorize us to charge your selected payment method for any purchases made.<br><br>

7. Intellectual Property<br>

All content, logos, trademarks, and other intellectual property related to Porsche MY are the property of Porsche MY or its licensors. You may not use, reproduce, or distribute any materials without our express written consent.<br><br>

8. Privacy Policy<br>

By using Porsche MY’s services, you agree to the collection and use of your information in accordance with our Privacy Policy. Please review our Privacy Policy for more details.<br><br>

9. Limitation of Liability<br>

To the fullest extent permitted by law, Porsche MY will not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profit, revenue, data, or use incurred by you or any third party in connection with your use of our website or services, even if Porsche MY has been advised of the possibility of such damages.<br><br>

10. Termination<br>

We reserve the right to suspend or terminate your access to Porsche MY’s services at any time and for any reason, including but not limited to violations of this Agreement or fraudulent activity.<br><br>

11. Changes to Terms of Service<br>

We may revise these Terms of Service at any time. Any changes will be posted on this page, and the revised terms will take effect immediately upon posting. Your continued use of our services following any changes constitutes acceptance of those changes.<br><br>

12. Governing Law<br>

This Agreement will be governed by and construed in accordance with the laws of [Jurisdiction], without regard to its conflict of law principles.<br><br>

13. Contact Information<br>

If you have any questions or concerns about these Terms of Service, please contact us at:<br><br>

Email: kl2307013904@student.uptm.edu.my<br>
Address: Porsche MY, Kuala Lumpur<br></p>
                </p>
            </div>
        </div>
    </div>

    <!-- Modal for Privacy Policy -->
    <div id="privacyPolicyModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal('privacyPolicyModal')">&times;</span>
            <h2>Porsche MY-Privacy Policy</h2>
            <div class="modal-body">
                <p>

Effective Date: 1 November 2024<br><br>

At Porsche MY, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and protect your personal information when you use our services, including our website and related platforms.<br>

By using our website and services, you agree to the collection and use of your personal data as described in this policy.<br><br>

1. Information We Collect<br>
We collect several types of information to provide and improve our services:<br><br>

Personal Identification Information: When you create an account or make a purchase, we may collect your name, email address, phone number, delivery address, and other personal details.<br>
Usage Data: We may collect information on how you access and use our website, such as your IP address, browser type, and browsing behavior.
Payment Information: When you make a purchase, your payment details (e.g., credit card information) are processed through a secure third-party payment provider. We do not store this sensitive information.<br>
Cookies and Tracking Technologies: We use cookies to track your activity on our website to enhance user experience and analyze trends.<br><br>
2. How We Use Your Information<br>
We use the information we collect in the following ways:<br><br>

To provide, maintain, and improve our services.<br>
To process transactions and send you related updates, such as order confirmations and shipping details.<br>
To communicate with you about new products, offers, and promotions that may interest you.<br>
To personalize your experience on our website.<br>
To analyze trends and improve the functionality and performance of our website.<br><br>
3. Data Security<br>
We take the security of your personal information seriously and implement reasonable security measures to protect it from unauthorized access, use, or disclosure. While we strive to use commercially acceptable means to protect your personal data, no method of transmission over the Internet or electronic storage is completely secure.<br><br>

4. Sharing of Information<br>
We may share your personal information in the following situations:<br><br>

With third-party service providers: We may share your information with trusted vendors who help us in processing payments, delivering orders, and performing marketing services. These third parties are obligated to protect your information and use it solely for the purposes for which it was provided.<br>
Legal Requirements: We may disclose your information to comply with legal obligations or to protect our rights, property, and safety, or the rights, property, and safety of others.<br><br>
5. Your Rights and Choices<br>
You have the following rights regarding your personal information:<br><br>

Access and Update: You can access and update your personal information by logging into your account.<br>
Delete Account: You may request to delete your account by contacting us.<br>
Opt-out of Communications: You can opt-out of receiving promotional emails by following the unsubscribe instructions provided in those emails or by contacting us directly.<br><br>

6. Third-Party Links<br>
Our website may contain links to third-party sites. We are not responsible for the privacy practices or content of these external sites. We encourage you to review the privacy policies of any third-party websites you visit.<br><br>

7. Changes to This Privacy Policy<br>
We may update this Privacy Policy from time to time. Any changes will be posted on this page, and we will notify you of significant changes by email or through a prominent notice on our website. Please review this policy periodically to stay informed about how we are protecting your information.<br><br>

8. Contact Us<br>
If you have any questions or concerns about this Privacy Policy or our data practices, please contact us at:<br><br>

Email: kl2307013904<br>
Address: Porsche MY, Kuala Lumpur<br></p>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Show toast message function
        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            if (type === 'success') {
                toast.classList.add('success');
            } else {
                toast.classList.add('error');
            }

            setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.remove('success');
                toast.classList.remove('error');
            }, 4000);
        }

        // Prevent toast from showing on social media link clicks
        document.querySelectorAll('nav .social a').forEach(link => {
            link.addEventListener('click', () => {
                window.preventToast = true;
            });
        });

        // Display toast only if preventToast is not set
        window.onload = function() {
            if (!window.preventToast) {
                <?php if ($successMessage): ?>
                    showToast('<?php echo $successMessage; ?>', 'success');
                <?php elseif ($errorMessage): ?>
                    showToast('<?php echo $errorMessage; ?>', 'error');
                <?php endif; ?>
            }
        };

        // Open the modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        // Close the modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        // Open overlay menu function
        function openOverlayMenu() {
            document.getElementById("overlayMenu").classList.add("active");
        }

        // Close overlay menu function
        function closeOverlayMenu() {
            document.getElementById("overlayMenu").classList.remove("active");
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            var termsModal = document.getElementById("termsModal");
            var privacyPolicyModal = document.getElementById("privacyPolicyModal");
            if (event.target === termsModal) {
                termsModal.style.display = "none";
            } else if (event.target === privacyPolicyModal) {
                privacyPolicyModal.style.display = "none";
            }
        }
    </script>
</body>
</html>

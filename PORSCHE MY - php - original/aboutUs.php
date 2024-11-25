<?php
// Include the database connection
include('db_connect.php');

// Fetch team data from the database
$query = "SELECT name, role, image_path FROM team WHERE department = 'Leadership'";
$result = $conn->query($query);

// Store team data in an array
$teamMembers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teamMembers[] = $row;
    }
} else {
    echo "No team members found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche MY</title>

    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Link File CSS -->
    <link rel="stylesheet" href="stylesAboutUs.css">

    <style>
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
                <a href="#">Porsche Lifestyle</a>
                <a href="models.php">Models</a>
                <a href="contactUs.php">Contact Us</a>
            </div>

            <div class="social">
                <a href="https://www.facebook.com/porsche/" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="https://x.com/Porsche?ref_src=twsrc%5Egoogle%7Ctwcamp%5Eserp%7Ctwgr%5Eauthor" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                <a href="https://www.instagram.com/porsche/" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
            </div>
        </nav>
    </header>
    
    <div class="container">
        <img class="cover-photo" src="images/Porsche Lifestyle 1.jpg" alt="coverPhoto">
        <div class="about-container">
            <div class="our-story">
                <h1>Our Story</h1>
                <div>
                &nbsp &nbsp It sits unremarkably in a gray safe in a climate-controlled room: the first ledger of the Porsche design office, stored in a fireproof room in the archive of the Porsche Museum. In the timeworn ledger one can find order number 1, placed on August 21, 1930. The job involved manufacturing individual components for a “Hesselmann engine,” a cross between a diesel and a gas engine—a sign of the company’s innovative spirit since its inception. Order number 7 was of another dimension altogether. “Small-car project,” reads the description in the ledger. The Wanderer company planned to motorize the masses and needed a concept with which it could economically and inexpensively develop what was then considered a luxury item into a Volks-Wagen—a car for the people. A clever idea, as history would demonstrate. The order book provides an illuminating look at how Ferdinand Porsche and his small team of just nineteen employees embodied the vision of design creativity.<br><br>

                &nbsp &nbsp On April 25, 1931, Professor Porsche had his company officially entered in the commercial register. From that day forward, “Dr. Ing. h.c. F. Porsche GmbH, Konstruktionen und Beratung für Motoren und Fahrzeuge,” based in Stuttgart, was officially on the books. The first five projects were started in 1930 in St. Ulrich, Austria. The drawing board was in the bedroom of Porsche’s son Ferry. But the office moved to Stuttgart at the beginning of 1931, initially renting space in the city center. The idea of a neutral design office was still unheard of in the automotive world. Ferdinand Porsche did not, at the time, harbor the intention of building his own cars. His aim was to carry out technical projects for a variety of clients as well as charge licensing fees and patent royalties. The first order book illustrates in impressive fashion how the Porsche office became a hotbed of innovation for the German automotive industry.
                </div>
            </div>

            <div class="our-team">
                <h1>Meet Our Leaders</h1>
                <div class="teams-image">
                    <?php foreach ($teamMembers as $member) { ?>
                        <div class="card">
                            <img class="team-img" src="<?php echo htmlspecialchars($member['image_path']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                            <div class="box-content">
                                <h3 class="title"><?php echo htmlspecialchars($member['name']); ?></h3>
                                <span class="post"><?php echo htmlspecialchars($member['role']); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="our-values">
                <h1>Our Values</h1>
                <div>
                Porsche is the most successful manufacturer of exclusive sports cars and has an outstanding reputation. This success is based on the desirability of the vehicles, but equally importantly, due to its unique corporate culture that is shaped by integrity and compliance. And it should stay that way. All board members, members of the management bodies, managers and employees make their own contributions every day according to the motto:
                Sporty and fair - that's typical Porsche.
                </div>
            </div>
        </div>
    </div>
 
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

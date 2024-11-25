<?php
// Include the database connection
include('db_connect.php');

session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Porsche MY - Explore the latest Porsche models, features, and lifestyle.">
    <meta property="og:image" content="images/logo.png">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Porsche MY - Official Website">
    <title>Porsche MY</title>

    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Link Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    
    <!-- Link File CSS -->
    <link rel="stylesheet" href="styles.css">

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

        svg {
            cursor: pointer;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            pointer-events: none; /* Allows interaction with elements behind the SVG */
            min-height: 100vh;
            display: grid;
            justify-content: center;
            align-content: center;
            animation: go 2s linear 0s infinite; /* Animation will still run for 2 seconds infinitely */
        }

        
        @keyframes go{
            0%{
                background-color: rgb(250, 200, 152);
            }
            25%{
                background-color: #800000;
            }
            50%{
                background-color: white;
            }
            75%{
                background-color: #ccc;
            }
            100%{
                background-color: #000;
            }
        }

        body>svg{
            width: 100vw;     /* Set width to 100% of the viewport width */
            height: 100vh; 
            --stroke-dash: 46;
            --stroke-dash-negative: -46;       
        }

        g#car{
            transform: translateX(-3px);
            animation: translate 2s ease-in-out infinite;
        }

        path#shadow{
            animation: skew 2s ease-in-out infinite;
        }

        g.wheels use{
            animation: rotate 2s linear infinite;
        }

        path.air{
            stroke-dasharray: var(--stroke-dash);
            stroke-dashoffset: var(--stroke-dash);
            animation: offset 2s linear infinite;
            opacity: 0;
        }

        @keyframes translate{
            1%{
                transform: translateX(0px);
            }
            20%{
                transform: translateX(-10px);
            }
            40%{
                transform: translateX(-20px);
            }
            60%{
                transform: translateX(-30px);
            }
            100%{
                transform: translateX(1400px);
            }
        }

        @keyframes skew{
            50%{
                transform: skewX(-20deg);
            }
        }

        @keyframes rotate{
            0%{
                transform: rotate(-130deg);
            }
            50%{
                transform: rotate(-290deg);
            }
            100%{
                transform: rotate(6turn);
            }
        }

        @keyframes offset{
            1%{
                opacity: 1;
            }
            15%{
                stroke-dashoffset: 0;
                opacity:1;
            }
            24%{
                opacity: 1;
            }
            25%{
                opacity: 0;
                stroke-dashoffset: var(--stroke-dash-negative);
            }
            26%{
                opacity: 1;
                stroke-dashoffset: var(--stroke-dash-negative);
            }
            27%{
                opacity: 0;
                stroke-dashoffset: var(--stroke-dash-negative);
            }
            28%{
                opacity: 1;
                stroke-dashoffset: var(--stroke-dash-negative);
            }
            100%{
                stroke-dashoffset: var(--stroke-dash-negative);
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <img src="images/Porsche Black and White Logo.png" class="logo" alt="Porsche Logo">

            <div class="menu" id="menu">
                <a href="#">Home</a>
                <a href="aboutUs.php">Porsche Lifestyle</a>
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
        
    <svg id="car-animation" viewBox="0 0 178 40" width="178" height="40">
    <!-- dash included behind the car
    ! be sure to delay the animation of the path after the dashes on the right side of the car
    -->
  <path class="air" d="M 46 16.5 h -20 a 8 8 0 0 1 0 -16" fill="none" stroke="#E85725" stroke-width="1" stroke-linejoin="round" stroke-linecap="round">
  </path>

  <!-- wrap the svg describing the car in a group
    this to translate the car horizontally within the wrapping svg
    -->
  <g id="car">
    <!-- svg describing the race car in a container 118 wide and 28.125 tall
        .125 due to the 2.25 width of the stroke

        position in the bottom center of the wrapping svg
        -->
    <svg viewBox="0 0 118 28.125" x="30" y="11.725" width="118" height="28.125">
      <defs>
        <!-- circle repeated for the wheel -->
        <circle id="circle" cx="0" cy="0" r="1">
        </circle>
        <!-- wheel
                three overlapping circles describing the parts of the wheel
                in between the circles add path elements to detail the graphic
                -->
        <g id="wheel">
          <use href="#circle" fill="#1E191A" transform="scale(10)"></use>
          <use href="#circle" fill="#fff" transform="scale(5)"></use>
          <!-- inner shadow -->
          <path fill="#1E191A" stroke="#1E191A" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.2" stroke-dashoffset="0" d="M -3.5 0 a 4 4 0 0 1 7 0 a 3.5 3.5 0 0 0 -7 0">
          </path>
          <use href="#circle" fill="#1E191A" transform="scale(1.5)"></use>
          <!-- yellow stripe
                    include stroke-dasharray values totalling the circumference of the circle
                    this to use the dash-offset property and have the stripe rotate around the center while keeping its shape
                    ! explicitly set the stroke-dashoffset property to 0 for the other path elements (in this way the stroke-dashoffset attribute added through the <use> element affects only this path)
                    -->
          <path fill="none" stroke="#F9B35C" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="20 14 8 5" d="M 0 -7.5 a 7.5 7.5 0 0 1 0 15 a 7.5 7.5 0 0 1 0 -15">
          </path>
          <!-- outer glow (from a hypothetical light source) -->
          <path fill="none" stroke="#fff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" opacity="0.1" stroke-dashoffset="0" d="M -6.5 -6.25 a 10 10 0 0 1 13 0 a 9 9 0 0 0 -13 0">
          </path>
        </g>
      </defs>
      <!-- group describing the pilot's helmet
            translate in the middle of the cockpit
            -->
      <g transform="translate(51.5 11.125)">
        <path stroke-width="2" stroke="#1E191A" fill="#EF3F33" d="M 0 0 v -2 a 4.5 4.5 0 0 1 9 0 v 2">
        </path>
        <rect fill="#1E191A" x="3.25" y="-3" width="5" height="3">
        </rect>
      </g>

      <!-- group describing the car -->
      <g transform="translate(10 24.125)">
        <!-- shadow below the car
                ! change the transform-origin of the shadow to animate it from the top center
                the idea is to skew the shadow as the car moves
                -->
        <g transform="translate(59 0)">
          <path id="shadow" opacity="0.7" fill="#1E191A" d="M -64 0 l -4 4 h 9 l 8 -1.5 h 100 l -3.5 -2.5">
          </path>
        </g>
        <!-- path describing the frame of the car
                ! do not add a stroke at the bottom of the frame
                additional lines are overlapped to detail the belly of the vehicle
                -->
        <path fill="#fff" stroke="#1E191A" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" d="M 0 0 v -10 l 35 -13 v 5 l 4 0.5 l 0.5 4.5 h 35.5 l 30 13">
        </path>

        <!-- wings -->
        <g fill="#fff" stroke="#1E191A" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
          <path d="M -6 0 v -22 h 10 z">
          </path>
          <path d="M 105 0 h -3 l -12 -5.2 v 6.2 h 12">
          </path>
        </g>

        <!-- grey areas to create details around the car's dashes -->
        <g fill="#949699" opacity="0.7">
          <rect x="16" y="-6" width="55" height="6">
          </rect>
          <path d="M 24 -14 l 13 -1.85 v 1.85">
          </path>
        </g>

        <!-- dashes included sparingly on top of the frame -->
        <g fill="none" stroke="#1E191A" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
          <path stroke-dasharray="30 7 42" d="M 90 0 h -78">
          </path>
          <path d="M 39.5 -13 h -15">
          </path>
        </g>

        <!-- elements describing the side of the car -->
        <path fill="#fff" stroke="#1E191A" stroke-width="2.25" stroke-linejoin="round" d="M 48.125 -6 h -29 v 6 h 29">
          <!-- .125 to tuck the path behind the rectangle and avoid a pixel disconnect as the svg is scaled -->
        </path>

        <rect x="48" y="-7.125" width="6.125" height="7.125" fill="#1E191A">
        </rect>

        <!-- rear view mirror -->
        <g fill="#1E191A">
          <rect x="60" y="-15" width="1" height="6">
          </rect>
          <rect x="56.5" y="-17.5" width="6" height="2.5">
          </rect>
        </g>
      </g>

      <!-- group describing the wheels, positioned at the bottom of the graphic and at either end of the frame -->
      <g class="wheels" transform="translate(0 18.125)">
        <g transform="translate(10 0)">
          <use href="#wheel"></use>
        </g>

        <g transform="translate(87 0)">
          <!-- add an offset to rotate the yellow stripe around the center -->
          <use href="#wheel" stroke-dashoffset="-22"></use>
        </g>
      </g>
    </svg>
  </g>

  <!-- dashes included above and around the race car
    ! include them in order from right to left
    this allows to rapidly assign an increasing delay in the script, to have the dashes animated in sequence
    -->
  <g fill="none" stroke-width="1" stroke-linejoin="round" stroke-linecap="round">
    <!-- right side -->
    <path class="air" stroke="#E85725" d="M 177.5 34 h -10 q -16 0 -32 -8">
    </path>

    <path class="air" stroke="#949699" d="M 167 28.5 c -18 -2 -22 -8 -37 -10.75">
    </path>

    <path class="air" stroke="#949699" d="M 153 20 q -4 -1.7 -8 -3">
    </path>

    <path class="air" stroke="#E85725" d="M 117 16.85 c -12 0 -12 16 -24 16 h -8">
      <!-- around (117 29.85) where the right wheel is centered -->
    </path>

    <!-- left side -->
    <path class="air" stroke="#949699" d="M 65 12 q -5 3 -12 3.8">
    </path>

    <path class="air" stroke="#949699" stroke-dasharray="9 10" d="M 30 13.5 h -2.5 q -5 0 -5 -5">
    </path>

    <path class="air" stroke="#949699" d="M 31 33 h -10">
    </path>

    <path class="air" stroke="#949699" d="M 29.5 23 h -12">
    </path>
    <path class="air" stroke="#949699" d="M 13.5 23 h -6">
    </path>

    <path class="air" stroke="#E85725" d="M 28 28 h -27.5">
    </path>
  </g>
</svg>

    <!-- Overlay menu -->
    <div id="overlayMenu" class="overlay">
        <a href="javascript:void(0)" class="close-btn" onclick="closeOverlayMenu()">&times;</a>
        <div class="overlay-content">
            <a href="index.php">Home</a>
            <a href="aboutUs.php">Porsche Lifestyle</a>
            <a href="models.php">Models</a>
            <a href="contactUs.php">Contact Us</a>
            <a href="loginAndRegister.php">My Profile</a>
        </div>
    </div>

    <main>
        <div class="hero">
            <div class="text">
                <h4>Powerful, Agile and </h4>
                <h1>Fierce to <br> <span>Drive</span></h1>
                <p>If one does not fail at times, then one has not challenged himself.</p>
                <a href="javascript:void(0)" class="btn" onclick="openOverlayMenu()">Discover More</a>
            </div>
        </div>

        <section class="features">
            <h2>Features</h2>
            <div class="horizontal-scroll">
                <div class="feature-card">
                    <i class="fa-solid fa-car"></i>
                    <h3>Performance</h3>
                    <p>Experience unrivaled speed and agility.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-cogs"></i>
                    <h3>Technology</h3>
                    <p>Advanced tech for your comfort and safety.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-paintbrush"></i>
                    <h3>Design</h3>
                    <p>Sleek designs that turn heads.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-tachometer-alt"></i>
                    <h3>Efficiency</h3>
                    <p>Designed for optimal fuel efficiency.</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-user-shield"></i>
                    <h3>Safety</h3>
                    <p>Top-notch safety features to protect you.</p>
                </div>
            </div>
        </section>

        <section class="testimonials">
            <h2>Latest Customer Feedback</h2>
            <div class="testimonials-scroll">
                <div class="testimonial-card">
                    <p>"Sportcar yang paling selesa abad ni!" - Irham Firdaus.</p>
                </div>
                <div class="testimonial-card">
                    <p>"Comfortable, reliable and affordable enough for daily duties." - Amran Harun.</p>
                </div>
                <div class="testimonial-card">
                    <p>"A thrilling driving experience with unmatched comfort." - Sarah.</p>
                </div>
                <div class="testimonial-card">
                    <p>"Pengalaman drive paling best untuk keluarga!" - Irfan Lizan.</p>
                </div>
            </div>
        </section>
    </main>

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
        // Open overlay menu function
        function openOverlayMenu() {
            document.getElementById("overlayMenu").classList.add("active"); // Add the active class to show overlay
        }

        // Close overlay menu function
        function closeOverlayMenu() {
            document.getElementById("overlayMenu").classList.remove("active"); // Remove the active class to hide overlay
        }

        // Background image rotation for the hero section
        let heroBg = document.querySelector('.hero');
        let images = ["url(images/bg.jpg)", "url(images/bg-light.jpg)"];
        let currentIndex = 0;

        setInterval(() => {
            currentIndex = (currentIndex + 1) % images.length;
            heroBg.style.backgroundImage = images[currentIndex];
        }, 2000);

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

// Disable scrolling
document.body.style.overflow = 'hidden';

// Set a timeout to hide the SVG after 4 seconds and restore scrolling
setTimeout(function() {
  document.getElementById('car-animation').style.display = 'none'; // Hide the SVG
  document.body.style.overflow = 'auto'; // Restore scrolling
}, 3900); // 4000 milliseconds = 4 seconds

</script>

  
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
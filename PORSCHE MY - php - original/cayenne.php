<?php
// Include database connection
include('db_connect.php');

// Fetch data from the database for the 'Cayenne' category
$query = "SELECT model_name AS model, power, acceleration, top_speed, price, image_path FROM porsche_models WHERE category = 'Cayenne'";
$result = $conn->query($query);

// Store the model data in an array for JavaScript use
$models = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $models[] = $row;
    }
} else {
    echo "No models found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Porsche MY</title>
    <link rel="stylesheet" href="stylemodels.css">
</head>
<body>
    <div class="header" id="header">
        <nav>
            <img src="images/Porsche Logo.png" class="logo" alt="Porsche Logo">
            <ul id="modelMenu">
                <!-- Dynamically generate menu items -->
                <?php foreach ($models as $model): ?>
                    <li><a href="#" id="<?php echo str_replace(' ', '', $model['model']); ?>">
                        <?php echo $model['model']; ?>
                    </a></li>
                <?php endforeach; ?>
            </ul>
            <a href="models.php" class="btn">Back ></a>
        </nav>

        <div class="info">
            <div>
                <h2 id="power">-- --</h2>
                <p>Power [kW]/Power [PS]</p>
            </div>
            <div>
                <h2 id="acceleration">-- --</h2>
                <p>Acceleration 0-100km/h</p>
            </div>
            <div>
                <h2 id="topSpeed">-- --</h2>
                <p>Top Speed</p>
            </div>
            <div class="line"></div>
            <div>
                <h2 id="model">Choose Model</h2>
            </div>

            <!-- Hidden form for purchasing the model -->
            <form id="modelForm" action="credit_card_payment.php" method="POST">
                <input type="hidden" id="selectedModel" name="selectedModel" value="">
                <input type="hidden" id="selectedPower" name="selectedPower" value="">
                <input type="hidden" id="selectedAcceleration" name="selectedAcceleration" value="">
                <input type="hidden" id="selectedTopSpeed" name="selectedTopSpeed" value="">
                <input type="hidden" id="selectedPrice" name="selectedPrice" value="">
                <input type="hidden" id="selectedImage" name="selectedImage" value="">

                <button type="submit" class="btn" id="buyNowBtn" disabled>Buy Now ></button>
            </form>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const models = <?php echo json_encode($models); ?>;

        // DOM elements
        const header = document.getElementById("header");
        const model = document.getElementById("model");
        const power = document.getElementById("power");
        const acceleration = document.getElementById("acceleration");
        const topSpeed = document.getElementById("topSpeed");
        const selectedModelInput = document.getElementById("selectedModel");
        const selectedPowerInput = document.getElementById("selectedPower");
        const selectedAccelerationInput = document.getElementById("selectedAcceleration");
        const selectedTopSpeedInput = document.getElementById("selectedTopSpeed");
        const selectedPriceInput = document.getElementById("selectedPrice");
        const selectedImageInput = document.getElementById("selectedImage");
        const buyNowBtn = document.getElementById("buyNowBtn");

        // Add click listeners for each model
        models.forEach(modelData => {
            const buttonId = modelData.model.replace(/\s+/g, '');
            const button = document.getElementById(buttonId);

            if (button) {
                button.onclick = function() {
                    // Update header background and model details
                    header.style.backgroundImage = `url('${modelData.image_path}')`;
                    model.innerHTML = modelData.model;
                    power.innerHTML = modelData.power;
                    acceleration.innerHTML = modelData.acceleration;
                    topSpeed.innerHTML = modelData.top_speed;

                    // Enable the Buy Now button and set form field values
                    buyNowBtn.disabled = false;
                    selectedModelInput.value = modelData.model;
                    selectedPowerInput.value = modelData.power;
                    selectedAccelerationInput.value = modelData.acceleration;
                    selectedTopSpeedInput.value = modelData.top_speed;
                    selectedPriceInput.value = modelData.price;
                    selectedImageInput.value = modelData.image_path;
                };
            }
        });
    </script>
</body>
</html>

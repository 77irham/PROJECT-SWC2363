<?php
// Include the database connection file (Make sure to update with your actual connection file)
include('db_connect.php');

// Fetch data for Panamera models from the database
$query = "SELECT model_name, power, acceleration, top_speed, price, image_path FROM porsche_models WHERE category = 'Panamera'";
$result = $conn->query($query);

// Store the Panamera model data in an array for easy access in JavaScript
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
    <title>Porsche MY - Panamera Models</title>
    <link rel="stylesheet" href="stylemodels.css">
</head>
<body>
    <div class="header" id="header">
        <nav>
            <img src="images/Porsche Logo.png" class="logo" alt="Porsche Logo">
            <ul id="modelMenu">
                <!-- Dynamically generate model buttons -->
                <?php foreach ($models as $model): ?>
                    <li><a href="#" id="<?php echo str_replace(' ', '', $model['model_name']); ?>">
                        <?php echo $model['model_name']; ?>
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

        // DOM elements for the models
        var header = document.getElementById("header");
        var model = document.getElementById("model");
        var power = document.getElementById("power");
        var acceleration = document.getElementById("acceleration");
        var topSpeed = document.getElementById("topSpeed");
        const selectedModelInput = document.getElementById("selectedModel");
        const selectedPowerInput = document.getElementById("selectedPower");
        const selectedAccelerationInput = document.getElementById("selectedAcceleration");
        const selectedTopSpeedInput = document.getElementById("selectedTopSpeed");
        const selectedPriceInput = document.getElementById("selectedPrice");
        const selectedImageInput = document.getElementById("selectedImage");
        const buyNowBtn = document.getElementById("buyNowBtn");

        // Add click listeners for each dynamically generated model button
        models.forEach(modelData => {
            const buttonId = modelData.model_name.replace(/\s+/g, '');
            const button = document.getElementById(buttonId);

            if (button) {
                button.onclick = function() {
                    // Update header background and model details
                    header.style.backgroundImage = `url('${modelData.image_path}')`;
                    model.innerHTML = modelData.model_name;
                    power.innerHTML = modelData.power;
                    acceleration.innerHTML = modelData.acceleration;
                    topSpeed.innerHTML = modelData.top_speed;

                    // Enable the Buy Now button and set form field values
                    buyNowBtn.disabled = false;
                    selectedModelInput.value = modelData.model_name;
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

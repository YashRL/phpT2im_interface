<?php

include_once 'connection.php'

// Array of demo prompts
$demoPrompts = [
    "A beautiful sunset over the mountains",
    "A cozy cabin in the snowy woods",
    "A futuristic city skyline",
    "A serene beach with palm trees",
    "A mystical forest with fireflies",
];

// Function to select a random demo prompt
function getRandomDemoPrompt($prompts) {
    $randomIndex = array_rand($prompts);
    return $prompts[$randomIndex];
}

// Function to store the image in the database
function storeImage($username, $imageDescription, $imageData, $db) {
    $stmt = $db->prepare("INSERT INTO generated_images (username, prompt, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $imageDescription, $imageData);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// PHP code to create a simple web application

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["generate"])) {
        $imageDescription = $_POST["image_description"];
        
        // Call the Hugging Face API to generate an image
        $apiUrl = "https://api*******";
        $apiHeaders = array(
            "Authorization: **************",
        );
        $requestData = array(
            "inputs" => $imageDescription,
        );
        
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $apiHeaders);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requestData));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        if ($httpCode == 200) {
            // Output the generated image as a base64 encoded string
            $processedImage = base64_encode($response);
            echo json_encode(array("image" => $processedImage));
            // Store the image in the database
            if (isset($_SESSION["username"])) {
                $username = $_SESSION["username"];
                storeImage($username, $imageDescription, $processedImage, $db);
            }
            exit;
        }
        // Handle errors here
        curl_close($curl);
    } elseif (isset($_POST["upload"])) {
        // Handle image upload here
        // You can add code to handle image upload and store it in the database
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Text to Image Generator</title>
</head>
<body>
    <h1>Text to Image Generator</h1>
    
    <?php
    session_start();
    // Display the username
    if (isset($_SESSION["username"])) {
        $username = $_SESSION["username"];
        echo "<p>Hi, $username...</p>";
    }
    ?>

    <form method="post" action="" id="imageForm">
        <label for="image_description">Enter the image description:</label>
        <input type="text" name="image_description" id="image_description">
        <input type="button" name="generate" value="Generate Image" onclick="generateImage()">
        <input type="button" name="surprise" value="Surprise Me" onclick="fillRandomPrompt()">
        <input type="button" name="upload" value="Upload" onclick="uploadImage()">
    </form>

    <!-- Display the generated image here -->
    <div id="imageContainer"></div>

    <script>
    function generateImage() {
    var imageDescription = document.getElementById("image_description").value;
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            var response = JSON.parse(xhr.responseText);
            var imageContainer = document.getElementById("imageContainer");
            var image = new Image();
            image.src = "data:image/jpeg;base64," + response.image;
            imageContainer.innerHTML = "";
            imageContainer.appendChild(image);
            
        }
    };
    
    xhr.send("generate=1&image_description=" + encodeURIComponent(imageDescription));
}


    function fillRandomPrompt() {
        var prompts = <?php echo json_encode($demoPrompts); ?>;
        var randomPrompt = prompts[Math.floor(Math.random() * prompts.length)];
        document.getElementById("image_description").value = randomPrompt;
    }

    function uploadImage($imageData, $imageDescription) {
    Database credentials
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "image";

    Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
    }

    // Convert base64-encoded image data to binary
    $imageData = base64_decode($imageData);

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO generated_images (description, image) VALUES (?, ?)");
    $stmt->bind_param("sb", $imageDescription, $imageData);

    // Execute SQL statement
    $stmt->execute();

    // Close connection
    $stmt->close();
    $conn->close();
}


</script>

</body>
</html>

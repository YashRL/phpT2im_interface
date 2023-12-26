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

        // Try connecting to FastAPI
        $fastApiUrl = "http://127.0.0.1:8000/generate_images/"; // Change to your FastAPI endpoint
        $fastApiData = json_encode(array(
            "input_prompt" => $imageDescription,
            "batch_size" => 3,
            "guidance_scale" => 10.0
        ));

        $fastApiCurl = curl_init($fastApiUrl);
        curl_setopt($fastApiCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($fastApiCurl, CURLOPT_POST, true);
        curl_setopt($fastApiCurl, CURLOPT_POSTFIELDS, $fastApiData);

        $fastApiResponse = curl_exec($fastApiCurl);
        $fastApiHttpCode = curl_getinfo($fastApiCurl, CURLINFO_HTTP_CODE);

        curl_close($fastApiCurl);

        if ($fastApiHttpCode == 200) {
            // Output the generated image from FastAPI
            echo $fastApiResponse;
            exit;
        } else {
            // If FastAPI fails, fallback to Hugging Face API
            $huggingFaceApiUrl = "https://api*******";
            $huggingFaceApiHeaders = array(
                "Authorization: **************",
            );
            $huggingFaceData = array(
                "inputs" => $imageDescription,
            );

            $huggingFaceCurl = curl_init($huggingFaceApiUrl);
            curl_setopt($huggingFaceCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($huggingFaceCurl, CURLOPT_HTTPHEADER, $huggingFaceApiHeaders);
            curl_setopt($huggingFaceCurl, CURLOPT_POST, true);
            curl_setopt($huggingFaceCurl, CURLOPT_POSTFIELDS, json_encode($huggingFaceData));

            $huggingFaceResponse = curl_exec($huggingFaceCurl);
            $huggingFaceHttpCode = curl_getinfo($huggingFaceCurl, CURLINFO_HTTP_CODE);

            curl_close($huggingFaceCurl);

            if ($huggingFaceHttpCode == 200) {
                // Output the generated image from Hugging Face API
                echo json_encode(array("image" => base64_encode($huggingFaceResponse)));
                // Store the image in the database
                if (isset($_SESSION["username"])) {
                    $username = $_SESSION["username"];
                    storeImage($username, $imageDescription, base64_encode($huggingFaceResponse), $db);
                }
                exit;
            } else {
                // Handle errors from Hugging Face API here
                http_response_code($huggingFaceHttpCode);
                echo "Error from Hugging Face API";
                exit;
            }
        }
    } elseif (isset($_POST["upload"])) {
        // Handle image upload here
        // You can add code to handle image upload and store it in the database
    }
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

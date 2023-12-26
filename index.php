<?php
// Database connection
//$db = new mysqli('localhost', 'root', '', 't2i_data');
include_once 'connection.php'

// Check if the form is submitted for registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"]; // Store the password in plaintext

    // Insert user data into the database
    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    $result = $db->query($query);

    if ($result) {
        // Registration successful
        echo "Registration successful!";
        // You can add a link to the login page here
    } else {
        // Registration failed
        echo "Registration failed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
</head>
<body>
    <h1>Registration</h1>
    <form method="post" action="index.php"> <!-- Update the action attribute -->
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <input type="submit" name="register" value="Register">
</form>

<p>Already registered? <a href="login.php">Login here</a></p>
</body>
</html>

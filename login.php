<?php
// Start a session to manage user sessions
session_start();

// Database connection
//$db = new mysqli('localhost', 'root', '', 't2i_data');
include_once 'connection.php'

// Check if the form is submitted for login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Query to retrieve the user's password from the database
    $query = "SELECT username, password FROM users WHERE username = '$username'";
    $result = $db->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row["password"];

        // Verify the entered password against the stored password
        if ($password == $storedPassword) {
            // Password is correct, login successful
            $_SESSION["username"] = $row["username"];
            header("Location: genrator.php");
            exit;
        }
    }

    // Login failed, show an error message
    echo "Invalid username and password.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <input type="submit" name="login" value="Login">
    </form>
</body>
</html>

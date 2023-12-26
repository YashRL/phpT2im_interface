$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 't2i_data';

// Create a database connection
$db = new mysqli($hostname, $username, $password, $database);

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

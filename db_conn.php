<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "recipe";

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to UTF-8
$conn->set_charset("utf8");

?>

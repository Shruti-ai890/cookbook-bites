<?php
session_start();
// Include your database connection
include 'db_conn.php';

// Assuming you have already validated and sanitized user inputs
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Insert user into the database
$sql = "INSERT INTO customer (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Handle SQL prepare statement error
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param('sss', $name, $email, $password);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Registration successful
    $_SESSION['user_id'] = $conn->insert_id; // Set session with user ID
    header("Location: user_dashboard.php");
    exit();
} else {
    // Registration failed
    header("Location: index.php?registration=failed");
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

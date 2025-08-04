<?php
// update-chef.php

require 'db_conn.php'; // Include your database connection

// Get POST data
$chef_id = isset($_POST['chef_id']) ? intval($_POST['chef_id']) : 0;
$chef_name = isset($_POST['chef_name']) ? trim($_POST['chef_name']) : '';

if ($chef_id > 0 && !empty($chef_name)) {
    // Update chef details in the database using MySQLi
    $stmt = $conn->prepare("UPDATE chefs SET chef_name = ? WHERE chef_id = ?");
    $stmt->bind_param("si", $chef_name, $chef_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or chef list
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Update failed.");
    }
    $stmt->close();
} else {
    die("Invalid input.");
}

$conn->close();

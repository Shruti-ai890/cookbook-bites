<?php
// update-cuisine.php

require 'db_conn.php'; // Include your database connection

// Get POST data
$cuisine_id = isset($_POST['cuisine_id']) ? intval($_POST['cuisine_id']) : 0;
$cuisine_name = isset($_POST['cuisine_name']) ? trim($_POST['cuisine_name']) : '';

if ($cuisine_id > 0 && !empty($cuisine_name)) {
    // Update cuisine details in the database using MySQLi
    $stmt = $conn->prepare("UPDATE cuisines SET cuisine_name = ? WHERE cuisine_id = ?");
    $stmt->bind_param("si", $cuisine_name, $cuisine_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or cuisine list
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

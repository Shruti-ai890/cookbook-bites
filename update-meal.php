<?php
// update-meal.php

require 'db_conn.php'; // Ensure this path is correct and points to your db_conn.php

// Get POST data
$meal_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$meal_name = isset($_POST['meal_name']) ? trim($_POST['meal_name']) : '';

if ($meal_id > 0 && !empty($meal_name)) {
    // Update meal details in the database using MySQLi
    $stmt = $conn->prepare("UPDATE meals SET meal_name = ? WHERE id = ?");
    $stmt->bind_param("si", $meal_name, $meal_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or meal list
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

<?php
// delete-meal.php

require 'db_conn.php'; // Include your database connection

// Get the meal ID from the query string
$meal_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($meal_id > 0) {
    // Delete meal from the database using MySQLi
    $stmt = $conn->prepare("DELETE FROM meals WHERE id = ?");
    $stmt->bind_param("i", $meal_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or meal list
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Delete failed.");
    }
    $stmt->close();
} else {
    die("Invalid meal ID.");
}

$conn->close();

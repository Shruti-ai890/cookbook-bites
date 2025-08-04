<?php
// delete-ingredient.php

require 'db_conn.php'; // Include your database connection

// Get the ingredient ID from the query string
$ingredient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($ingredient_id > 0) {
    // Delete ingredient from the database using MySQLi
    $stmt = $conn->prepare("DELETE FROM ingredients WHERE id = ?");
    $stmt->bind_param("i", $ingredient_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or ingredient list
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Delete failed.");
    }
    $stmt->close();
} else {
    die("Invalid ingredient ID.");
}

$conn->close();

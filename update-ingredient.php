<?php
// update-ingredient.php

require 'db_conn.php'; // Include your database connection

// Get POST data
$ingredient_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$ingredient_name = isset($_POST['ingredient_name']) ? trim($_POST['ingredient_name']) : '';

if ($ingredient_id > 0 && !empty($ingredient_name)) {
    // Update ingredient details in the database using MySQLi
    $stmt = $conn->prepare("UPDATE ingredients SET ingredient_name = ? WHERE id = ?");
    $stmt->bind_param("si", $ingredient_name, $ingredient_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or ingredient list
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

<?php
// delete-chef.php

require 'db_conn.php'; // Include your database connection

// Get the chef ID from the query string
$chef_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($chef_id > 0) {
    // Delete chef from the database using MySQLi
    $stmt = $conn->prepare("DELETE FROM chefs WHERE chef_id = ?");
    $stmt->bind_param("i", $chef_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or chef list
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Delete failed.");
    }
    $stmt->close();
} else {
    die("Invalid chef ID.");
}

$conn->close();

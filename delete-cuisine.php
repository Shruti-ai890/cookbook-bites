<?php
// delete-cuisine.php

require 'db_conn.php'; // Include your database connection

// Get the cuisine ID from the query string
$cuisine_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cuisine_id > 0) {
    // Delete cuisine from the database using MySQLi
    $stmt = $conn->prepare("DELETE FROM cuisines WHERE cuisine_id = ?");
    $stmt->bind_param("i", $cuisine_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect back to the admin dashboard or cuisine list
        header("Location: admin_dashboard.php");
        exit();
    } else {
        die("Delete failed.");
    }
    $stmt->close();
} else {
    die("Invalid cuisine ID.");
}

$conn->close();

<?php
// delete-recipe.php

include 'db_conn.php'; // Include your database connection file

if (isset($_GET['id'])) {
    $recipe_id = intval($_GET['id']);
    
    $query = "DELETE FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        header("Location: admin_dashboard.php"); // Redirect to the page showing recipes
        exit;
    } else {
        echo "Delete failed.";
    }
}
?>

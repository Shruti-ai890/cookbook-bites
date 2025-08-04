<?php
// Include database connection
include 'db_conn.php';

// Check if ID is set
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare SQL statement to delete the recipe
    $sql = "DELETE FROM user_recipes WHERE id = ?";
    
    // Initialize statement
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "i", $id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the previous page or to a confirmation page
            header("Location: admin_dashboard.php?message=Recipe deleted successfully");
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
} else {
    echo "ID not set";
}

// Close connection
mysqli_close($conn);
?>

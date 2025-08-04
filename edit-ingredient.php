<?php
// edit-ingredient.php

require 'db_conn.php'; // Include your database connection

// Get the ingredient ID from the query string
$ingredient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($ingredient_id > 0) {
    // Fetch ingredient details from the database using MySQLi
    $stmt = $conn->prepare("SELECT * FROM ingredients WHERE id = ?");
    $stmt->bind_param("i", $ingredient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ingredient = $result->fetch_assoc();

    if (!$ingredient) {
        die("Ingredient not found.");
    }
    $stmt->close();
} else {
    die("Invalid ingredient ID.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Ingredient</title>
    <style>
/* General Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

/* Form Container */
form {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Form Labels */
form label {
    display: block;
    font-size: 18px;
    margin-bottom: 8px;
    color: #333;
}

/* Form Inputs */
form input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

/* Button Styling */
form button {
    padding: 10px 20px;
    background-color: #ff6b6b;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
}

form button:hover {
    background-color: #ff4949;
}

/* Responsive Design */
@media (max-width: 768px) {
    form {
        padding: 15px;
    }

    form label, form button {
        font-size: 16px;
    }

    form input[type="text"] {
        padding: 8px;
        font-size: 14px;
    }
}

</style>
</head>
<body>
    <h1>Edit Ingredient</h1>
    <form action="update-ingredient.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($ingredient['id']) ?>">
        <label for="ingredient_name">Ingredient Name:</label>
        <input type="text" name="ingredient_name" id="ingredient_name" value="<?= htmlspecialchars($ingredient['ingredient_name']) ?>" required>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</body>
</html>

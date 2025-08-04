<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the POST variables are set
    $chef_id = isset($_POST['chef_id']) ? $_POST['chef_id'] : null;
    $cuisine_id = isset($_POST['cuisine_id']) ? $_POST['cuisine_id'] : null;
    $meal_id = isset($_POST['meal_id']) ? $_POST['meal_id'] : null;
    $ingredient_id = isset($_POST['ingredient_id']) ? $_POST['ingredient_id'] : null;
    $recipe_name = isset($_POST['recipe_name']) ? $_POST['recipe_name'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $servings = isset($_POST['servings']) ? $_POST['servings'] : null;
    $cooking_time = isset($_POST['cooking_time']) ? $_POST['cooking_time'] : null;

    // Handle file upload
    $recipe_image = '';
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] == 0) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['recipe_image']['name']);
        if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $upload_file)) {
            $recipe_image = basename($_FILES['recipe_image']['name']);
        } else {
            echo "Failed to upload image.";
        }
    }

    // Check if all required fields are present
    if ($chef_id !== null && $cuisine_id !== null && $recipe_name !== null && $description !== null && $meal_id !== null && $ingredient_id !== null && $servings !== null && $cooking_time !== null) {
        // Prepare the SQL query
        $sql = "INSERT INTO recipes (chef_id, cuisine_id, recipe_name, description, recipe_image, servings, cooking_time, meal_id, ingredient_id)
                VALUES ('$chef_id', '$cuisine_id', '$recipe_name', '$description', '$recipe_image', '$servings', '$cooking_time', '$meal_id', '$ingredient_id')";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            echo "New recipe created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: One or more required fields are missing.";
    }

    // Close the connection
    $conn->close();
}
?>




<form action="process_form.php" method="post" enctype="multipart/form-data">
    <label for="chef_id">Chef ID:</label>
    <input type="number" id="chef_id" name="chef_id" required><br>

    <label for="cuisine_id">Cuisine ID:</label>
    <input type="number" id="cuisine_id" name="cuisine_id" required><br>

    <label for="meal_id">Meal ID:</label>
    <input type="number" id="meal_id" name="meal_id" required><br>

    <label for="ingredient_id">Ingredient ID:</label>
    <input type="number" id="ingredient_id" name="ingredient_id" required><br>

    <label for="recipe_name">Recipe Name:</label>
    <input type="text" id="recipe_name" name="recipe_name" required><br>

    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea><br>

    <label for="recipe_image">Recipe Image:</label>
    <input type="file" id="recipe_image" name="recipe_image" accept="image/*"><br>

    <label for="servings">Servings:</label>
    <input type="number" id="servings" name="servings" required><br>

    <label for="cooking_time">Cooking Time (minutes):</label>
    <input type="number" id="cooking_time" name="cooking_time" required><br>

    <input type="submit" value="Submit">
</form>

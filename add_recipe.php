<?php
// add_recipe.php

include 'db_conn.php';

$message = "";

// Initialize form variables
$recipe_name = $chef_id = $meal_id = $ingredient_id = $cuisine_id = $recipe_description = $servings = $cooking_time = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_recipe'])) {
        $recipe_name = $_POST['recipe_name'];
        $chef_id = isset($_POST['chef_id']) && !empty($_POST['chef_id']) ? $_POST['chef_id'] : null;
        $meal_id = isset($_POST['meal_id']) && !empty($_POST['meal_id']) ? $_POST['meal_id'] : null;
        $ingredient_id = isset($_POST['ingredient_id']) && !empty($_POST['ingredient_id']) ? $_POST['ingredient_id'] : null;
        $cuisine_id = isset($_POST['cuisine_id']) && !empty($_POST['cuisine_id']) ? $_POST['cuisine_id'] : null;
        $recipe_description = $_POST['recipe_description'];
        $servings = $_POST['servings'];
        $cooking_time = $_POST['cooking_time'];

        // Handle the image upload
        $image_name = $_FILES['recipe_image']['name'];
        $image_tmp_name = $_FILES['recipe_image']['tmp_name'];
        $image_size = $_FILES['recipe_image']['size'];
        $image_error = $_FILES['recipe_image']['error'];

        // Define the directory to store the uploaded images
        $upload_dir = 'uploads/';
        $target_file = $upload_dir . basename($image_name);

        // Check if the recipe already exists
        $check_sql = "SELECT * FROM recipes WHERE recipe_name = '$recipe_name' 
                      AND chef_id = '$chef_id' 
                      AND meal_id = '$meal_id' 
                      AND ingredient_id = '$ingredient_id' 
                      AND cuisine_id = '$cuisine_id'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $message = "Recipe already exists!";
        } else {
            if ($image_error === 0) {
                if (move_uploaded_file($image_tmp_name, $target_file)) {
                    // Insert recipe into the database
                    $sql = "INSERT INTO recipes (recipe_name, chef_id, meal_id, ingredient_id, cuisine_id, description, recipe_image, servings, cooking_time) 
                            VALUES ('$recipe_name', '$chef_id', '$meal_id', '$ingredient_id', '$cuisine_id', '$recipe_description', '$target_file','$servings', '$cooking_time')";
                    if (mysqli_query($conn, $sql)) {
                        $message = "Recipe added successfully!";
                        // Clear the form fields
                        $recipe_name = $chef_id = $meal_id = $ingredient_id = $cuisine_id = $recipe_description = "";
                        $servings = $cooking_time = "";
                    } else {
                        $message = "Error: " . mysqli_error($conn);
                    }
                } else {
                    $message = "Failed to upload image.";
                }
            } else {
                $message = "Error uploading image.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <style>
            body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #ff6347;
            color: #fff;
            padding: 1em 0;
            text-align: center;
            position: relative;
        }
        .login-container {
            position: absolute;
            top: 0.5em;
            left: 1em;
            display: flex;
            gap: 0.5em;
        }
        .login-container a {
            color: #fff;
            text-decoration: none;
            padding: 0.5em 1em;
            background-color: #ff4500;
            border-radius: 4px;
            display: block;
            cursor: pointer;
        }
        .login-container a:hover {
            background-color: #ff6347;
        }
        nav {
            margin: 1em 0;
        }
        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            position: relative;
            margin: 0 0.5em;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 0.5em 1em;
            background-color: #ff4500;
            border-radius: 4px;
            display: block;
        }
        nav a:hover {
            background-color: #ff6347;
        }
        .form-container {
            display: none;
            background-color: #fff;
            padding: 2em;
            margin: 2em auto;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
        }
        input[type="text"], textarea,select{
            width: 100%;
            padding: 0.5em;
            margin-bottom: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        textarea {
            resize: vertical;
            height: 100px;
        }
        input[type="submit"] {
            padding: 0.5em 2em;
            background-color: #ff6347;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #ff4500;
        }
        .message {
            margin-bottom: 1em;
            color: green;
        }
    </style>
</head>
<body>
    <div class="message"><?php echo $message; ?></div>
    <h2>Add Recipe</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="recipe_name">Recipe Name</label>
        <input type="text" id="recipe_name" name="recipe_name" value="<?php echo htmlspecialchars($recipe_name); ?>" required>
        
        <label for="chef_id">Chef</label>
        <select id="chef_id" name="chef_id" required>
            <option value="">Select Chef</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM chefs");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['chef_name']) . "</option>";
            }
            ?>
        </select>
        
        <label for="meal_id">Meal</label>
        <select id="meal_id" name="meal_id" required>
            <option value="">Select Meal</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM meals");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['meal_name']) . "</option>";
            }
            ?>
        </select>
        
        <label for="ingredient_id">Ingredient</label>
        <select id="ingredient_id" name="ingredient_id" required>
            <option value="">Select Ingredient</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM ingredients");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['ingredient_name']) . "</option>";
            }
            ?>
        </select>
        
        <label for="cuisine_id">Cuisine</label>
        <select id="cuisine_id" name="cuisine_id" required>
            <option value="">Select Cuisine</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM cuisines");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['cuisine_name']) . "</option>";
            }
            ?>
        </select>
        
        <label for="recipe_description">Recipe Description</label>
        <textarea id="recipe_description" name="recipe_description"><?php echo htmlspecialchars($recipe_description); ?></textarea>
        
        <label for="servings">Servings</label>
        <input type="number" id="servings" name="servings" value="<?php echo htmlspecialchars($servings); ?>" required>
        
        <label for="cooking_time">Cooking Time</label>
        <input type="text" id="cooking_time" name="cooking_time" value="<?php echo htmlspecialchars($cooking_time); ?>" required>
        
        <label for="recipe_image">Recipe Image</label>
        <input type="file" id="recipe_image" name="recipe_image" accept="image/*" required>
        
        <input type="submit" name="add_recipe" value="Add Recipe">
    </form>
</body>
</html>

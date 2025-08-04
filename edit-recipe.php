<?php
include 'db_conn.php'; // Include your database connection file

if (isset($_GET['id'])) {
    $recipe_id = intval($_GET['id']);
    
    // Fetch the recipe details
    $query = "SELECT * FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();
    
    if (!$recipe) {
        echo "Recipe not found.";
        exit;
    }

    // Fetch data for the form options
    $chefs_query = "SELECT * FROM chefs";
    $chefs_result = $conn->query($chefs_query);
    $chefs = $chefs_result->fetch_all(MYSQLI_ASSOC);

    $meals_query = "SELECT * FROM meals";
    $meals_result = $conn->query($meals_query);
    $meals = $meals_result->fetch_all(MYSQLI_ASSOC);

    $ingredients_query = "SELECT * FROM ingredients";
    $ingredients_result = $conn->query($ingredients_query);
    $ingredients = $ingredients_result->fetch_all(MYSQLI_ASSOC);

    $cuisines_query = "SELECT * FROM cuisines";
    $cuisines_result = $conn->query($cuisines_query);
    $cuisines = $cuisines_result->fetch_all(MYSQLI_ASSOC);

} else {
    echo "No recipe ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe</title>
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
        form input[type="text"],
        form input[type="number"],
        form textarea,
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Dropdown Styling */
        form select {
            width: calc(100% + 20px); /* Increase width for dropdowns */
            padding: 10px;
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

            form input[type="text"],
            form input[type="number"],
            form textarea,
            form select {
                padding: 8px;
                font-size: 14px;
            }

            form select {
                width: 100%; /* Adjust width for smaller screens */
	 white-space: pre-wrap;
            }
        }
    </style>
</head>
<body>
    <h2>Edit Recipe</h2>
    <form action="update-recipe.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= htmlspecialchars($recipe['id']) ?>">
    <input type="hidden" name="existing_image" value="<?= htmlspecialchars($recipe['recipe_image']) ?>">

    <label for="recipe_name"><b>Recipe Name:</b></label>
    <input type="text" id="recipe_name" name="recipe_name" value="<?= htmlspecialchars($recipe['recipe_name']) ?>" required>
        
        <label for="chef_id"><b>Chef:</b></label>
        <select id="chef_id" name="chef_id" required>
            <?php foreach ($chefs as $chef): ?>
                <option value="<?=$chef['chef_id']?>" <?=$chef['chef_id'] == $recipe['chef_id'] ? 'selected' : ''?>><?=$chef['chef_name']?></option>
            <?php endforeach; ?>
        </select>

        <label for="meal_id"><b>Meal:</b></label>
        <select id="meal_id" name="meal_id" required>
            <?php foreach ($meals as $meal): ?>
                <option value="<?=$meal['id']?>" <?=$meal['id'] == $recipe['meal_id'] ? 'selected' : ''?>><?=$meal['meal_name']?></option>
            <?php endforeach; ?>
        </select>

        <label for="ingredient_id"><b>Ingredient:</b></label>
        <select id="ingredient_id" name="ingredient_id" required>
            <?php foreach ($ingredients as $ingredient): ?>
                <option value="<?=$ingredient['id']?>" <?=$ingredient['id'] == $recipe['ingredient_id'] ? 'selected' : ''?>><?=$ingredient['ingredient_name']?></option>
            <?php endforeach; ?>
        </select>

        <label for="cuisine_id"><b>Cuisine:</b></label>
        <select id="cuisine_id" name="cuisine_id" required>
            <?php foreach ($cuisines as $cuisine): ?>
                <option value="<?=$cuisine['cuisine_id']?>" <?=$cuisine['cuisine_id'] == $recipe['cuisine_id'] ? 'selected' : ''?>><?=$cuisine['cuisine_name']?></option>
            <?php endforeach; ?>
        </select>

       <label for="ingredients"><b>Ingredients (text format):</b></label>
    <textarea id="ingredients" name="ingredients" row="10"><?= htmlspecialchars($recipe['ingredients']) ?></textarea>

    <label for="steps"><b>Steps:</b></label>
    <textarea id="steps" name="steps" rows="10"><?= htmlspecialchars($recipe['steps']) ?></textarea>


        <label for="recipe_image"><b>Image:</b></label>
        <input type="file" id="recipe_image" name="recipe_image">
        <?php if ($recipe['recipe_image']): ?>
        <img src="uploads/<?= htmlspecialchars($recipe['recipe_image']) ?>" width="100" alt="<?= htmlspecialchars($recipe['recipe_name']) ?>">
    <?php endif; ?>

        <label for="servings"><b>Servings:</b></label>
        <input type="number" id="servings" name="servings" value="<?=$recipe['servings']?>" required>

        <label for="cooking_time"><b>Cooking Time:</b></label>
        <input type="text" id="cooking_time" name="cooking_time" value="<?=$recipe['cooking_time']?>" required>

        <button type="submit" class="btn btn-primary">Update Recipe</button>
    </form>
</body>
</html>

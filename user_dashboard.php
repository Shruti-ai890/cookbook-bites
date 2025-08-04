<?php
session_start();
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$meals_query = "SELECT * FROM meals";
$meals_result = mysqli_query($conn, $meals_query);

// Fetch cuisines for dropdown
$cuisines_query = "SELECT * FROM cuisines";
$cuisines_result = mysqli_query($conn, $cuisines_query);

// Fetch user information
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM customer WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userName = htmlspecialchars($user['name']);
} else {
    $userName = "User"; // Default value if no user is found
}

// Fetch recipes from the recipes table
$recipes_query = "SELECT id, recipe_name FROM recipes";
$recipes_result = mysqli_query($conn, $recipes_query);

// Fetch recipes from the user_recipes table
$user_recipes_query = "SELECT id,recipe_name FROM user_recipes";
$user_recipes_result = mysqli_query($conn, $user_recipes_query);



// Combine results
$all_recipes = []; // Initialize an empty array

// Combine recipes from the recipes table
while ($row = mysqli_fetch_assoc($recipes_result)) {
    $all_recipes[] = $row; // Add each recipe to the array
}

// Combine recipes from the user_recipes table
while ($row = mysqli_fetch_assoc($user_recipes_result)) {
    $all_recipes1[] = $row; // Add each recipe to the array
}






$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        .header {
            background-color: #ff4500;
            color: #fff;
            padding: 1em;
            text-align: center;
            position: relative;
        }

        .header h1 {
            margin: 0;
            font-size: 2em;
        }

        .nav {
            display: flex;
            justify-content: center;
            gap: 1em;
            margin-top: 1em;
        }

        .nav button {
            padding: 0.5em 1em;
            border: none;
            border-radius: 4px;
            background-color: #ff6347;
            color: #fff;
            cursor: pointer;
            font-size: 1em;
        }

        .nav button:hover {
            background-color: #ffe4e1; /* Light color on hover */
        }

        .form-container {
            background-color: #fff;
            padding: 2em;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 2em auto; /* Center the form */
            max-width: 800px;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }

        .form-container form input,
        .form-container form textarea,
        .form-container form select {
            margin-bottom: 1em;
            padding: 0.5em;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .form-container form button {
            padding: 0.5em 1em;
            border: none;
            border-radius: 4px;
            background-color: #ff6347;
            color: #fff;
            font-size: 1em;
            cursor: pointer;
        }

        .form-container form button:hover {
            background-color: #ffe4e1; /* Light color on hover */
        }
.autocomplete-items {
    list-style-type: none;
    padding: 0;
    margin: 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 150px;
    overflow-y: auto;
    background: white;
    position: absolute;
    z-index: 999;
}

.autocomplete-items li {
    padding: 0.5em;
    cursor: pointer;
}

.autocomplete-items li:hover {
    background-color: #f0f0f0;
}
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo $userName; ?></h1>
        <div class="nav">
            <button class="logout" onclick="location.href='logout.php'">Logout</button>
         <button class="add-recipe" onclick="toggleRecipeForm()">Add Recipe</button>
            <button class="feedback" onclick="toggleFeedbackForm()">Feedback</button>


        </div>
    </div>

    <div id="add-recipe-form" class="form-container">
        <h2>Add Recipe</h2>
        <form action="add-recipe.php" method="post" enctype="multipart/form-data">
            <label for="recipe_name">Recipe Name:</label>
            <input type="text" id="recipe_name" name="recipe_name" required>

            <label for="meal_id">Meal:</label>
            <select id="meal_id" name="meal_id" required>
                <option value="" disabled selected>Select a Meal</option>
                <?php
                while ($meal = mysqli_fetch_assoc($meals_result)) {
                    echo "<option value='{$meal['id']}'>{$meal['meal_name']}</option>";
                }
                ?>
            </select>

            <label for="cuisine_id">Cuisine:</label>
            <select id="cuisine_id" name="cuisine_id" required>
                <option value="" disabled selected>Select a Cuisine</option>
                <?php
                while ($cuisine = mysqli_fetch_assoc($cuisines_result)) {
                    echo "<option value='{$cuisine['cuisine_id']}'>{$cuisine['cuisine_name']}</option>";
                }
                ?>
            </select>

            <label for="ingredients">Ingredients:</label>
            <textarea id="ingredients" name="ingredients" rows="4" required></textarea>

            <label for="steps">Recipe Steps:</label>
            <textarea id="steps" name="steps" rows="4" required></textarea>

            <label for="recipe_image">Recipe Image:</label>
            <input type="file" id="recipe_image" name="recipe_image" accept="image/*" required>

            <label for="servings">Servings:</label>
            <input type="number" id="servings" name="servings" required>

            <label for="cooking_time">Cooking Time (minutes):</label>
            <input type="text" id="cooking_time" name="cooking_time" required>

            <button type="submit">Submit Recipe</button>
        </form>
    </div>


<div id="feedback-form" class="form-container" style="display:none;">
    <h2>Submit Feedback</h2>
    <form action="submit_feedback.php" method="post">
        <label for="recipe_select">Recipe Name:</label>
        <select id="recipe_select" name="recipe_id" required>
            <option value="" disabled selected>Select a Recipe</option>
            <?php foreach ($all_recipes as $recipe): ?>
                <option value="<?php echo htmlspecialchars($recipe['id']); ?>">
                    <?php echo htmlspecialchars($recipe['recipe_name']); ?>
                </option>
            <?php endforeach; ?>

<?php foreach ($all_recipes1 as $recipe1): ?>
                <option value="<?php echo htmlspecialchars($recipe1['id']); ?>">
                    <?php echo htmlspecialchars($recipe1['recipe_name']); ?>
                </option>
            <?php endforeach; ?>


        </select>

        <label for="feedback">Your Feedback:</label>
        <textarea id="feedback" name="feedback" rows="4" required></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
</div>






<script>
        function toggleRecipeForm() {
            var recipeForm = document.getElementById('add-recipe-form');
            var feedbackForm = document.getElementById('feedback-form');
            if (recipeForm.style.display === 'none' || recipeForm.style.display === '') {
                recipeForm.style.display = 'block';
                feedbackForm.style.display = 'none';
            } else {
                recipeForm.style.display = 'none';
            }
        }

       function toggleFeedbackForm() {
    var feedbackForm = document.getElementById('feedback-form');
    var recipeForm = document.getElementById('add-recipe-form');
    if (feedbackForm.style.display === 'none' || feedbackForm.style.display === '') {
        feedbackForm.style.display = 'block';
        recipeForm.style.display = 'none';
    } else {
        feedbackForm.style.display = 'none';
    }
}

        // Autocomplete function
      document.addEventListener('DOMContentLoaded', function() {
    const recipeInput = document.getElementById('recipe_name');
    const autocompleteList = document.getElementById('autocomplete-list');

    recipeInput.addEventListener('input', function() {
        const query = this.value;

        if (query.length === 0) {
            autocompleteList.innerHTML = '';
            return;
        }

        fetch(`search_recipes.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                autocompleteList.innerHTML = '';
                data.forEach(recipe => {
                    const item = document.createElement('li');
                    item.textContent = recipe.recipe_name;
                    item.addEventListener('click', function() {
                        recipeInput.value = recipe.recipe_name;
                        autocompleteList.innerHTML = '';
                    });
                    autocompleteList.appendChild(item);
                });
            });
    });
});

    </script>


    <script>
        // Ensure the form is displayed by default
        document.getElementById('add-recipe-form').style.display = 'block';

        function toggleForm() {
            var form = document.getElementById('add-recipe-form');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</body>
</html>

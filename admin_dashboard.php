<?php
// admin_dashboard.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: admin_login.php');
    exit();
}

include 'db_conn.php';

$message = "";

// Initialize form variables
$chef_name = $chef_description = "";
$cuisine_name = "";
$meal_name = "";
$ingredient_name = "";
$recipe_name = "";
$recipe_description = $servings = $cooking_time = "";
$chef_id = $meal_id = $ingredient_id = $cuisine_id = "";

// Fetch data for the 'view_recipes' section
$recipes_query = "SELECT * FROM recipes";
$recipes_result = mysqli_query($conn,$recipes_query);
$recipes = mysqli_fetch_all($recipes_result, MYSQLI_ASSOC);



// Fetch recipes data from the user_recipes table
$sql = "SELECT * FROM user_recipes";
$user_recipes_result = mysqli_query($conn, $sql);

if (!$user_recipes_result) {
    die("Query failed: " . mysqli_error($conn));
}



$chefs_query = "SELECT * FROM chefs";
$chefs_result = mysqli_query($conn, $chefs_query);
$chefs = mysqli_fetch_all($chefs_result, MYSQLI_ASSOC);

$meals_query = "SELECT * FROM meals";
$meals_result = mysqli_query($conn,$meals_query);
$meals = mysqli_fetch_all($meals_result ,MYSQLI_ASSOC);

$ingredients_query = "SELECT * FROM ingredients";
$ingredients_result = mysqli_query( $conn,$ingredients_query);
$ingredients = mysqli_fetch_all($ingredients_result,MYSQLI_ASSOC);

$cuisines_query = "SELECT * FROM cuisines";
$cuisines_result = mysqli_query($conn, $cuisines_query);
$cuisines = mysqli_fetch_all($cuisines_result , MYSQLI_ASSOC);



// Determine which form to show based on the POST data
$current_form = 'view_recipes';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = "";

    // Handle adding a chef
    if (isset($_POST['add_chef'])) {
        $chef_name = mysqli_real_escape_string($conn, $_POST['chef_name']);
        $chef_description = mysqli_real_escape_string($conn, $_POST['chef_description']);

        // Check if the chef already exists
        $check_sql = "SELECT * FROM chefs WHERE chef_name = '$chef_name'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $message = "Chef already exists!";
            $current_form = 'add_chef_form';
        } else {
            $sql = "INSERT INTO chefs (chef_name, chef_description) VALUES ('$chef_name', '$chef_description')";
            if (mysqli_query($conn, $sql)) {
                $message = "Chef added successfully!";
                $chef_name = $chef_description = "";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }

    // Handle adding a cuisine
    elseif (isset($_POST['add_cuisine'])) {
        $cuisine_name = mysqli_real_escape_string($conn, $_POST['cuisine_name']);

        // Check if the cuisine already exists
        $check_sql = "SELECT * FROM cuisines WHERE cuisine_name = '$cuisine_name'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $message = "Cuisine already exists!";
            $current_form = 'add_cuisine_form';
        } else {
            $sql = "INSERT INTO cuisines (cuisine_name) VALUES ('$cuisine_name')";
            if (mysqli_query($conn, $sql)) {
                $message = "Cuisine added successfully!";
                $cuisine_name = "";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }

    // Handle adding a meal
    elseif (isset($_POST['add_meal'])) {
        $meal_name = mysqli_real_escape_string($conn, $_POST['meal_name']);

        // Check if the meal already exists
        $check_sql = "SELECT * FROM meals WHERE meal_name = '$meal_name'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $message = "Meal already exists!";
            $current_form = 'add_meal_form';
        } else {
            $sql = "INSERT INTO meals (meal_name) VALUES ('$meal_name')";
            if (mysqli_query($conn, $sql)) {
                $message = "Meal added successfully!";
                $meal_name = "";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }

    // Handle adding an ingredient
    elseif (isset($_POST['add_ingredient'])) {
        $ingredient_name = mysqli_real_escape_string($conn, $_POST['ingredient_name']);

        // Check if the ingredient already exists
        $check_sql = "SELECT * FROM ingredients WHERE ingredient_name = '$ingredient_name'";
        $result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            $message = "Ingredient already exists!";
            $current_form = 'add_ingredient_form';
        } else {
            $sql = "INSERT INTO ingredients (ingredient_name) VALUES ('$ingredient_name')";
            if (mysqli_query($conn, $sql)) {
                $message = "Ingredient added successfully!";
                $ingredient_name = "";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        }
    }

    // Handle adding a recipe
    elseif (isset($_POST['add_recipe'])) {
        $recipe_name = mysqli_real_escape_string($conn, $_POST['recipe_name']);
        $chef_id = (int) $_POST['chef_id']; // cast to int for safety
        $meal_id = (int) $_POST['meal_id']; // cast to int for safety
        $ingredient_id = (int) $_POST['ingredient_id']; // cast to int for safety
        $cuisine_id = (int) $_POST['cuisine_id']; // cast to int for safety
        $ingredients = $_POST['ingredients'];
        $steps = $_POST['steps']; // Procedure
        $servings = mysqli_real_escape_string($conn, $_POST['servings']);
        $cooking_time = mysqli_real_escape_string($conn, $_POST['cooking_time']);
         
        

 // Handle the image upload
        if (!empty($_FILES['recipe_image']['name'])) {
            $image_name = $_FILES['recipe_image']['name'];
            $image_tmp_name = $_FILES['recipe_image']['tmp_name'];
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
                        $sql = "INSERT INTO recipes (recipe_name, chef_id, meal_id, ingredient_id, cuisine_id, ingredients,steps, recipe_image, servings, cooking_time) 
                                VALUES ('$recipe_name', '$chef_id', '$meal_id', '$ingredient_id', '$cuisine_id', '$ingredients','$steps', '$target_file', '$servings', '$cooking_time')";
                        if (mysqli_query($conn, $sql)) {
                            $message = "Recipe added successfully!";
                            $recipe_name = $chef_id = $meal_id = $ingredient_id = $cuisine_id = $recipe_description = $servings = $cooking_time = "";
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
        } else {
            $message = "Recipe image is required.";
        }


        $current_form = 'add_recipe_form';
    }
}



// Handle edit and delete actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if ($action == 'edit') {
        header("Location: edit-recipe.php?id=$id");
        exit;
    } elseif ($action == 'delete') {
        $delete_query = "DELETE FROM recipes WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin_dashboard.php");
        exit;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
   
    <script>
        function showForm(formId) {
            document.querySelectorAll('.form-container').forEach(function(form) {
                form.style.display = 'none';
            });
            document.getElementById(formId).style.display = 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            var initialForm = '<?php echo $current_form; ?>';
            showForm(initialForm);
        });
    </script>


<style>
/* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

html, body {
    height: 100%; /* Ensure body takes up full height */
    margin: 0; /* Remove default margin */
    padding: 0; /* Remove default padding */
}

/* Header Styles */
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

/* Navigation Styles */
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

/* Form Container Styles */
.form-container {
    background-color: #fff;
    padding: 2em;
    margin: 2em auto;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative; /* Allows for positioning of pseudo-elements */
    width: calc(100% - 40px); /* Full width minus padding (adjust as needed) */
    max-width: 100vw; /* Ensure it does not exceed viewport width */
    margin: 20px auto; /* Center horizontally and add vertical space */
    padding: 20px; /* Add padding inside the container */
    box-sizing: border-box; /* Include padding and border in the element’s total width and height */
}

/* Form Header */
.form-container h2 {
    text-align: center;
    color: #ff6347; /* Tomato color for the header */
    font-size: 2em;
    margin-bottom: 20px;
}

/* Form Labels */
.form-container label {
    display: block;
    margin-bottom: 10px;
    color: #333;
    font-weight: bold;
}

/* Form Fields */
.form-container input[type="text"],
.form-container input[type="number"],
.form-container input[type="file"],
.form-container select,
.form-container textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1em;
}

/* Textarea Style */
.form-container textarea {
    resize: vertical; /* Allow vertical resize */
    height: 100px;
}

/* Submit Button */
.form-container input[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #ff6347; /* Tomato color for the button */
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 1.2em;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.form-container input[type="submit"]:hover {
    background-color: #ff4500; /* Darker tomato color on hover */
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-container {
        width: calc(100% - 20px); /* Adjust width for smaller screens */
        margin: 10px auto; /* Adjust margin for smaller screens */
    }
}

/* Table Styles */
table {
    width: 100%; /* Table takes full width of its container */
    table-layout: auto; /* Allows columns to adjust based on content */
    border-collapse: collapse; /* Collapses borders to avoid double borders */
    margin-bottom: 20px; /* Space between tables */
}

th, td {
    padding: 10px; /* Add padding inside table cells */
    text-align: left; /* Align text to the left */
    border: 1px solid #ddd; /* Add border inside table cells */
}

thead th {
    background-color: #f4f4f4; /* Light background for headers */
}

/* Image Styles */
img {
    width: 200px; /* Ensure images fit within their containers */
    height: 200px; /* Set a fixed height for images */
    object-fit: cover; /* Cover the content of the image while preserving aspect ratio */
}

/* Message Styles */
.message {
    margin-bottom: 1em;
    color: green;
}



    </style>
</head>
<body>
    <header>
        <div class="login-container">
            <a href="logout.php">Logout</a>
        </div>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="javascript:void(0)" onclick="showForm('view_recipes')">Admin</a></li> 
                <li><a href="javascript:void(0)" onclick="showForm('add_chef_form')">Add Chef</a></li>
                <li><a href="javascript:void(0)" onclick="showForm('add_cuisine_form')">Add Cuisine</a></li>
                <li><a href="javascript:void(0)" onclick="showForm('add_meal_form')">Add Meal</a></li>
                <li><a href="javascript:void(0)" onclick="showForm('add_ingredient_form')">Add Ingredient</a></li>
                <li><a href="javascript:void(0)" onclick="showForm('add_recipe_form')">Add Recipe</a></li>
            </ul>
        </nav>
    </header>

    <div id="add_chef_form" class="form-container">
        <h2>Add Chef</h2>
        <form method="POST" action="admin_dashboard.php">
            <label for="chef_name">Chef Name:</label>
            <input type="text" id="chef_name" name="chef_name" required>
            
            <br>
            <input type="submit" name="add_chef" value="Add Chef">
        </form>
    </div>

    <div id="add_cuisine_form" class="form-container">
        <h2>Add Cuisine</h2>
        <form method="POST" action="admin_dashboard.php">
            <label for="cuisine_name">Cuisine Name:</label>
            <input type="text" id="cuisine_name" name="cuisine_name" required>
            <br>
            <input type="submit" name="add_cuisine" value="Add Cuisine">
        </form>
    </div>

    <div id="add_meal_form" class="form-container">
        <h2>Add Meal</h2>
        <form method="POST" action="admin_dashboard.php">
            <label for="meal_name">Meal Name:</label>
            <input type="text" id="meal_name" name="meal_name" required>
            <br>
            <input type="submit" name="add_meal" value="Add Meal">
        </form>
    </div>

    <div id="add_ingredient_form" class="form-container">
        <h2>Add Ingredient</h2>
        <form method="POST" action="admin_dashboard.php">
            <label for="ingredient_name">Ingredient Name:</label>
            <input type="text" id="ingredient_name" name="ingredient_name" required>
            <br>
            <input type="submit" name="add_ingredient" value="Add Ingredient">
        </form>
    </div>


<div id="add_recipe_form" class="form-container">
    <h2>Add Recipe</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label for="recipe_name">Recipe Name:</label>
        <input type="text" id="recipe_name" name="recipe_name" required>
        <br>
        <label for="chef_id">Chef:</label>
        <select id="chef_id" name="chef_id" required>
            <option value="" disabled selected>Select a Chef</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM chefs");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['chef_id']}'>{$row['chef_name']}</option>";
            }
            ?>
        </select>
        <br>
        <label for="meal_id">Meal:</label>
        <select id="meal_id" name="meal_id" required>
            <option value="" disabled selected>Select a Meal</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM meals");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['id']}'>{$row['meal_name']}</option>";
            }
            ?>
        </select>
        <br>
        <label for="ingredient_id">Ingredient:</label>
        <select id="ingredient_id" name="ingredient_id" required>
            <option value="" disabled selected>Select an Ingredient</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM ingredients");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['id']}'>{$row['ingredient_name']}</option>";
            }
            ?>
        </select>
        <br>
        <label for="cuisine_id">Cuisine:</label>
        <select id="cuisine_id" name="cuisine_id" required>
            <option value="" disabled selected>Select a Cuisine</option>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM cuisines");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='{$row['cuisine_id']}'>{$row['cuisine_name']}</option>";
            }
            ?>
        </select>
        <br>
        <label for="ingredients">Ingredients (text format):</label>
        <textarea id="ingredients" name="ingredients" required></textarea>
        <br>
        <label for="steps">Procedure/Steps:</label>
        <textarea id="steps" name="steps" required></textarea>
        <br>
        <label for="recipe_image">Upload Image:</label>
        <input type="file" id="recipe_image" name="recipe_image" required>
        <br>
        <label for="servings">Servings:</label>
        <input type="number" id="servings" name="servings" required>
        <br>
        <label for="cooking_time">Cooking Time:</label>
        <input type="text" id="cooking_time" name="cooking_time" required>
        <br>
        <input type="submit" name="add_recipe" value="Add Recipe">
    </form>
</div>






    <?php if ($message) : ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

<div id="view_recipes" class="form-container">
    <h2>All Recipes</h2>
    <table class="table table-bordered shadow" border=2>
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Recipe Name</th>
                <th>Chef Name</th>
                <th>Meal</th>
                <th>ingredient_name</th>
                <th>Ingredients</th>
                <th>Steps</th>
                <th>Cuisine</th>
                <th>Image</th>
                <th>Servings</th>
                <th>Cooking Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php 
    $i = 0;
    foreach ($recipes as $recipe) {
        $i++;
    ?>
    <tr>
        <td><?=$i?></td>
        <td><?=$recipe['recipe_name']?></td>
        <td>
            <?php
            $chefName = "Undefined";
            foreach ($chefs as $chef) {
                if ($chef['chef_id'] == $recipe['chef_id']) {
                    $chefName = $chef['chef_name'];
                    break;
                }
            }
            echo $chefName;
            ?>
        </td>
        <td>
            <?php
            $mealName = "Undefined";
            foreach ($meals as $meal) {
                if ($meal['id'] == $recipe['meal_id']) {
                    $mealName = $meal['meal_name'];
                    break;
                }
            }
            echo $mealName;
            ?>
        </td>
        <td>
            <?php
            $ingredientName = "Undefined";
            foreach ($ingredients as $ingredient) {
                if ($ingredient['id'] == $recipe['ingredient_id']) {
                    $ingredientName = $ingredient['ingredient_name'];
                    break;
                }
            }
            echo $ingredientName;
            ?>
        </td>
        <td>
            <?= htmlspecialchars($recipe['ingredients']) ?>
        </td>
        <td>
            <?= htmlspecialchars($recipe['steps']) ?>
        </td>
        <td>
            <?php
            $cuisineName = "Undefined";
            foreach ($cuisines as $cuisine) {
                if ($cuisine['cuisine_id'] == $recipe['cuisine_id']) {
                    $cuisineName = $cuisine['cuisine_name'];
                    break;
                }
            }
            echo $cuisineName;
            ?>
        </td>
        <td>
            <img width="100"  height="100" src="uploads/<?= htmlspecialchars($recipe['recipe_image']) ?>" alt="<?= htmlspecialchars($recipe['recipe_name']) ?>">
        </td>
        <td><?=$recipe['servings']?></td>
        <td><?=$recipe['cooking_time']?></td>
        <td>
            <a href="edit-recipe.php?id=<?=$recipe['id']?>" class="btn btn-warning">Edit</a>
            <a href="delete-recipe.php?id=<?=$recipe['id']?>" class="btn btn-danger">Delete</a>
        </td>
    </tr>
    <?php } ?>
</tbody>

    </table>
</div>


<!-- User Recipes Table -->
<div class="table-container">
    <h2>User Recipes Table</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Recipe Name</th>
                <th>Meal</th>
                <th>Cuisine</th>
                <th>Ingredients</th>
                <th>Steps</th>
                <th>Image</th>
                <th>Servings</th>
                <th>Cooking Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
                    <?php while ($user_recipe = mysqli_fetch_assoc($user_recipes_result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user_recipe['id']); ?></td>
                            <td><?php echo htmlspecialchars($user_recipe['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user_recipe['recipe_name']); ?></td>

                            <td>
                                <?php
                                $mealName = "Undefined";
                                foreach ($meals as $meal) {
                                    if ($meal['id'] == $user_recipe['meal_id']) {
                                        $mealName = $meal['meal_name'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($mealName);
                                ?>
                            </td>

                            <td>
                                <?php
                                $cuisineName = "Undefined";
                                foreach ($cuisines as $cuisine) {
                                    if ($cuisine['cuisine_id'] == $user_recipe['cuisine_id']) {
                                        $cuisineName = $cuisine['cuisine_name'];
                                        break;
                                    }
                                }
                                echo htmlspecialchars($cuisineName);
                                ?>
                            </td>

                            <td><?php echo htmlspecialchars($user_recipe['ingredients']); ?></td>
                            <td><?php echo htmlspecialchars($user_recipe['steps']); ?></td>
                            <td>
                                <img src="uploads/<?php echo htmlspecialchars($user_recipe['recipe_image']); ?>" alt="Recipe Image" style="width: 100px;">
                            </td>
                            <td><?php echo htmlspecialchars($user_recipe['servings']); ?></td>
                            <td><?php echo htmlspecialchars($user_recipe['cooking_time']); ?></td>
                            <td>
                                <a href="delete-user-recipe.php?id=<?php echo $user_recipe['id']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
    </table>
</div>




<?php if ($meals == 0) { ?>
    <div class="alert alert-warning text-center p-5" role="alert">
        <img src="img/empty.png" width="100">
        <br>
        There are no meals in the database
    </div>
<?php } else { ?>
    <!-- List of all Meals -->
    <h4 class="mt-5">All Meals</h4>
    <table class="table table-bordered shadow">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Meal Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $l = 0;
            foreach ($meals as $meal) {
                $l++;    
            ?>
            <tr>
                <td><?=$l?></td>
                <td><?=$meal['meal_name']?></td>
                <td>
                    <a href="edit-meal.php?id=<?=$meal['id']?>" class="btn btn-warning">Edit</a>
                    <a href="delete-meal.php?id=<?=$meal['id']?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

<?php if ($cuisines == 0) { ?>
    <div class="alert alert-warning text-center p-5" role="alert">
        <img src="img/empty.png" width="100">
        <br>
        There are no cuisines in the database
    </div>
<?php } else { ?>
    <!-- List of all Cuisines -->
    <h4 class="mt-5">All Cuisines</h4>
    <table class="table table-bordered shadow">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Cuisine Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $m = 0;
            foreach ($cuisines as $cuisine) {
                $m++;    
            ?>
            <tr>
                <td><?=$m?></td>
                <td><?=$cuisine['cuisine_name']?></td>
                <td>
                    <a href="edit-cuisine.php?id=<?=$cuisine['cuisine_id']?>" class="btn btn-warning">Edit</a>
                    <a href="delete-cuisine.php?id=<?=$cuisine['cuisine_id']?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>



<?php if ($chefs == 0) { ?>
    <div class="alert alert-warning text-center p-5" role="alert">
        <img src="img/empty.png" width="100">
        <br>
        There are no chefs in the database
    </div>
<?php } else { ?>
    <!-- List of all Chefs -->
    <h4 class="mt-5">All Chefs</h4>
    <table class="table table-bordered shadow">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Chef Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $n = 0;
            foreach ($chefs as $chef) {
                $n++;    
            ?>
            <tr>
                <td><?=$n?></td>
                <td><?=$chef['chef_name']?></td>
                <td>
                    <a href="edit-chef.php?id=<?=$chef['chef_id']?>" class="btn btn-warning">Edit</a>
                    <a href="delete-chef.php?id=<?=$chef['chef_id']?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

<?php if ($ingredients == 0) { ?>
    <div class="alert alert-warning text-center p-5" role="alert">
        <img src="img/empty.png" width="100">
        <br>
        There are no ingredients in the database
    </div>
<?php } else { ?>
    <!-- List of all Ingredients -->
    <h4 class="mt-5">All Ingredients</h4>
    <table class="table table-bordered shadow">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Ingredient Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $o = 0;
            foreach ($ingredients as $ingredient) {
                $o++;    
            ?>
            <tr>
                <td><?=$o?></td>
                <td><?=$ingredient['ingredient_name']?></td>
                <td>
                    <a href="edit-ingredient.php?id=<?=$ingredient['id']?>" class="btn btn-warning">Edit</a>
                    <a href="delete-ingredient.php?id=<?=$ingredient['id']?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

</body>
</html>

<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Retrieve ingredient name from query parameters
$ingredient_name = isset($_GET['ingredient']) ? trim($_GET['ingredient']) : '';

// Build the query based on whether an ingredient name is provided
if (!empty($ingredient_name)) {
    // Fetch the ingredient ID based on the provided ingredient name
    $ingredient_query = "SELECT id FROM ingredients WHERE ingredient_name = ?";
    $ingredient_stmt = $conn->prepare($ingredient_query);
    if ($ingredient_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
    $ingredient_stmt->bind_param("s", $ingredient_name);
    $ingredient_stmt->execute();
    $ingredient_result = $ingredient_stmt->get_result();
    $ingredient = $ingredient_result->fetch_assoc();

    if ($ingredient) {
        $ingredient_id = $ingredient['id'];
    } else {
        echo "No such ingredient found.";
        exit;
    }

    // Prepare statement to fetch recipes with the specified ingredient ID
    $query = "
        SELECT recipes.*, chefs.chef_name, cuisines.cuisine_name 
        FROM recipes
        JOIN chefs ON recipes.chef_id = chefs.chef_id
        JOIN cuisines ON recipes.cuisine_id = cuisines.cuisine_id
        WHERE recipes.ingredient_id = ?
        ORDER BY recipes.id DESC";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
    $stmt->bind_param("i", $ingredient_id);
} else {
    // Query to get all recipes if no ingredient filter is applied
    $query = "
        SELECT recipes.*, chefs.chef_name, cuisines.cuisine_name 
        FROM recipes
        JOIN chefs ON recipes.chef_id = chefs.chef_id
        JOIN cuisines ON recipes.cuisine_id = cuisines.cuisine_id
        ORDER BY recipes.id DESC";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
}

// Execute the query and fetch results
$stmt->execute();
$result = $stmt->get_result();
$recipes = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Gallery</title>
    <style>
        /* General Styling */
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
        }

        .gallery-container {
            padding: 2em;
            margin: 2em auto;
            max-width: 1200px;
            display: flex;
            flex-wrap: wrap;
            gap: 1em;
        }

        .recipe-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            flex: 1 1 calc(33% - 2em);
            box-sizing: border-box;
            padding: 1em;
        }

         .recipe-card img {
    max- width: 100%;
    height:200px; /* Adjust this to ensure images are square */
    object-fit: cover; /* Ensures the image covers the container without distortion */
    border-radius: 8px; /* Optional: adds rounded corners to the image */
}

        .recipe-card h3 {
            margin-top: 0.5em;
            font-size: 18px;
        }

        .recipe-card p {
            margin: 0.5em 0;
            font-size: 16px;
        }

        .recipe-card a {
            display: inline-block;
            margin-top: 0.5em;
            text-decoration: none;
            color: #ff6347;
            font-weight: bold;
        }

        .recipe-card a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .recipe-card {
                flex: 1 1 calc(50% - 2em);
            }
        }

        @media (max-width: 480px) {
            .recipe-card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Recipe Gallery</h1>
    </header>

    <div class="gallery-container">
        <?php if (!empty($recipes)): ?>
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <img src="uploads/<?php echo htmlspecialchars($recipe['recipe_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <h3><?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><strong>Chef:</strong> <?php echo htmlspecialchars($recipe['chef_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="recipe_detail.php?id=<?php echo htmlspecialchars($recipe['id'], ENT_QUOTES, 'UTF-8'); ?>">View Recipe</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recipes found for the specified ingredient.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Get the selected meal ID from the URL
$meal_id = isset($_GET['meal_id']) ? intval($_GET['meal_id']) : 0;

// Fetch recipes filtered by the selected meal from both tables
$query = "
    SELECT recipes.id, recipes.recipe_name, recipes.recipe_image, recipes.cooking_time, recipes.ingredients, recipes.steps, chefs.chef_name, cuisines.cuisine_name, 'recipe' AS source
    FROM recipes
    JOIN chefs ON recipes.chef_id = chefs.chef_id
    JOIN cuisines ON recipes.cuisine_id = cuisines.cuisine_id
    WHERE recipes.meal_id = ?
    
    UNION ALL

    SELECT user_recipes.id, user_recipes.recipe_name, user_recipes.recipe_image, user_recipes.cooking_time, user_recipes.ingredients, user_recipes.steps, customer.name AS chef_name, cuisines.cuisine_name, 'user_recipe' AS source
    FROM user_recipes
    JOIN cuisines ON user_recipes.cuisine_id = cuisines.cuisine_id
    JOIN customer ON user_recipes.user_id = customer.id
    WHERE user_recipes.meal_id = ?
    
    ORDER BY id DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $meal_id, $meal_id);
$stmt->execute();
$result = $stmt->get_result();

$recipes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipes by Meal</title>
    <style>
        /* Add your existing CSS here */
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
            max-width: 100%;
            height:200px;
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
        <h1>Recipes by Meal</h1>
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
                    <a href="<?php echo $recipe['source'] === 'recipe' ? 'recipe_detail.php?id=' : 'user_recipe_detail.php?id='; ?><?php echo htmlspecialchars($recipe['id'], ENT_QUOTES, 'UTF-8'); ?>">View Recipe</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recipes found for this meal.</p>
        <?php endif; ?>
    </div>
</body>
</html>


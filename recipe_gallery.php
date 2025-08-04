<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Fetch recipes for the gallery
$query = "
    SELECT recipes.*, chefs.chef_name, cuisines.cuisine_name 
    FROM recipes 
    JOIN chefs ON recipes.chef_id = chefs.chef_id 
    JOIN cuisines ON recipes.cuisine_id = cuisines.cuisine_id 
    ORDER BY recipes.id DESC";
$result = $conn->query($query);

if ($result === false) {
    die('Query failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
}
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
            max-width: 100%;
            height: auto;
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
            <p>No recipes found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

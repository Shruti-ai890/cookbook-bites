<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Get the search query from the URL
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($query) {
    // Prepare SQL queries to search in recipes and user_recipes
    $recipeQuery = "
        SELECT recipes.id AS recipe_id, recipes.recipe_name, cuisines.cuisine_name, recipes.recipe_image, 'recipe' AS source 
        FROM recipes 
        JOIN cuisines ON recipes.cuisine_id = cuisines.cuisine_id
        WHERE recipes.recipe_name LIKE ? 
        OR recipes.ingredients LIKE ? 
        OR cuisines.cuisine_name LIKE ?
    ";
    
    $userRecipeQuery = "
        SELECT ur.id AS recipe_id, ur.recipe_name, ci.cuisine_name, ur.recipe_image, 'user_recipe' AS source
        FROM user_recipes ur
        JOIN cuisines ci ON ur.cuisine_id = ci.cuisine_id
        WHERE ur.recipe_name LIKE ? 
        OR ur.ingredients LIKE ? 
        OR ci.cuisine_name LIKE ?
    ";

    // Prepare the recipes search statement
    $stmtRecipe = $conn->prepare($recipeQuery);
    if ($stmtRecipe === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
    $searchTerm = "%$query%";
    $stmtRecipe->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmtRecipe->execute();
    $resultRecipe = $stmtRecipe->get_result();

    // Prepare the user_recipes search statement
    $stmtUserRecipe = $conn->prepare($userRecipeQuery);
    if ($stmtUserRecipe === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
    $stmtUserRecipe->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmtUserRecipe->execute();
    $resultUserRecipe = $stmtUserRecipe->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        /* Add your CSS styles here */
        .recipe-item {
            margin-bottom: 20px;
        }
        .recipe-item img {
            width: 100px; /* Adjust the size as needed */
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <h2>Search Results</h2>
    <div class="recipe-gallery">
        <?php if ($query): ?>
            <?php if ($resultUserRecipe->num_rows > 0): ?>
                <!-- Display results from user_recipes table -->
                <?php while ($rowUserRecipe = $resultUserRecipe->fetch_assoc()): ?>
                    <div class="recipe-item">
                        <a href="user_recipe_detail.php?id=<?php echo htmlspecialchars($rowUserRecipe['recipe_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="uploads/<?php echo htmlspecialchars($rowUserRecipe['recipe_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($rowUserRecipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <h3><?php echo htmlspecialchars($rowUserRecipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($rowUserRecipe['cuisine_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <?php if ($resultRecipe->num_rows > 0): ?>
                <!-- Display results from recipes table -->
                <?php while ($rowRecipe = $resultRecipe->fetch_assoc()): ?>
                    <div class="recipe-item">
                        <a href="recipe_detail.php?id=<?php echo htmlspecialchars($rowRecipe['recipe_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <img src="uploads/<?php echo htmlspecialchars($rowRecipe['recipe_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($rowRecipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <h3><?php echo htmlspecialchars($rowRecipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($rowRecipe['cuisine_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php elseif ($resultUserRecipe->num_rows == 0 && $resultRecipe->num_rows == 0): ?>
                <p>No results found for "<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>


<?php
$stmtRecipe->close();
$stmtUserRecipe->close();
$conn->close();
?>

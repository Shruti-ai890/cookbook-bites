<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Get the user ID from the URL
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id > 0) {
    // Fetch recipes for the user along with the customer name and cuisine name
    $query = "
        SELECT recipes.*, customers.customer_name, cuisines.cuisine_name 
        FROM recipes 
        JOIN customers ON recipes.user_id = customers.customer_id 
        JOIN cuisines ON recipes.cuisine_id = cuisines.cuisine_id 
        WHERE recipes.user_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipes = $result->fetch_all(MYSQLI_ASSOC);
    
    // Check if any recipes were found
    if (!$recipes) {
        $message = "No recipes found for this user.";
    }
} else {
    $message = "Invalid user ID.";
    $recipes = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Recipes</title>
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

.recipe-list-container {
    background-color: #fff;
    padding: 2em;
    margin: 2em auto;
    max-width: 800px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    box-sizing: border-box; /* Ensure padding and border are included in the width */
}

.recipe-list-container h1 {
    margin-top: 0;
}

.recipe-list-item {
    border-bottom: 1px solid #ddd;
    padding: 1em 0;
}

.recipe-list-item img {
    max-width: 100px;
    height: auto;
    border-radius: 4px;
    margin-right: 1em;
    vertical-align: middle;
}

.recipe-list-item p {
    margin: 0.5em 0;
    font-size: 16px;
}

.recipe-list-item a {
    text-decoration: none;
    color: #ff6347;
}

@media (max-width: 768px) {
    .recipe-list-container {
        padding: 1em;
        margin: 1em;
    }

    .recipe-list-item {
        display: block;
        text-align: center;
    }

    .recipe-list-item img {
        max-width: 80%;
        margin-bottom: 1em;
    }

    .recipe-list-item p {
        font-size: 14px;
    }
}


    </style>
</head>
<body>
    <header>
        <h1>User Recipes</h1>
    </header>

    <div class="recipe-list-container">
        <?php if (!empty($recipes)): ?>
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-list-item">
                    <img src="uploads/<?php echo htmlspecialchars($recipe['recipe_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <h2><?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($recipe['customer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Servings:</strong> <?php echo htmlspecialchars($recipe['servings'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="recipe_detail.php?id=<?php echo htmlspecialchars($recipe['id'], ENT_QUOTES, 'UTF-8'); ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

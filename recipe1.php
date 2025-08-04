<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Initialize arrays to hold recipes
$recipes = [];
$user_recipes = [];
$meal_name = ''; // Variable to hold meal name

// Get the cuisine name from the URL
$cuisine_name = isset($_GET['cuisine']) ? mysqli_real_escape_string($conn, $_GET['cuisine']) : '';

// Get the meal ID from the URL
$meal_id = isset($_GET['meal_id']) ? intval($_GET['meal_id']) : 0;

if (!empty($cuisine_name)) {
    // Fetch recipes from the recipes table based on cuisine
    $recipes_query = "
        SELECT r.*, cu.cuisine_name
        FROM recipes r
        JOIN cuisines cu ON r.cuisine_id = cu.cuisine_id
        WHERE cu.cuisine_name = ?";
    $recipes_stmt = $conn->prepare($recipes_query);
    if ($recipes_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $recipes_stmt->bind_param("s", $cuisine_name);
    $recipes_stmt->execute();
    $recipes_result = $recipes_stmt->get_result();

    while ($row = $recipes_result->fetch_assoc()) {
        $recipes[] = $row;
    }

    $recipes_stmt->close();

    // Fetch recipes from the user_recipes table based on cuisine
    $user_recipes_query = "
        SELECT ur.*, cu.cuisine_name
        FROM user_recipes ur
        JOIN cuisines cu ON ur.cuisine_id = cu.cuisine_id
        WHERE cu.cuisine_name = ?";
    $user_recipes_stmt = $conn->prepare($user_recipes_query);
    if ($user_recipes_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $user_recipes_stmt->bind_param("s", $cuisine_name);
    $user_recipes_stmt->execute();
    $user_recipes_result = $user_recipes_stmt->get_result();

    while ($row = $user_recipes_result->fetch_assoc()) {
        $user_recipes[] = $row;
    }

    $user_recipes_stmt->close();
} elseif ($meal_id > 0) {
    // Fetch the meal name
    $meal_name_query = "SELECT meal_name FROM meals WHERE id = ?";
    $meal_name_stmt = $conn->prepare($meal_name_query);
    if ($meal_name_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $meal_name_stmt->bind_param("i", $meal_id);
    $meal_name_stmt->execute();
    $meal_name_result = $meal_name_stmt->get_result();
    
    if ($meal_name_result->num_rows > 0) {
        $meal_row = $meal_name_result->fetch_assoc();
        $meal_name = $meal_row['meal_name'];
    }
    
    $meal_name_stmt->close();

    // Fetch recipes from the recipes table based on meal
    $recipes_query = "
        SELECT r.*, m.meal_name
        FROM recipes r
        JOIN meals m ON r.meal_id = m.id
        WHERE m.id = ?";
    $recipes_stmt = $conn->prepare($recipes_query);
    if ($recipes_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $recipes_stmt->bind_param("i", $meal_id);
    $recipes_stmt->execute();
    $recipes_result = $recipes_stmt->get_result();

    while ($row = $recipes_result->fetch_assoc()) {
        $recipes[] = $row;
    }

    $recipes_stmt->close();

    // Fetch recipes from the user_recipes table based on meal
    $user_recipes_query = "
        SELECT ur.*, m.meal_name
        FROM user_recipes ur
        JOIN meals m ON ur.meal_id = m.id
        WHERE m.id = ?";
    $user_recipes_stmt = $conn->prepare($user_recipes_query);
    if ($user_recipes_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $user_recipes_stmt->bind_param("i", $meal_id);
    $user_recipes_stmt->execute();
    $user_recipes_result = $user_recipes_stmt->get_result();

    while ($row = $user_recipes_result->fetch_assoc()) {
        $user_recipes[] = $row;
    }

    $user_recipes_stmt->close();
} else {
    echo "Invalid filter criteria.";
    exit;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipes</title>
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

        .recipe-list {
            background-color: #fff;
            padding: 2em;
            margin: 2em auto;
            max-width: 800px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .recipe-list h2 {
            margin-top: 0;
        }

        .recipe-item {
            border-bottom: 1px solid #ddd;
            padding: 1em 0;
        }

        .recipe-item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 0.5em;
        }

        .recipe-item p {
            margin: 0.5em 0;
        }

        .recipe-item a {
            text-decoration: none;
            color: #ff6347;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .recipe-list {
                padding: 1em;
                margin: 1em;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Recipes</h1>
        <?php if (!empty($cuisine_name)): ?>
            <h2>Recipes for Cuisine: <?php echo htmlspecialchars($cuisine_name, ENT_QUOTES, 'UTF-8'); ?></h2>
        <?php elseif ($meal_id > 0): ?>
            <h2>Recipes for Meal: <?php echo htmlspecialchars($meal_name, ENT_QUOTES, 'UTF-8'); ?></h2>
        <?php else: ?>
            <h2>No specific filter selected.</h2>
        <?php endif; ?>
    </header>

    <div class="recipe-list">
        <?php 
        $combined_recipes = array_merge($recipes, $user_recipes);

        if (!empty($combined_recipes)): ?>
            <?php foreach ($combined_recipes as $recipe): ?>
                <div class="recipe-item">
                    <?php if (!empty($recipe['recipe_image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($recipe['recipe_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p><?php echo htmlspecialchars($recipe['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <a href="recipe_detail.php?id=<?php echo htmlspecialchars($recipe['id'], ENT_QUOTES, 'UTF-8'); ?>">View Recipe</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recipes found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

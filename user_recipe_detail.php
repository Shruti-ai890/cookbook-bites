<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Get the user recipe ID from the URL
$user_recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_recipe_id > 0) {
    // Fetch recipe details from the user_recipes table
    $query = "
        SELECT ur.*, c.name AS customer_name, cu.cuisine_name
        FROM user_recipes ur
        JOIN customer c ON ur.user_id = c.id
        JOIN cuisines cu ON ur.cuisine_id = cu.cuisine_id
        WHERE ur.id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $user_recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();

    // Fetch feedback for the user recipe
    $feedback_query = "
        SELECT f.feedback, c.name AS customer_name
        FROM feedback f
        JOIN customer c ON f.cust_id = c.id
        WHERE f.recipe_id = ?";
    $feedback_stmt = $conn->prepare($feedback_query);
    if ($feedback_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $feedback_stmt->bind_param("i", $user_recipe_id);
    $feedback_stmt->execute();
    $feedback_result = $feedback_stmt->get_result();
    $feedbacks = [];

    while ($row = $feedback_result->fetch_assoc()) {
        $feedbacks[] = $row;
    }

    $stmt->close();
    $feedback_stmt->close();
} else {
    echo "Invalid recipe ID.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Recipe Detail</title>
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

        .recipe-detail-container {
            background-color: #fff;
            padding: 2em;
            margin: 2em auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .recipe-detail-container h1 {
            margin-top: 0;
        }

        .recipe-detail-container img {
            max-width: 50%;
            height: 200px;
            border-radius: 8px;
            margin-bottom: 1em;
        }

        .recipe-detail-container pre {
            white-space: pre-wrap;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 1em;
            border-radius: 4px;
            border: 1px solid #ddd;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .recipe-detail-container p {
            margin: 0.5em 0;
            font-size: 16px;
        }

        .feedback {
            border-top: 1px solid #ddd;
            margin-top: 2em;
            padding-top: 1em;
        }

        .feedback-item {
            margin-bottom: 1em;
        }

        .feedback-item p {
            margin: 0.5em 0;
        }

        .feedback-item .customer-name {
            font-weight: bold;
        }

        .feedback-item .feedback-text {
            margin-left: 0.5em;
        }

        @media (max-width: 768px) {
            .recipe-detail-container {
                padding: 1em;
                margin: 1em;
            }

            .recipe-detail-container p, .recipe-detail-container pre {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($recipe['recipe_name'] ?? 'Recipe Details', ENT_QUOTES, 'UTF-8'); ?></h1>
    </header>

    <div class="recipe-detail-container">
        <?php if (isset($recipe)): ?>
            <img src="uploads/<?php echo htmlspecialchars($recipe['recipe_image'] ?? 'default.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name'] ?? 'Recipe Image', ENT_QUOTES, 'UTF-8'); ?>">
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($recipe['customer_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine_name'] ?? 'Not available', ENT_QUOTES, 'UTF-8'); ?></p>
            <pre><strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients'] ?? 'Not available', ENT_QUOTES, 'UTF-8'); ?></pre>
            <pre><strong>Steps:</strong> <?php echo htmlspecialchars($recipe['steps'] ?? 'Not available', ENT_QUOTES, 'UTF-8'); ?></pre>
            <p><strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time'] ?? 'Not available', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Servings:</strong> <?php echo htmlspecialchars($recipe['servings'] ?? 'Not available', ENT_QUOTES, 'UTF-8'); ?></p>

            <!-- Display Feedback -->
            <div class="feedback">
                <h3>Feedback:</h3>
                <?php if (empty($feedbacks)): ?>
                    <p>No feedback available for this recipe.</p>
                <?php else: ?>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="feedback-item">
                            <p class="customer-name"><?php echo htmlspecialchars($feedback['customer_name'], ENT_QUOTES, 'UTF-8'); ?>:</p>
                            <p class="feedback-text"><?php echo htmlspecialchars($feedback['feedback'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Recipe details could not be found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

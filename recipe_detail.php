<?php
include 'db_conn.php'; // Ensure this includes the correct database connection

// Get the recipe ID from the URL
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($recipe_id > 0) {
    // Fetch recipe details along with the chef's name and cuisine name
    $query = "
        SELECT recipes.*, chefs.chef_name, cuisines.cuisine_name 
        FROM recipes 
        JOIN chefs ON recipes.chef_id = chefs.chef_id 
        JOIN cuisines ON recipes.cuisine_id = cuisines.cuisine_id 
        WHERE recipes.id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recipe = $result->fetch_assoc();
    
    // Check if recipe was found
    if (!$recipe) {
        echo "Recipe not found.";
        exit;
    }

    // Fetch feedback for the recipe
    $feedback_query = "
        SELECT feedback.feedback, customer.name AS customer_name
        FROM feedback
        JOIN customer ON feedback.cust_id = customer.id
        WHERE feedback.recipe_id = ?
        ORDER BY feedback.datetime DESC";
    $feedback_stmt = $conn->prepare($feedback_query);
    if ($feedback_stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
    }
    $feedback_stmt->bind_param("i", $recipe_id);
    $feedback_stmt->execute();
    $feedback_result = $feedback_stmt->get_result();
    $feedbacks = $feedback_result->fetch_all(MYSQLI_ASSOC);

    // Handle feedback submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $feedback_text = isset($_POST['feedback_text']) ? trim($_POST['feedback_text']) : '';
        if (!empty($feedback_text)) {
            $insert_feedback_query = "
                INSERT INTO feedback (cust_id, recipe_id, feedback, datetime)
                VALUES (?, ?, ?, NOW())";
            $insert_feedback_stmt = $conn->prepare($insert_feedback_query);
            if ($insert_feedback_stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error)); // Show MySQL error
            }
            $customer_id = 1; // Replace with actual customer ID if logged in
            $insert_feedback_stmt->bind_param("iis", $customer_id, $recipe_id, $feedback_text);
            $insert_feedback_stmt->execute();
            if ($insert_feedback_stmt->affected_rows > 0) {
                echo '<p>Feedback submitted successfully!</p>';
                // Refresh feedback list
                $feedback_result = $feedback_stmt->get_result();
                $feedbacks = $feedback_result->fetch_all(MYSQLI_ASSOC);
            } else {
                echo '<p>Failed to submit feedback.</p>';
            }
        }
    }
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
    <title>Recipe Detail</title>
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
            box-sizing: border-box; /* Ensure padding and border are included in the width */
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
            white-space: pre-wrap; /* Ensures line breaks are preserved */
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 1em;
            border-radius: 4px;
            border: 1px solid #ddd;
            word-wrap: break-word; /* Allows long words to break and not overflow */
            overflow-wrap: break-word; /* Breaks words that are too long */
        }

        .recipe-detail-container p {
            margin: 0.5em 0;
            font-size: 16px;
        }

        .feedback {
            margin-top: 2em;
            padding-top: 1em;
            border-top: 1px solid #ddd;
        }

        .feedback-item {
            margin-bottom: 1em;
        }

        .feedback-item p {
            margin: 0;
        }

        .feedback-item .customer-name {
            font-weight: bold;
        }

        .feedback-item .feedback-text {
            margin-left: 1em;
        }

        .feedback-form textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .feedback-form button {
            background-color: #ff6347;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .feedback-form button:hover {
            background-color: #e55340;
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
        <h1><?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
    </header>

    <div class="recipe-detail-container">
        <?php if (isset($recipe)): ?>
            <img src="uploads/<?php echo htmlspecialchars($recipe['recipe_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($recipe['recipe_name'], ENT_QUOTES, 'UTF-8'); ?>">
            <p><strong>Chef:</strong> <?php echo htmlspecialchars($recipe['chef_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine_name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <pre><strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients'], ENT_QUOTES, 'UTF-8'); ?></pre>
            <pre><strong>Steps:</strong> <?php echo htmlspecialchars($recipe['steps'], ENT_QUOTES, 'UTF-8'); ?></pre>
            <p><strong>Cooking Time:</strong> <?php echo htmlspecialchars($recipe['cooking_time'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Servings:</strong> <?php echo htmlspecialchars($recipe['servings'], ENT_QUOTES, 'UTF-8'); ?></p>

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

                
        <?php else: ?>
            <p>Recipe details could not be found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

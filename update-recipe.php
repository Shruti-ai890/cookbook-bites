<?php
include 'db_conn.php'; // Include your database connection file



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = intval($_POST['id']);
    $recipe_name = $_POST['recipe_name'];
    $chef_id = intval($_POST['chef_id']);
    $meal_id = intval($_POST['meal_id']);
    $ingredient_id = intval($_POST['ingredient_id']);
    $cuisine_id = intval($_POST['cuisine_id']);
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];
    $servings = intval($_POST['servings']);
    $cooking_time = $_POST['cooking_time'];
    
    // Handle file upload
    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['recipe_image']['tmp_name'];
        $fileName = $_FILES['recipe_image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $recipe_image = $newFileName;
        } else {
            $recipe_image = $_POST['existing_image']; // Keep existing image if upload fails
        }
    } else {
        $recipe_image = $_POST['existing_image']; // Keep existing image if no new image is uploaded
    }

    // Update recipe details
    $query = "UPDATE recipes SET recipe_name = ?, chef_id = ?, meal_id = ?, ingredient_id = ?, cuisine_id = ?, ingredients = ?, steps = ?, recipe_image = ?, servings = ?, cooking_time = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siisisssii", $recipe_name, $chef_id, $meal_id, $ingredient_id, $cuisine_id, $ingredients, $steps, $recipe_image, $servings, $cooking_time, $recipe_id);
    $stmt->execute();

    header('Location: admin_dashboard.php'); // Redirect to a page listing recipes or any other page
    exit;
}


?>

<?php
session_start();
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle file upload
if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['recipe_image']['tmp_name'];
    $fileName = $_FILES['recipe_image']['name'];
    $fileSize = $_FILES['recipe_image']['size'];
    $fileType = $_FILES['recipe_image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $uploadFileDir = './uploads/';
    $dest_path = $uploadFileDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $dest_path)) {
        // File uploaded successfully
    } else {
        die('Error uploading the file.');
    }
} else {
    die('No file uploaded or there was an upload error.');
}

// Prepare SQL query
$sql = "INSERT INTO user_recipes (user_id, recipe_name, meal_id, cuisine_id, ingredients, steps, recipe_image, servings, cooking_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

// Bind parameters
$recipe_name = $_POST['recipe_name'];
$meal_id = $_POST['meal_id'];
$cuisine_id = $_POST['cuisine_id'];
$ingredients = $_POST['ingredients'];
$steps = $_POST['steps'];
$recipe_image = $newFileName; // Image file name
$servings = $_POST['servings'];
$cooking_time = $_POST['cooking_time'];

$stmt->bind_param('issssssii', $user_id, $recipe_name, $meal_id, $cuisine_id, $ingredients, $steps, $recipe_image, $servings, $cooking_time);

// Execute and check result
if ($stmt->execute()) {
    echo "Recipe added successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Execute and check result
if ($stmt->execute()) {
    // Redirect to user_dashboard.php after successful insertion
    header("Location: user_dashboard.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

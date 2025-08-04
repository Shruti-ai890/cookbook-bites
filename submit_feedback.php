<?php
session_start();
include 'db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Retrieve and sanitize input values
$cust_id = $_SESSION['user_id'];
$recipe_id = mysqli_real_escape_string($conn, $_POST['recipe_id']);
$feedback = mysqli_real_escape_string($conn, $_POST['feedback']);

// Fetch recipe_name based on recipe_id
$recipe_query = "SELECT recipe_name FROM recipes WHERE id = ?";
$recipe_stmt = $conn->prepare($recipe_query);
if ($recipe_stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$recipe_stmt->bind_param('i', $recipe_id);
$recipe_stmt->execute();
$recipe_result = $recipe_stmt->get_result();

if ($recipe_result->num_rows > 0) {
    $recipe_row = $recipe_result->fetch_assoc();
    $recipe_name = $recipe_row['recipe_name'];
} else {
    die('Invalid recipe ID.');
}

$recipe_stmt->close();

// Get current datetime
$datetime = date('Y-m-d H:i:s');

// Insert feedback into the database
$sql = "INSERT INTO feedback (cust_id, recipe_id, recipe_name, feedback, datetime) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param('iisss', $cust_id, $recipe_id, $recipe_name, $feedback, $datetime);

if ($stmt->execute()) {
    echo "Feedback submitted successfully.";
    header("Location: user_dashboard.php");
    exit();
} else {
    echo "Error: " . htmlspecialchars($stmt->error);
}

$stmt->close();
$conn->close();
?>

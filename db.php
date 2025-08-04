<?php
// Database configuration
$host = 'localhost'; // Database host
$dbname = 'recipe'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $pdo->prepare('SELECT password FROM admin WHERE username = :username');
    $stmt->execute(['username' => $username]);
    $storedPasswordHash = $stmt->fetchColumn();

    // Verify password
    if (password_verify($password, $storedPasswordHash)) {
        echo "Login successful!";
        // Redirect to admin area or dashboard
        // header('Location: admin_dashboard.php');
    } else {
        echo "Invalid username or password.";
    }
}
?>

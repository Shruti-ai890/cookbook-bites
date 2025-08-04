<?php
// Example of hashing a password
$password = '12345'; // Replace with the password you want to hash
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Output the hashed password to store in the database
echo $hashedPassword;
?>

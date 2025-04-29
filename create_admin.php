<?php
include 'db_connect.php'; 

$username = 'admin';
$password = 'Ambience@1234';

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert admin into DB
$sql = "INSERT INTO admins (username, password) VALUES (:username, :password)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':username' => $username,
    ':password' => $hashedPassword
]);

echo "Admin created with hashed password.";

<?php
include 'db_connect.php';

$username = 'admin';
$plainPassword = 'Ambience@1234';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username, $hashedPassword]);

echo "Admin user created.";
?>
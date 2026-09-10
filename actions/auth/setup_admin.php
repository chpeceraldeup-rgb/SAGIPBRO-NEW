<?php

require_once "../../config/database.php";

$full_name = "SAGIPBRO Administrator";
$username = "christian";
$password = "ceralde";

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users
        (full_name, username, password_hash, role, status)
        VALUES
        (:full_name, :username, :password_hash, 'admin', 'Active')";

$stmt = $conn->prepare($sql);

$stmt->execute([
    ":full_name" => $full_name,
    ":username" => $username,
    ":password_hash" => $password_hash
]);

echo "Admin account created successfully.";

?>
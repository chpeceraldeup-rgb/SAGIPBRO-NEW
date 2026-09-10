<?php

require_once __DIR__ . "/../../config/database.php";

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Run this setup script from the command line.");
}

$full_name = getenv('SAGIPBRO_ADMIN_NAME') ?: 'SAGIPBRO Administrator';
$username = getenv('SAGIPBRO_ADMIN_USERNAME');
$password = getenv('SAGIPBRO_ADMIN_PASSWORD');
if (!$username || !$password || strlen($password) < 3) {
    exit("Set SAGIPBRO_ADMIN_USERNAME and a 3-character SAGIPBRO_ADMIN_PASSWORD first.\n");
}

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
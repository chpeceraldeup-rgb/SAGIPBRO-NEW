<?php

session_start();

require_once "../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../login.php");
    exit;
}

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    header("Location: ../../login.php?error=Please fill in all fields");
    exit;
}

$sql = "SELECT * FROM users WHERE username = :username AND status = 'Active' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ":username" => $username
]);

$user = $stmt->fetch();

if ($user && password_verify($password, $user["password_hash"])) {

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["full_name"] = $user["full_name"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    header("Location: ../../dashboard/admin.php");
    exit;
}

header("Location: ../../login.php?error=Invalid username or password");
exit;
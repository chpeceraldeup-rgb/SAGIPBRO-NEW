<?php

require_once '../../config/database.php';
require_once '../../config/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	header('Location: ../../register.php');
	exit;
}

verifyCsrf();

$fullName = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
if ($fullName === '' || $username === '' || strlen($password) < 8) {
	header('Location: ../../register.php?error=Name, username, and an 8-character password are required');
	exit;
}
if (!hash_equals($password, $confirmPassword)) {
	header('Location: ../../register.php?error=Passwords do not match');
	exit;
}

$stmt = $conn->prepare('INSERT INTO users (full_name, username, password_hash, role) VALUES (?, ?, ?, \'resident\')');
try {
	$stmt->execute([$fullName, $username, password_hash($password, PASSWORD_DEFAULT)]);
	header('Location: ../../login.php?success=Registration complete');
} catch (PDOException $e) {
	header('Location: ../../register.php?error=Username is already in use');
}
exit;

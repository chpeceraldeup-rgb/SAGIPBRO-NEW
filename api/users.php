<?php

require_once __DIR__ . '/bootstrap.php';
requireApiLogin(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->query('SELECT id, full_name, username, role, status, created_at FROM users ORDER BY full_name');
    jsonResponse(['data' => $stmt->fetchAll()]);
}

$data = requestData();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = requiredString($data, 'full_name', 150);
    $username = requiredString($data, 'username', 80);
    $password = (string) ($data['password'] ?? '');
    $role = $data['role'] ?? 'resident';
    if (strlen($password) < 8 || !in_array($role, ['admin', 'official', 'volunteer', 'resident'], true)) {
        jsonResponse(['error' => 'Invalid password or role.'], 422);
    }
    try {
        $stmt = $conn->prepare('INSERT INTO users (full_name, username, password_hash, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$fullName, $username, password_hash($password, PASSWORD_DEFAULT), $role]);
    } catch (PDOException $e) {
        jsonResponse(['error' => 'Username is already in use.'], 409);
    }
    $id = (int) $conn->lastInsertId();
    logActivity($conn, 'create', 'user', $id, ['role' => $role]);
    jsonResponse(['id' => $id, 'message' => 'User created.'], 201);
}

$id = positiveInt($data, 'id');
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $role = $data['role'] ?? null;
    $status = $data['status'] ?? null;
    if (!in_array($role, ['admin', 'official', 'volunteer', 'resident'], true) || !in_array($status, ['Active', 'Inactive'], true)) {
        jsonResponse(['error' => 'Invalid role or status.'], 422);
    }
    $stmt = $conn->prepare('UPDATE users SET role = ?, status = ? WHERE id = ?');
    $stmt->execute([$role, $status, $id]);
    logActivity($conn, 'update', 'user', $id, ['role' => $role, 'status' => $status]);
    jsonResponse(['message' => 'User updated.']);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $conn->prepare("UPDATE users SET status = 'Inactive' WHERE id = ?");
    $stmt->execute([$id]);
    logActivity($conn, 'delete', 'user', $id);
    jsonResponse(['message' => 'User deactivated.']);
}

methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);

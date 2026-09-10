<?php

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	requireApiLogin();
	$stmt = $conn->query('SELECT a.*, a.message AS body, a.posted_by AS created_by, u.full_name AS author FROM announcements a JOIN users u ON u.id = a.posted_by ORDER BY a.created_at DESC');
	jsonResponse(['data' => $stmt->fetchAll()]);
}

requireApiLogin(['admin', 'official']);
$data = requestData();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$title = requiredString($data, 'title', 180);
	$body = requiredString($data, 'body', 10000);
	$status = in_array($data['status'] ?? 'Draft', ['Draft', 'Published'], true) ? $data['status'] : 'Draft';
	$publishedAt = $status === 'Published' ? date('Y-m-d H:i:s') : null;
	$stmt = $conn->prepare('INSERT INTO announcements (title, message, status, posted_by, priority) VALUES (?, ?, ?, ?, ?)');
	$stmt->execute([$title, $body, $status, currentUserId(), $data['priority'] ?? 'Normal']);
	$id = (int) $conn->lastInsertId();
	logActivity($conn, $status === 'Published' ? 'publish' : 'create', 'announcement', $id);
	jsonResponse(['id' => $id, 'message' => 'Announcement created.'], 201);
}

$id = positiveInt($data, 'id');
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	$title = requiredString($data, 'title', 180);
	$body = requiredString($data, 'body', 10000);
	$stmt = $conn->prepare('UPDATE announcements SET title = ?, message = ? WHERE id = ? AND status <> \'Archived\'');
	$stmt->execute([$title, $body, $id]);
	if (!$stmt->rowCount()) {
		jsonResponse(['error' => 'Announcement not found or archived.'], 404);
	}
	logActivity($conn, 'update', 'announcement', $id);
	jsonResponse(['message' => 'Announcement updated.']);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	$stmt = $conn->prepare("UPDATE announcements SET status = 'Archived' WHERE id = ?");
	$stmt->execute([$id]);
	if (!$stmt->rowCount()) {
		jsonResponse(['error' => 'Announcement not found.'], 404);
	}
	logActivity($conn, 'archive', 'announcement', $id);
	jsonResponse(['message' => 'Announcement archived.']);
}

methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);

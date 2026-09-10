<?php

require_once __DIR__ . '/bootstrap.php';
requireApiLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$stmt = $conn->query(
		'SELECT d.*, r.name AS resource_name, u.full_name AS distributed_by_name
		 FROM distributions d JOIN resources r ON r.id = d.resource_id
		 JOIN users u ON u.id = d.distributed_by ORDER BY d.distributed_at DESC'
	);
	jsonResponse(['data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	methodNotAllowed(['GET', 'POST']);
}

requireApiLogin(['admin', 'official', 'volunteer']);
$data = requestData();
$resourceId = positiveInt($data, 'resource_id');
$quantity = positiveInt($data, 'quantity');
$recipientName = requiredString($data, 'recipient_name', 150);

$conn->beginTransaction();
try {
	$stockStmt = $conn->prepare('SELECT stock FROM resources WHERE id = ? AND status = \'Available\' FOR UPDATE');
	$stockStmt->execute([$resourceId]);
	$resource = $stockStmt->fetch();
	if (!$resource) {
		throw new RuntimeException('Resource not found.');
	}
	if ((int) $resource['stock'] < $quantity) {
		throw new RuntimeException('Insufficient stock.');
	}

	$residentId = !empty($data['recipient_resident_id']) ? positiveInt($data, 'recipient_resident_id') : null;
	$insert = $conn->prepare(
		'INSERT INTO distributions (resource_id, recipient_resident_id, recipient_name, quantity, distributed_by)
		 VALUES (?, ?, ?, ?, ?)'
	);
	$insert->execute([$resourceId, $residentId, $recipientName, $quantity, currentUserId()]);
	$update = $conn->prepare('UPDATE resources SET stock = stock - ? WHERE id = ?');
	$update->execute([$quantity, $resourceId]);
	$id = (int) $conn->lastInsertId();
	logActivity($conn, 'create', 'distribution', $id, ['resource_id' => $resourceId, 'quantity' => $quantity]);
	$conn->commit();
	jsonResponse(['id' => $id, 'message' => 'Distribution recorded and stock deducted.'], 201);
} catch (Throwable $e) {
	$conn->rollBack();
	jsonResponse(['error' => $e->getMessage()], 422);
}

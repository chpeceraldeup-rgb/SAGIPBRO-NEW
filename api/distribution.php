<?php

require_once __DIR__ . '/bootstrap.php';
requireApiLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$stmt = $conn->query(
		' SELECT d.*, r.resource_name, d.distribution_date AS distributed_at, u.full_name AS distributed_by_name
		 FROM distributions d JOIN resources r ON r.id = d.resource_id
		 JOIN users u ON u.id = d.distributed_by ORDER BY d.distribution_date DESC'
	);
	jsonResponse(['data' => $stmt->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'DELETE'], true)) methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
	requireApiLogin(['admin', 'official', 'volunteer']);
	$data = requestData();
	$id = positiveInt($data, 'id');
	$conn->beginTransaction();
	try {
		$existing = $conn->prepare('SELECT resource_id, quantity FROM distributions WHERE id = ? FOR UPDATE');
		$existing->execute([$id]);
		$record = $existing->fetch();
		if (!$record) throw new RuntimeException('Distribution not found.');
		if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			$conn->prepare('UPDATE resources SET quantity = quantity + ? WHERE id = ?')->execute([(int) $record['quantity'], (int) $record['resource_id']]);
			$conn->prepare('DELETE FROM distributions WHERE id = ?')->execute([$id]);
			logActivity($conn, 'delete', 'distribution', $id);
		} else {
			$quantity = positiveInt($data, 'quantity');
			$recipientName = requiredString($data, 'recipient_name', 150);
			$resourceId = positiveInt($data, 'resource_id');
			$delta = $quantity - (int) $record['quantity'];
			if ($resourceId !== (int) $record['resource_id']) {
				$conn->prepare('UPDATE resources SET quantity = quantity + ? WHERE id = ?')->execute([(int) $record['quantity'], (int) $record['resource_id']]);
				$stockUpdate = $conn->prepare('UPDATE resources SET quantity = quantity - ? WHERE id = ? AND quantity >= ?');
				$stockUpdate->execute([$quantity, $resourceId, $quantity]);
			} else {
				$stockUpdate = $conn->prepare('UPDATE resources SET quantity = quantity - ? WHERE id = ? AND quantity >= ?');
				$stockUpdate->execute([max(0, $delta), $resourceId, max(0, $delta)]);
			}
			if (!$stockUpdate->rowCount() && $delta > 0) throw new RuntimeException('Insufficient stock.');
			$stmt = $conn->prepare('UPDATE distributions SET resource_id = ?, household_id = ?, recipient_name = ?, quantity = ?, distribution_date = ?, remarks = ? WHERE id = ?');
			$stmt->execute([$resourceId, $data['household_id'] ?? null, $recipientName, $quantity, $data['distribution_date'] ?? date('Y-m-d H:i:s'), $data['remarks'] ?? null, $id]);
			logActivity($conn, 'update', 'distribution', $id);
		}
		$conn->commit();
		jsonResponse(['message' => $_SERVER['REQUEST_METHOD'] === 'DELETE' ? 'Distribution deleted and stock restored.' : 'Distribution updated.']);
	} catch (Throwable $e) {
		if ($conn->inTransaction()) $conn->rollBack();
		jsonResponse(['error' => $e->getMessage()], 422);
	}
}

requireApiLogin(['admin', 'official', 'volunteer']);
$data = requestData();
$resourceId = positiveInt($data, 'resource_id');
$quantity = positiveInt($data, 'quantity');
$recipientName = requiredString($data, 'recipient_name', 150);

$conn->beginTransaction();
try {
	$stockStmt = $conn->prepare('SELECT quantity AS stock FROM resources WHERE id = ? AND status <> \'Inactive\' FOR UPDATE');
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
		'INSERT INTO distributions (resource_id, household_id, recipient_name, quantity, distributed_by, distribution_date)
		 VALUES (?, ?, ?, ?, ?, NOW())'
	);
	$insert->execute([$resourceId, $data['household_id'] ?? null, $recipientName, $quantity, currentUserId()]);
	$update = $conn->prepare('UPDATE resources SET quantity = quantity - ? WHERE id = ?');
	$update->execute([$quantity, $resourceId]);
	$id = (int) $conn->lastInsertId();
	logActivity($conn, 'create', 'distribution', $id, ['resource_id' => $resourceId, 'quantity' => $quantity]);
	$conn->commit();
	jsonResponse(['id' => $id, 'message' => 'Distribution recorded and stock deducted.'], 201);
} catch (Throwable $e) {
	$conn->rollBack();
	jsonResponse(['error' => $e->getMessage()], 422);
}

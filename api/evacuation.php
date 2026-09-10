<?php

require_once __DIR__ . '/bootstrap.php';
requireApiLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$stmt = $conn->query("SELECT e.*, GREATEST(e.capacity - e.occupants, 0) AS available_capacity,
		CASE WHEN e.status = 'Closed' THEN 'Closed' WHEN e.occupants >= e.capacity THEN 'Full' ELSE 'Open' END AS availability
		FROM evacuation_centers e ORDER BY e.name");
	jsonResponse(['data' => $stmt->fetchAll()]);
}

requireApiLogin(['admin', 'official', 'volunteer']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$data = requestData();
	$centerId = positiveInt($data, 'center_id');
	$name = requiredString($data, 'name', 150);
	$contact = $data['contact_no'] ?? null;
	$conn->beginTransaction();
	try {
		$centerStmt = $conn->prepare("SELECT capacity, occupants FROM evacuation_centers WHERE id = ? AND status = 'Open' FOR UPDATE");
		$centerStmt->execute([$centerId]);
		$center = $centerStmt->fetch();
		if (!$center || (int) $center['occupants'] >= (int) $center['capacity']) {
			throw new RuntimeException('Evacuation center is closed or full.');
		}
		$residentId = !empty($data['resident_id']) ? positiveInt($data, 'resident_id') : null;
		$stmt = $conn->prepare('INSERT INTO evacuees (resident_id, center_id, name, contact_no) VALUES (?, ?, ?, ?)');
		$stmt->execute([$residentId, $centerId, $name, $contact]);
		$conn->prepare('UPDATE evacuation_centers SET occupants = occupants + 1 WHERE id = ?')->execute([$centerId]);
		$id = (int) $conn->lastInsertId();
		logActivity($conn, 'check-in', 'evacuee', $id, ['center_id' => $centerId]);
		$conn->commit();
		jsonResponse(['id' => $id, 'message' => 'Evacuee checked in.'], 201);
	} catch (Throwable $e) {
		$conn->rollBack();
		jsonResponse(['error' => $e->getMessage()], 422);
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	$data = requestData();
	$id = positiveInt($data, 'id');
	$conn->beginTransaction();
	try {
		$stmt = $conn->prepare('UPDATE evacuees SET checked_out_at = NOW() WHERE id = ? AND checked_out_at IS NULL');
		$stmt->execute([$id]);
		if (!$stmt->rowCount()) {
			throw new RuntimeException('Active evacuee record not found.');
		}
		$centerStmt = $conn->prepare('SELECT center_id FROM evacuees WHERE id = ?');
		$centerStmt->execute([$id]);
		$centerId = (int) $centerStmt->fetchColumn();
		$conn->prepare('UPDATE evacuation_centers SET occupants = GREATEST(occupants - 1, 0) WHERE id = ?')->execute([$centerId]);
		logActivity($conn, 'check-out', 'evacuee', $id);
		$conn->commit();
		jsonResponse(['message' => 'Evacuee checked out.']);
	} catch (Throwable $e) {
		$conn->rollBack();
		jsonResponse(['error' => $e->getMessage()], 422);
	}
}

methodNotAllowed(['GET', 'POST', 'PUT']);

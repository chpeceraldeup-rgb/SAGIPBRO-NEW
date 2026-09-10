<?php

require_once __DIR__ . '/bootstrap.php';
requireApiLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$stmt = $conn->query('SELECT r.*, h.household_no, h.address, h.barangay FROM residents r LEFT JOIN households h ON h.id = r.household_id WHERE r.status = \'Active\' ORDER BY r.last_name, r.first_name');
	jsonResponse(['data' => $stmt->fetchAll()]);
}

requireApiLogin(['admin', 'official']);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	methodNotAllowed(['GET', 'POST']);
}
$data = requestData();
$firstName = requiredString($data, 'first_name', 80);
$lastName = requiredString($data, 'last_name', 80);
$sex = $data['sex'] ?? '';
if (!in_array($sex, ['Male', 'Female', 'Other'], true)) {
	jsonResponse(['error' => 'Invalid sex.'], 422);
}
$householdId = !empty($data['household_id']) ? positiveInt($data, 'household_id') : null;
$stmt = $conn->prepare('INSERT INTO residents (household_id, first_name, last_name, sex, birth_date, contact_no, vulnerability) VALUES (?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$householdId, $firstName, $lastName, $sex, $data['birth_date'] ?? null, $data['contact_no'] ?? null, $data['vulnerability'] ?? null]);
$id = (int) $conn->lastInsertId();
logActivity($conn, 'create', 'resident', $id);
jsonResponse(['id' => $id, 'message' => 'Resident registered.'], 201);

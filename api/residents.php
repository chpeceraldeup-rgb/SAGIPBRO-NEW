<?php
require_once __DIR__ . '/bootstrap.php';
requireApiLogin();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$stmt = $conn->query(
	"SELECT r.*, h.household_code, h.address AS household_address, h.purok
		 FROM residents r LEFT JOIN households h ON h.id = r.household_id
		 ORDER BY r.last_name, r.first_name"
	);
	$residents = $stmt->fetchAll();
	foreach ($residents as &$resident) {
	$resident['sex'] = $resident['gender'];
	$resident['contact_no'] = $resident['contact_number'];
	$resident['vulnerability'] = $resident['vulnerable_group'];
	$resident['household_no'] = $resident['household_code'];
	$resident['address_display'] = $resident['address'] ?: ($resident['household_address'] ?? '');
	}
	unset($resident);
	jsonResponse(['data' => $residents]);
}
requireApiLogin(['admin', 'official']);
$data = requestData();
$resolveHouseholdId = static function ($value) use ($conn): ?int {
	if ($value === null || $value === '') {
	return null;
	}
	if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false) {
	return (int) $value;
	}
	$stmt = $conn->prepare('SELECT id FROM households WHERE household_code = ?');
	$stmt->execute([trim((string) $value)]);
	$id = $stmt->fetchColumn();
	return $id === false ? null : (int) $id;
};
$parseName = static function (string $fullName): array {
	$parts = preg_split('/\s+/', trim($fullName), 2);
	return [$parts[0] ?? '', $parts[1] ?? ''];
};
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$firstName = requiredString($data, 'first_name', 100);
	$lastName = requiredString($data, 'last_name', 100);
	$gender = $data['sex'] ?? $data['gender'] ?? '';
	if ($gender === 'Prefer not to say') {
	$gender = 'Other';
	}
	if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
	jsonResponse(['error' => 'Invalid sex.'], 422);
	}
	$householdId = $resolveHouseholdId($data['household_id'] ?? null);
	if (!empty($data['household_id']) && $householdId === null) {
	jsonResponse(['error' => 'Household not found.'], 422);
	}
	$stmt = $conn->prepare(
	'INSERT INTO residents (household_id, first_name, last_name, birth_date, gender, contact_number, address, vulnerable_group, status)
		 VALUES (?, ?, ?, ?, ?, ?, ?, ?, \'Active\')'
	);
	$stmt->execute([
	$householdId,
	$firstName,
	$lastName,
	$data['birth_date'] ?: null,
	$gender,
	$data['contact'] ?? $data['contact_number'] ?? null,
	$data['address'] ?? null,
	$data['priority_group'] ?? $data['vulnerable_group'] ?? 'None'
	]);
	$id = (int) $conn->lastInsertId();
	logActivity($conn, 'create', 'resident', $id);
	jsonResponse(['id' => $id, 'message' => 'Resident registered.'], 201);
}
$id = positiveInt($data, 'id');
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	if (!empty($data['full_name'])) {
	[$firstName, $lastName] = $parseName(requiredString($data, 'full_name', 200));
	} else {
	$firstName = requiredString($data, 'first_name', 100);
	$lastName = requiredString($data, 'last_name', 100);
	}
	$status = $data['status'] ?? 'Active';
	if (!in_array($status, ['Active', 'Inactive'], true)) {
	jsonResponse(['error' => 'Invalid status.'], 422);
	}
	$stmt = $conn->prepare(
	'UPDATE residents SET first_name = ?, last_name = ?, contact_number = ?, vulnerable_group = ?, status = ? WHERE id = ?'
	);
	$stmt->execute([
	$firstName,
	$lastName,
	$data['contact'] ?? $data['contact_number'] ?? null,
	$data['priority_group'] ?? $data['vulnerable_group'] ?? 'None',
	$status,
	$id
	]);
	if (!$stmt->rowCount()) {
	jsonResponse(['error' => 'Resident not found or unchanged.'], 404);
	}
	logActivity($conn, 'update', 'resident', $id);
	jsonResponse(['message' => 'Resident updated.']);
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	$stmt = $conn->prepare("UPDATE residents SET status = 'Inactive' WHERE id = ? AND status <> 'Inactive'");
	$stmt->execute([$id]);
	if (!$stmt->rowCount()) {
	jsonResponse(['error' => 'Resident not found.'], 404);
	}
	logActivity($conn, 'delete', 'resident', $id);
	jsonResponse(['message' => 'Resident deactivated.']);
}
methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);

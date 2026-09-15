<?php
require_once __DIR__ . '/bootstrap.php';
requireApiLogin();

$centerColumns = $conn->query('SHOW COLUMNS FROM evacuation_centers')->fetchAll(PDO::FETCH_COLUMN);
$nameColumn = in_array('center_name', $centerColumns, true) ? 'center_name' : 'name';
$locationColumn = in_array('location', $centerColumns, true) ? 'location' : 'address';
$occupantsColumn = in_array('current_occupants', $centerColumns, true) ? 'current_occupants' : 'occupants';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->query("SELECT id, {$nameColumn} AS name, {$locationColumn} AS location, capacity,
        {$occupantsColumn} AS occupants, COALESCE(contact_person, '') AS contact,
        COALESCE(contact_number, '') AS phone, COALESCE(notes, '') AS notes, status, updated_at
        FROM evacuation_centers ORDER BY {$nameColumn}");
    jsonResponse(['data' => $stmt->fetchAll()]);
}

requireApiLogin(['admin', 'official']);
$data = requestData();
$validStatus = ['Open', 'Closed'];

$centerValues = static function (array $input) use ($validStatus): array {
    $capacity = filter_var($input['capacity'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $occupants = filter_var($input['occupants'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($capacity === false || $occupants === false || $occupants > $capacity) {
        jsonResponse(['error' => 'Current occupants cannot exceed the center capacity.'], 422);
    }
    $notes = trim((string) ($input['notes'] ?? ''));
    if (strlen($notes) > 2000) {
        jsonResponse(['error' => 'Notes must not exceed 2,000 characters.'], 422);
    }
    return [
        requiredString($input, 'name', 150),
        requiredString($input, 'location', 255),
        $capacity,
        $occupants,
        requiredString($input, 'contact_person', 150),
        requiredString($input, 'contact_number', 30),
        $notes === '' ? null : $notes,
        in_array($input['status'] ?? 'Open', $validStatus, true) ? $input['status'] : 'Open',
    ];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$name, $location, $capacity, $occupants, $contactPerson, $contactNumber, $notes, $status] = $centerValues($data);
    $stmt = $conn->prepare("INSERT INTO evacuation_centers
        ({$nameColumn}, {$locationColumn}, capacity, {$occupantsColumn}, contact_person, contact_number, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $location, $capacity, $occupants, $contactPerson, $contactNumber, $notes, $status]);
    $id = (int) $conn->lastInsertId();
    logActivity($conn, 'create', 'evacuation_center', $id, ['name' => $name]);
    jsonResponse(['id' => $id, 'message' => 'Evacuation center created.'], 201);
}

$id = positiveInt($data, 'id');
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    [$name, $location, $capacity, $occupants, $contactPerson, $contactNumber, $notes, $status] = $centerValues($data);
    $exists = $conn->prepare('SELECT id FROM evacuation_centers WHERE id = ?');
    $exists->execute([$id]);
    if (!$exists->fetchColumn()) jsonResponse(['error' => 'Center not found.'], 404);
    $stmt = $conn->prepare("UPDATE evacuation_centers SET {$nameColumn} = ?, {$locationColumn} = ?,
        capacity = ?, {$occupantsColumn} = ?, contact_person = ?, contact_number = ?, notes = ?, status = ? WHERE id = ?");
    $stmt->execute([$name, $location, $capacity, $occupants, $contactPerson, $contactNumber, $notes, $status, $id]);
    logActivity($conn, 'update', 'evacuation_center', $id);
    jsonResponse(['message' => 'Evacuation center updated.']);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $conn->prepare("UPDATE evacuation_centers SET status = 'Closed' WHERE id = ? AND status <> 'Closed'");
    $stmt->execute([$id]);
    if (!$stmt->rowCount()) jsonResponse(['error' => 'Center not found or already closed.'], 404);
    logActivity($conn, 'delete', 'evacuation_center', $id);
    jsonResponse(['message' => 'Evacuation center closed.']);
}
methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);

<?php

require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	requireApiLogin();
	$stmt = $conn->query(
		"SELECT id, resource_name AS name, category, unit, quantity AS stock,
				minimum_stock AS low_stock_threshold, location, status, created_at, updated_at,
				CASE WHEN quantity = 0 THEN 'Out of stock'
					 WHEN quantity <= minimum_stock THEN 'Low stock'
					 ELSE 'In stock' END AS stock_status
		 FROM resources WHERE status <> 'Inactive' ORDER BY resource_name"
	);
	jsonResponse(['data' => $stmt->fetchAll()]);
}

requireApiLogin(['admin', 'official']);
$data = requestData();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = requiredString($data, 'name', 120);
	$category = requiredString($data, 'category', 80);
	$unit = requiredString($data, 'unit', 30);
	$stock = filter_var($data['stock'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
	$threshold = filter_var($data['low_stock_threshold'] ?? 10, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
	if ($stock === false || $threshold === false) {
		jsonResponse(['error' => 'Stock values must be non-negative integers.'], 422);
	}
	$stmt = $conn->prepare('INSERT INTO resources (resource_name, category, unit, quantity, minimum_stock, location, status) VALUES (?, ?, ?, ?, ?, ?, \'Available\')');
	$stmt->execute([$name, $category, $unit, $stock, $threshold, $data['location'] ?? null]);
	$id = (int) $conn->lastInsertId();
	logActivity($conn, 'create', 'resource', $id, ['name' => $name]);
	jsonResponse(['id' => $id, 'message' => 'Resource created.'], 201);
}

$id = positiveInt($data, 'id');
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
	$name = requiredString($data, 'name', 120);
	$category = requiredString($data, 'category', 80);
	$unit = requiredString($data, 'unit', 30);
	$stock = filter_var($data['stock'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
	$threshold = filter_var($data['low_stock_threshold'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
	if ($stock === false || $threshold === false) {
		jsonResponse(['error' => 'Stock values must be non-negative integers.'], 422);
	}
	$stmt = $conn->prepare('UPDATE resources SET resource_name = ?, category = ?, unit = ?, quantity = ?, minimum_stock = ?, location = ? WHERE id = ? AND status <> \'Inactive\'');
	$stmt->execute([$name, $category, $unit, $stock, $threshold, $data['location'] ?? null, $id]);
	if (!$stmt->rowCount()) {
		jsonResponse(['error' => 'Resource not found or unchanged.'], 404);
	}
	logActivity($conn, 'update', 'resource', $id);
	jsonResponse(['message' => 'Resource updated.']);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
	$stmt = $conn->prepare("UPDATE resources SET status = 'Inactive' WHERE id = ? AND status <> 'Inactive'");
	$stmt->execute([$id]);
	if (!$stmt->rowCount()) {
		jsonResponse(['error' => 'Resource not found.'], 404);
	}
	logActivity($conn, 'delete', 'resource', $id);
	jsonResponse(['message' => 'Resource archived.']);
}

methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);

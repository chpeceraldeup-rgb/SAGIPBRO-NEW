<?php

require_once __DIR__ . '/bootstrap.php';
requireApiLogin(['admin', 'official']);

$report = $_GET['report'] ?? 'summary';
switch ($report) {
    case 'resources':
        $stmt = $conn->query("SELECT name, category, stock, unit, CASE WHEN stock = 0 THEN 'Out of stock' WHEN stock <= low_stock_threshold THEN 'Low stock' ELSE 'In stock' END AS stock_status FROM resources WHERE status = 'Available' ORDER BY stock");
        break;
    case 'residents':
        $stmt = $conn->query("SELECT COUNT(*) AS total, SUM(sex = 'Male') AS male, SUM(sex = 'Female') AS female FROM residents WHERE status = 'Active'");
        break;
    case 'evacuation':
        $stmt = $conn->query('SELECT name, capacity, occupants, capacity - occupants AS available_capacity, status FROM evacuation_centers ORDER BY name');
        break;
    case 'distribution':
        $stmt = $conn->query('SELECT r.name AS resource, SUM(d.quantity) AS quantity_distributed, COUNT(*) AS transactions FROM distributions d JOIN resources r ON r.id = d.resource_id GROUP BY d.resource_id ORDER BY quantity_distributed DESC');
        break;
    case 'activity':
        $stmt = $conn->query('SELECT l.*, u.full_name FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.created_at DESC LIMIT 500');
        break;
    case 'summary':
        $stmt = $conn->query("SELECT (SELECT COUNT(*) FROM residents WHERE status = 'Active') AS residents, (SELECT COUNT(*) FROM resources WHERE status = 'Available') AS resources, (SELECT COUNT(*) FROM evacuation_centers WHERE status = 'Open') AS open_centers, (SELECT COUNT(*) FROM evacuees WHERE checked_out_at IS NULL) AS active_evacuees");
        break;
    default:
        jsonResponse(['error' => 'Unknown report.'], 404);
}
jsonResponse(['report' => $report, 'data' => $stmt->fetchAll()]);

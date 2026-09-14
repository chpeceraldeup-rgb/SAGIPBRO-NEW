<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/connection.php';

/** Only known schema identifiers are interpolated. Public requests never supply SQL identifiers. */
function publicSchema(PDO $db): array
{
    static $schemas;
    $schemas ??= new WeakMap();
    if (isset($schemas[$db])) {
        return $schemas[$db];
    }
    $columns = $db->query("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('resources','evacuation_centers','distributions','announcements')")->fetchAll();
    $tables = [];
    foreach ($columns as $column) {
        $tables[$column['TABLE_NAME']][] = $column['COLUMN_NAME'];
    }
    foreach (['resources', 'evacuation_centers', 'distributions', 'announcements'] as $table) {
        if (empty($tables[$table])) {
            throw new RuntimeException('The public service database schema is incomplete.');
        }
    }
    $legacy = in_array('resource_name', $tables['resources'], true);
    $schemas[$db] = [
        'resource_name' => $legacy ? 'resource_name' : 'name',
        'stock' => $legacy ? 'quantity' : 'stock',
        'threshold' => $legacy ? 'minimum_stock' : 'low_stock_threshold',
        'resource_visible' => in_array('status', $tables['resources'], true) ? "r.status = 'Available'" : '1 = 1',
        'center_name' => in_array('center_name', $tables['evacuation_centers'], true) ? 'center_name' : 'name',
        'center_address' => in_array('location', $tables['evacuation_centers'], true) ? 'location' : 'address',
        'occupants' => in_array('current_occupants', $tables['evacuation_centers'], true) ? 'current_occupants' : 'occupants',
        'body' => in_array('content', $tables['announcements'], true) ? 'content' : (in_array('message', $tables['announcements'], true) ? 'message' : 'body'),
        'announcement_published_at' => in_array('published_at', $tables['announcements'], true) ? 'published_at' : 'created_at',
        'announcement_expires_at' => in_array('expires_at', $tables['announcements'], true) ? 'expires_at' : null,
        'distribution_date_only' => in_array('distribution_date', $tables['distributions'], true),
        'distribution_date' => in_array('distribution_date', $tables['distributions'], true) ? 'distribution_date' : 'distributed_at',
        'distribution_location' => in_array('distribution_location', $tables['distributions'], true) ? 'distribution_location' : null,
    ];
    return $schemas[$db];
}

function publicSearchTerm(array $filters): string
{
    return is_string($filters['q'] ?? null) ? trim(substr($filters['q'], 0, 200)) : '';
}

function publicLike(string $term): string
{
    return '%' . strtr($term, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
}

function publicResources(PDO $db, array $filters = []): array
{
    $s = publicSchema($db);
    $sql = "SELECT r.id, r.{$s['resource_name']} AS name, r.category, r.unit,
        r.{$s['stock']} AS stock, r.{$s['threshold']} AS low_stock_threshold, r.updated_at,
        CASE WHEN r.{$s['stock']} <= 0 THEN 'Out of Stock'
             WHEN r.{$s['stock']} <= r.{$s['threshold']} THEN 'Low Stock' ELSE 'Available' END AS availability
        FROM resources r WHERE {$s['resource_visible']}";
    $where = [];
    $params = [];
    if (($term = publicSearchTerm($filters)) !== '') {
        $where[] = "(name LIKE ? ESCAPE '!' OR category LIKE ? ESCAPE '!')";
        $params[] = publicLike($term);
        $params[] = publicLike($term);
    }
    if (is_string($filters['category'] ?? null) && $filters['category'] !== '') {
        $where[] = 'category = ?';
        $params[] = substr($filters['category'], 0, 100);
    }
    if (in_array($filters['status'] ?? '', ['Available', 'Low Stock', 'Out of Stock'], true)) {
        $where[] = 'availability = ?';
        $params[] = $filters['status'];
    }
    $stmt = $db->prepare('SELECT * FROM (' . $sql . ') inventory' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY name, id');
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    $categories = $db->query("SELECT DISTINCT r.category FROM resources r WHERE {$s['resource_visible']} ORDER BY r.category")->fetchAll(PDO::FETCH_COLUMN);
    return ['rows' => $rows, 'categories' => $categories, 'total' => count($rows)];
}

function publicCenters(PDO $db, array $filters = []): array
{
    $s = publicSchema($db);
    $sql = "SELECT e.id, e.{$s['center_name']} AS name, e.{$s['center_address']} AS address,
        e.capacity, e.{$s['occupants']} AS occupants, e.updated_at,
        CASE WHEN LOWER(e.status) IN ('closed', 'under_maintenance') THEN 0
             ELSE GREATEST(CAST(e.capacity AS SIGNED) - CAST(e.{$s['occupants']} AS SIGNED), 0) END AS available_spaces,
        CASE WHEN LOWER(e.status) IN ('closed', 'under_maintenance') THEN 'Closed'
             WHEN LOWER(e.status) = 'full' OR e.{$s['occupants']} >= e.capacity THEN 'Full' ELSE 'Available' END AS availability
        FROM evacuation_centers e";
    $where = [];
    $params = [];
    if (($term = publicSearchTerm($filters)) !== '') {
        $where[] = "(name LIKE ? ESCAPE '!' OR address LIKE ? ESCAPE '!')";
        $params[] = publicLike($term);
        $params[] = publicLike($term);
    }
    if (in_array($filters['status'] ?? '', ['Available', 'Full', 'Closed'], true)) {
        $where[] = 'availability = ?';
        $params[] = $filters['status'];
    }
    $stmt = $db->prepare('SELECT * FROM (' . $sql . ') centers' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY FIELD(availability, 'Available','Full','Closed'), name, id");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        foreach (['capacity', 'occupants', 'available_spaces'] as $key) {
            $row[$key] = (int) $row[$key];
        }
        if ($row['availability'] !== 'Available') {
            $row['available_spaces'] = 0;
        }
    }
    unset($row);
    return ['rows' => $rows, 'total' => count($rows)];
}

function publicAnnouncements(PDO $db, array $filters = []): array
{
    $s = publicSchema($db);
    $publishedAt = $s['announcement_published_at'];
    $expiresAt = $s['announcement_expires_at'];
    $where = ["LOWER(status) = 'published'", "{$publishedAt} IS NOT NULL", "{$publishedAt} <= NOW()"];
    if ($expiresAt !== null) $where[] = "({$expiresAt} IS NULL OR {$expiresAt} > NOW())";
    $params = [];
    if (($term = publicSearchTerm($filters)) !== '') {
        $where[] = "(title LIKE ? ESCAPE '!' OR {$s['body']} LIKE ? ESCAPE '!')";
        $params[] = publicLike($term);
        $params[] = publicLike($term);
    }
    if (in_array($filters['priority'] ?? '', ['Normal', 'Urgent'], true)) {
        $where[] = 'priority = ?';
        $params[] = $filters['priority'];
    }
    $stmt = $db->prepare("SELECT id, title, {$s['body']} AS body, priority, {$publishedAt} AS published_at, " . ($expiresAt !== null ? $expiresAt : 'NULL') . " AS expires_at, updated_at
        FROM announcements WHERE " . implode(' AND ', $where) . " ORDER BY (priority = 'Urgent') DESC, published_at DESC, id DESC");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    return ['rows' => $rows, 'total' => count($rows)];
}

function publicDistributions(PDO $db, array $filters = []): array
{
    $s = publicSchema($db);
    // Staff controls whether an event is active. An elapsed end time prevents stale active notices.
    $rows = $db->query("SELECT id, title, location, starts_at, ends_at, details, updated_at,
        CASE WHEN status IN ('Upcoming','Active') AND ends_at IS NOT NULL AND ends_at <= NOW()
             THEN 'Completed' ELSE status END AS status
        FROM distribution_events WHERE publication_status = 'Published'")->fetchAll();
    // Union of planned resources and actual transactions retains unplanned distributions in totals.
    $items = $db->query("SELECT items.event_id, r.{$s['resource_name']} AS name, r.unit,
        plan.planned_quantity, COALESCE(actual.distributed_quantity, 0) AS distributed_quantity
        FROM (SELECT event_id, resource_id FROM distribution_event_resources
              UNION SELECT event_id, resource_id FROM distributions WHERE event_id IS NOT NULL) items
        JOIN distribution_events e ON e.id = items.event_id AND e.publication_status = 'Published'
        JOIN resources r ON r.id = items.resource_id
        LEFT JOIN distribution_event_resources plan ON plan.event_id = items.event_id AND plan.resource_id = items.resource_id
        LEFT JOIN (SELECT event_id, resource_id, SUM(quantity) AS distributed_quantity FROM distributions
                   WHERE event_id IS NOT NULL GROUP BY event_id, resource_id) actual
          ON actual.event_id = items.event_id AND actual.resource_id = items.resource_id
        ORDER BY name")->fetchAll();
    $byEvent = [];
    foreach ($items as $item) {
        $eventId = $item['event_id'];
        unset($item['event_id']);
        $byEvent[$eventId][] = $item;
    }
    foreach ($rows as &$row) {
        $row['resources'] = $byEvent[$row['id']] ?? [];
        $row['id'] = 'event-' . $row['id'];
        $row['time_recorded'] = true;
    }
    unset($row);
    $location = $s['distribution_location'] ? "COALESCE(NULLIF(TRIM(d.{$s['distribution_location']}), ''), 'Location not recorded')" : "'Location not recorded'";
    // Historical rows have no publication field. Publish totals only, never recipient names, contacts or notes.
    $legacy = $db->query("SELECT DATE(d.{$s['distribution_date']}) AS distribution_day,
        MIN(d.{$s['distribution_date']}) AS starts_at, MAX(d.{$s['distribution_date']}) AS updated_at,
        {$location} AS location, r.{$s['resource_name']} AS name, r.unit, SUM(d.quantity) AS distributed_quantity
        FROM distributions d JOIN resources r ON r.id = d.resource_id
        WHERE d.event_id IS NULL AND d.{$s['distribution_date']} <= NOW()
        GROUP BY DATE(d.{$s['distribution_date']}), {$location}, r.id, r.{$s['resource_name']}, r.unit
        ORDER BY distribution_day DESC, location, name")->fetchAll();
    $history = [];
    foreach ($legacy as $item) {
        $key = $item['distribution_day'] . "\0" . $item['location'];
        if (!isset($history[$key])) {
            $history[$key] = ['id' => 'history-' . substr(hash('sha256', $key), 0, 16),
                'title' => 'Recorded relief distribution', 'location' => $item['location'],
                'starts_at' => $item['starts_at'], 'ends_at' => null, 'status' => 'Completed',
                'details' => 'Summary of recorded distributions. Personal recipient information is not public.',
                'updated_at' => $item['updated_at'], 'time_recorded' => !$s['distribution_date_only'], 'resources' => []];
        }
        $history[$key]['resources'][] = ['name' => $item['name'], 'unit' => $item['unit'], 'planned_quantity' => null, 'distributed_quantity' => $item['distributed_quantity']];
    }
    $rows = array_merge($rows, array_values($history));
    $term = publicSearchTerm($filters);
    $status = in_array($filters['status'] ?? '', ['Upcoming','Active','Completed','Cancelled'], true) ? $filters['status'] : '';
    $rows = array_values(array_filter($rows, static function (array $row) use ($term, $status): bool {
        $searchable = $row['title'] . ' ' . $row['location'] . ' ' . ($row['details'] ?? '') . ' ' . implode(' ', array_column($row['resources'], 'name'));
        return ($status === '' || $row['status'] === $status) && ($term === '' || stripos($searchable, $term) !== false);
    }));
    $order = ['Active' => 0, 'Upcoming' => 1, 'Completed' => 2, 'Cancelled' => 3];
    usort($rows, static function (array $a, array $b) use ($order): int {
        $rank = $order[$a['status']] <=> $order[$b['status']];
        if ($rank !== 0) return $rank;
        $date = strcmp($a['starts_at'], $b['starts_at']);
        return ($a['status'] === 'Upcoming' ? $date : -$date) ?: strcmp($a['id'], $b['id']);
    });
    return ['rows' => $rows, 'total' => count($rows)];
}

function publicReport(PDO $db): array
{
    $resources = publicResources($db)['rows'];
    $centers = publicCenters($db)['rows'];
    $distributions = publicDistributions($db)['rows'];
    $countStatus = static fn(array $rows, string $field, string $status): int => count(array_filter($rows, static fn(array $r): bool => $r[$field] === $status));
    return [
        'resources' => $resources, 'centers' => $centers, 'distributions' => $distributions,
        'summary' => [
            'resources' => count($resources),
            'available_resources' => $countStatus($resources, 'availability', 'Available'),
            'low_stock_resources' => $countStatus($resources, 'availability', 'Low Stock'),
            'out_of_stock_resources' => $countStatus($resources, 'availability', 'Out of Stock'),
            'centers' => count($centers),
            'available_centers' => $countStatus($centers, 'availability', 'Available'),
            'available_spaces' => array_sum(array_column($centers, 'available_spaces')),
            'active_distributions' => $countStatus($distributions, 'status', 'Active'),
            'upcoming_distributions' => $countStatus($distributions, 'status', 'Upcoming'),
        ],
    ];
}

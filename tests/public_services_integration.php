<?php
declare(strict_types=1);

/**
 * Run: php tests/public_services_integration.php
 * Requires PDO MySQL and CREATE/DROP DATABASE permission on the local server.
 * Uses SAGIPBRO_DB_HOST/PORT/USER/PASSWORD, but NEVER SAGIPBRO_DB_NAME.
 * Only a randomly named database created by this process is modified/dropped.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run integration tests from the command line.');
}

require_once __DIR__ . '/../database/public_services_migration.php';
require_once __DIR__ . '/../includes/public_data.php';

$testDatabase = 'sagipbro_test_' . bin2hex(random_bytes(10));
$createdTestDatabase = false;
$server = null;
$assertions = 0;
$failed = false;

function check(bool $condition, string $message): void
{
    global $assertions;
    ++$assertions;
    if (!$condition) {
        throw new LogicException($message);
    }
}

function fixture(PDO $db, string $table, array $values): int
{
    // Identifiers here are hardcoded in this test, never request parameters.
    $keys = array_keys($values);
    $sql = 'INSERT INTO `' . $table . '` (`' . implode('`, `', $keys) . '`) VALUES ('
        . implode(', ', array_fill(0, count($keys), '?')) . ')';
    $statement = $db->prepare($sql);
    $statement->execute(array_values($values));
    return (int) $db->lastInsertId();
}

function rowNamed(array $result, string $name, string $key = 'name'): array
{
    foreach ($result['rows'] as $row) {
        if (($row[$key] ?? null) === $name) {
            return $row;
        }
    }
    throw new LogicException('Expected TEST fixture row was not returned: ' . $name);
}

function hasOnlyNames(array $result, array $expected): bool
{
    $actual = array_column($result['rows'], 'name');
    sort($actual);
    sort($expected);
    return $actual === $expected && (int) $result['total'] === count($expected);
}

try {
    $host = getenv('SAGIPBRO_DB_HOST') ?: '127.0.0.1';
    $port = getenv('SAGIPBRO_DB_PORT') ?: '3306';
    $user = getenv('SAGIPBRO_DB_USER');
    $password = getenv('SAGIPBRO_DB_PASSWORD');
    check(!str_contains($host, ';') && ctype_digit($port), 'Invalid test server host or port.');
    $server = new PDO('mysql:host=' . $host . ';port=' . $port . ';charset=utf8mb4', $user === false ? 'root' : $user, $password === false ? '' : $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $server->exec("SET time_zone = '+08:00'");
    check((bool) preg_match('/\Asagipbro_test_[a-f0-9]{20}\z/', $testDatabase), 'Unsafe generated test database identifier.');
    // No IF NOT EXISTS: an existing database must never become a cleanup target.
    $server->exec('CREATE DATABASE `' . $testDatabase . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $createdTestDatabase = true;
    $server->exec('USE `' . $testDatabase . '`');
    fwrite(STDOUT, "Preparing an isolated test schema; application database is not selected.\n");

    $schema = file_get_contents(__DIR__ . '/../database/sagipbro.sql');
    if ($schema === false) {
        throw new LogicException('Could not read the base schema.');
    }
    foreach (explode(';', $schema) as $statement) {
        $statement = trim($statement);
        if ($statement === '' || preg_match('/\A(?:CREATE\s+DATABASE\b|USE\b)/i', $statement)) {
            continue;
        }
        $server->exec($statement);
    }
    migratePublicServices($server);
    migratePublicServices($server);
    check($server->query('SELECT DATABASE()')->fetchColumn() === $testDatabase, 'Schema import changed the private test database.');
    fwrite(STDOUT, "PASS: original schema import and repeatable additive migration.\n");

    foreach (['publicResources', 'publicCenters', 'publicDistributions', 'publicAnnouncements'] as $query) {
        $empty = $query($server, []);
        check($empty['rows'] === [] && (int) $empty['total'] === 0, $query . ' must return a genuine empty state.');
    }
    $emptyReport = publicReport($server);
    check((int) $emptyReport['summary']['resources'] === 0, 'Empty report resource count must be zero.');
    check((int) $emptyReport['summary']['centers'] === 0, 'Empty report center count must be zero.');
    fwrite(STDOUT, "PASS: empty database results; no sample data fallback.\n");

    $staffId = fixture($server, 'users', [
        'full_name' => 'TEST PRIVATE STAFF NAME', 'username' => 'test_private_staff',
        'password_hash' => 'TEST-NOT-A-LOGIN-HASH', 'role' => 'admin',
    ]);
    $resourceIds = [];
    foreach ([
        ['TEST Relief Rice', 'Food', 40, 5, 'Available'],
        ['TEST Water', 'Water', 5, 5, 'Available'],
        ['TEST Empty Kit', 'Hygiene', 0, 0, 'Available'],
        ['TEST Hidden', 'Hidden category', 100, 5, 'Inactive'],
        ['TEST 100%_Ready', 'Food', 6, 5, 'Available'],
        ['TEST 100ZZReady', 'Food', 7, 5, 'Available'],
        ["TEST Quote ' Packet", 'Food', 8, 5, 'Available'],
    ] as [$name, $category, $stock, $threshold, $status]) {
        $resourceIds[$name] = fixture($server, 'resources', [
            'name' => $name, 'category' => $category, 'unit' => 'packs',
            'stock' => $stock, 'low_stock_threshold' => $threshold, 'status' => $status,
        ]);
    }
    $resources = publicResources($server, []);
    check((int) $resources['total'] === 6, 'Inactive resources must not be public.');
    check(!in_array('Hidden category', $resources['categories'], true), 'Inactive-only categories must not appear.');
    foreach (['Food', 'Water', 'Hygiene'] as $category) {
        check(in_array($category, $resources['categories'], true), 'Active resource category is missing.');
    }
    check(rowNamed($resources, 'TEST Relief Rice')['availability'] === 'Available', 'Stock above threshold must be Available.');
    check(rowNamed($resources, 'TEST Water')['availability'] === 'Low Stock', 'Stock equal to threshold must be Low Stock.');
    check(rowNamed($resources, 'TEST Empty Kit')['availability'] === 'Out of Stock', 'Zero stock takes precedence over threshold.');
    check(hasOnlyNames(publicResources($server, ['status' => 'Low Stock']), ['TEST Water']), 'Low Stock filter is incorrect.');
    check(hasOnlyNames(publicResources($server, ['status' => 'Out of Stock']), ['TEST Empty Kit']), 'Out of Stock filter is incorrect.');
    check((int) publicResources($server, ['status' => 'Available'])['total'] === 4, 'Available filter must exclude low/zero stock.');
    check(hasOnlyNames(publicResources($server, ['category' => 'Water']), ['TEST Water']), 'Category filter is incorrect.');
    check(hasOnlyNames(publicResources($server, ['q' => '%']), ['TEST 100%_Ready']), 'Percent search must match a literal percent.');
    check(hasOnlyNames(publicResources($server, ['q' => '_']), ['TEST 100%_Ready']), 'Underscore search must match a literal underscore.');
    check(hasOnlyNames(publicResources($server, ['q' => "'"]), ["TEST Quote ' Packet"]), 'Quote searches must work with native prepares.');
    check((int) publicResources($server, ['q' => "' OR 1=1 --"])['total'] === 0, 'SQL injection text must be treated as search text.');
    check(hasOnlyNames(publicResources($server, ['q' => 'Water', 'category' => 'Water', 'status' => 'Low Stock']), ['TEST Water']), 'Combined resource filters must work.');
    check((int) publicResources($server, ['q' => ['unexpected'], 'category' => ['unexpected'], 'status' => ['unexpected']])['total'] === 6, 'Array-valued query parameters must be ignored safely.');
    check((int) publicResources($server, ['category' => "' OR 1=1 --"])['total'] === 0, 'Category filtering must be parameterized.');
    check((int) publicResources($server, ['q' => str_repeat('x', 1000)])['total'] === 0, 'Oversized search text must be bounded safely.');
    fwrite(STDOUT, "PASS: resource visibility, exact stock states, categories, literal search, and SQL parameterization.\n");

    foreach ([
        ['TEST Binloc School', 'TEST Zone 1', 100, 30, 'Open'],
        ['TEST Full Hall', 'TEST Zone 2', 20, 20, 'Open'],
        ['TEST Closed Hall', 'TEST Zone 3', 50, 10, 'Closed'],
        ['TEST Zero Capacity', 'TEST Zone 4', 0, 0, 'Open'],
        ['TEST Over Capacity', 'TEST Zone 5', 10, 12, 'Open'],
    ] as [$name, $address, $capacity, $occupants, $status]) {
        fixture($server, 'evacuation_centers', compact('name', 'address', 'capacity', 'occupants', 'status'));
    }
    $centers = publicCenters($server, []);
    check((int) $centers['total'] === 5, 'All center statuses must remain visible.');
    $availableCenter = rowNamed($centers, 'TEST Binloc School');
    check($availableCenter['availability'] === 'Available', 'Open center with space must be Available.');
    check((int) $availableCenter['available_spaces'] === 70, 'Available space must equal capacity minus occupants.');
    check(rowNamed($centers, 'TEST Full Hall')['availability'] === 'Full', 'At-capacity center must be Full.');
    check(rowNamed($centers, 'TEST Closed Hall')['availability'] === 'Closed', 'Closed status must take precedence over capacity.');
    check(rowNamed($centers, 'TEST Zero Capacity')['availability'] === 'Full', 'A zero-capacity center cannot be Available.');
    check((int) rowNamed($centers, 'TEST Over Capacity')['available_spaces'] === 0, 'Available spaces must never be negative.');
    check(hasOnlyNames(publicCenters($server, ['status' => 'Available']), ['TEST Binloc School']), 'Available center filter is incorrect.');
    check((int) publicCenters($server, ['status' => 'Full'])['total'] === 3, 'Full filter must include zero and over-capacity centers.');
    check(hasOnlyNames(publicCenters($server, ['status' => 'Closed']), ['TEST Closed Hall']), 'Closed center filter is incorrect.');
    check(hasOnlyNames(publicCenters($server, ['q' => 'Zone 1']), ['TEST Binloc School']), 'Center location must be searchable.');
    check((int) publicCenters($server, ['q' => '%'])['total'] === 0, 'Center percent searches must be literal.');
    check((int) publicCenters($server, ['q' => ['unexpected'], 'status' => -1])['total'] === 5, 'Invalid center filters must not cause type errors.');
    fwrite(STDOUT, "PASS: center occupancy, availability, edge cases, and filters.\n");

    $clock = $server->query("SELECT DATE_SUB(NOW(), INTERVAL 2 DAY) AS older, DATE_SUB(NOW(), INTERVAL 1 HOUR) AS recent, DATE_ADD(NOW(), INTERVAL 2 DAY) AS future")->fetch();
    foreach ([
        ['TEST Normal current', 'Published', 'Normal', $clock['recent'], null],
        ['TEST Urgent current', 'Published', 'Urgent', $clock['older'], $clock['future']],
        ['TEST Hidden draft', 'Draft', 'Urgent', $clock['older'], null],
        ['TEST Hidden archived', 'Archived', 'Urgent', $clock['older'], null],
        ['TEST Hidden future', 'Published', 'Urgent', $clock['future'], null],
        ['TEST Hidden expired', 'Published', 'Urgent', $clock['older'], $clock['recent']],
        ['TEST Hidden no publish date', 'Published', 'Urgent', null, null],
    ] as [$title, $status, $priority, $publishedAt, $expiresAt]) {
        fixture($server, 'announcements', [
            'title' => $title, 'body' => 'TEST public announcement details', 'status' => $status,
            'priority' => $priority, 'created_by' => $staffId, 'published_at' => $publishedAt, 'expires_at' => $expiresAt,
        ]);
    }
    $announcements = publicAnnouncements($server, []);
    check((int) $announcements['total'] === 2, 'Only current, published, unexpired announcements may be public.');
    check($announcements['rows'][0]['title'] === 'TEST Urgent current', 'Urgent announcements must sort before newer normal ones.');
    check((int) publicAnnouncements($server, ['priority' => 'Urgent'])['total'] === 1, 'Urgent announcement filter is incorrect.');
    check((int) publicAnnouncements($server, ['q' => 'Normal'])['total'] === 1, 'Announcement title search failed.');
    check((int) publicAnnouncements($server, ['q' => 'announcement details'])['total'] === 2, 'Announcement details must be searchable.');
    check((int) publicAnnouncements($server, ['q' => "' OR 1=1 --"])['total'] === 0, 'Announcement search must be parameterized.');
    check((int) publicAnnouncements($server, ['q' => '%'])['total'] === 0, 'Announcement wildcard search must be literal.');
    check((int) publicAnnouncements($server, ['q' => ['unexpected'], 'priority' => ['unexpected']])['total'] === 2, 'Invalid announcement filters must not cause type errors.');
    fwrite(STDOUT, "PASS: announcement publication, scheduling, expiry, urgency, and search.\n");

    $eventIds = [];
    foreach ([
        ['TEST Active Distribution', 'TEST School', 'Active', 'Published', $clock['recent']],
        ['TEST Upcoming Distribution', 'TEST Hall', 'Upcoming', 'Published', $clock['future']],
        ['TEST Completed Distribution', 'TEST Gym', 'Completed', 'Published', $clock['older']],
        ['TEST Cancelled Distribution', 'TEST Court', 'Cancelled', 'Published', $clock['recent']],
        ['TEST Hidden Distribution', 'TEST PRIVATE LOCATION', 'Active', 'Draft', $clock['recent']],
        ['TEST Ended Distribution', 'TEST Old Hall', 'Active', 'Published', $clock['older']],
    ] as [$title, $location, $status, $publicationStatus, $startsAt]) {
        $eventIds[$title] = fixture($server, 'distribution_events', [
            'title' => $title, 'location' => $location, 'details' => 'TEST public distribution instructions',
            'status' => $status, 'publication_status' => $publicationStatus, 'starts_at' => $startsAt,
        ]);
    }
    $statement = $server->prepare('UPDATE distribution_events SET ends_at = ? WHERE id = ?');
    $statement->execute([$clock['recent'], $eventIds['TEST Ended Distribution']]);
    foreach (['TEST Relief Rice' => 100, 'TEST Water' => 20] as $resourceName => $quantity) {
        fixture($server, 'distribution_event_resources', [
            'event_id' => $eventIds['TEST Active Distribution'], 'resource_id' => $resourceIds[$resourceName], 'planned_quantity' => $quantity,
        ]);
    }
    foreach ([
        [$eventIds['TEST Active Distribution'], 'TEST Relief Rice', 7],
        [$eventIds['TEST Active Distribution'], 'TEST Relief Rice', 3],
        [$eventIds['TEST Active Distribution'], 'TEST Water', 2],
        [$eventIds['TEST Active Distribution'], 'TEST Empty Kit', 1],
        [$eventIds['TEST Hidden Distribution'], 'TEST Relief Rice', 99],
        [null, 'TEST Relief Rice', 4],
        [null, 'TEST Relief Rice', 6],
    ] as [$eventId, $resourceName, $quantity]) {
        fixture($server, 'distributions', [
            'resource_id' => $resourceIds[$resourceName], 'recipient_name' => 'TEST PRIVATE RECIPIENT NAME',
            'quantity' => $quantity, 'distributed_by' => $staffId, 'distributed_at' => $clock['recent'], 'event_id' => $eventId,
        ]);
    }
    fixture($server, 'distributions', [
        'resource_id' => $resourceIds['TEST Relief Rice'], 'recipient_name' => 'TEST PRIVATE FUTURE RECIPIENT',
        'quantity' => 999, 'distributed_by' => $staffId, 'distributed_at' => $clock['future'], 'event_id' => null,
    ]);
    $distributions = publicDistributions($server, []);
    $serialized = json_encode($distributions, JSON_THROW_ON_ERROR);
    foreach (['TEST PRIVATE RECIPIENT NAME', 'TEST PRIVATE STAFF NAME', 'TEST PRIVATE LOCATION', 'TEST Hidden Distribution', 'recipient_name', 'recipient_resident_id', 'distributed_by', 'password_hash'] as $privateValue) {
        check(!str_contains($serialized, $privateValue), 'Public distribution results exposed private or unpublished information.');
    }
    check((int) publicDistributions($server, ['status' => 'Active'])['total'] === 1, 'Active distribution filter must exclude drafts.');
    check((int) publicDistributions($server, ['status' => 'Upcoming'])['total'] === 1, 'Upcoming distributions must be public.');
    check((int) publicDistributions($server, ['q' => 'TEST School'])['total'] === 1, 'Distribution location must be searchable.');
    check((int) publicDistributions($server, ['q' => "' OR 1=1 --"])['total'] === 0, 'Distribution search must be parameterized.');
    check((int) publicDistributions($server, ['q' => '%'])['total'] === 0, 'Distribution wildcard search must be literal.');
    check((int) publicDistributions($server, ['q' => ['unexpected'], 'status' => ['unexpected']])['total'] === (int) $distributions['total'], 'Invalid distribution filters must not cause type errors.');
    check(rowNamed($distributions, 'TEST Ended Distribution', 'title')['status'] === 'Completed', 'Elapsed published events must not remain active.');
    $activeEvent = rowNamed($distributions, 'TEST Active Distribution', 'title');
    check(isset($activeEvent['resources']) && count($activeEvent['resources']) === 3, 'Events must include planned and unplanned distributed resources without join duplication.');
    $rice = null;
    $unplannedKit = null;
    foreach ($activeEvent['resources'] as $resource) {
        if ($resource['name'] === 'TEST Relief Rice') {
            $rice = $resource;
        } elseif ($resource['name'] === 'TEST Empty Kit') {
            $unplannedKit = $resource;
        }
    }
    check($rice !== null, 'Active distribution rice resource is missing.');
    check((float) $rice['planned_quantity'] === 100.0, 'Planned quantity must not multiply across transaction joins.');
    check((float) $rice['distributed_quantity'] === 10.0, 'Distributed quantities must aggregate matching event transactions exactly once.');
    check($unplannedKit !== null && $unplannedKit['planned_quantity'] === null, 'Unplanned distributed resources must remain visible without invented plans.');
    check((float) $unplannedKit['distributed_quantity'] === 1.0, 'Unplanned distributed quantities must remain exact.');
    $historyRows = array_values(array_filter($distributions['rows'], static fn(array $row): bool => str_starts_with($row['id'], 'history-')));
    check(count($historyRows) === 1, 'Same-day legacy transactions must aggregate into one public history record.');
    check(substr($historyRows[0]['starts_at'], 0, 10) !== substr($clock['future'], 0, 10), 'Future-dated legacy transactions must not appear as completed history.');
    check($historyRows[0]['status'] === 'Completed', 'Legacy transaction summaries must be Completed.');
    check($historyRows[0]['location'] === 'Location not recorded', 'Legacy rows without location must not invent a location.');
    check(count($historyRows[0]['resources']) === 1, 'Legacy transactions must aggregate by resource.');
    check((float) $historyRows[0]['resources'][0]['distributed_quantity'] === 10.0, 'Legacy quantities must total exactly, excluding event-linked records.');
    check($historyRows[0]['resources'][0]['planned_quantity'] === null, 'Legacy records must not invent a planned quantity.');
    fwrite(STDOUT, "PASS: public distribution scheduling, event supply totals, filters, and privacy.\n");

    $report = publicReport($server);
    foreach ([
        'resources' => 6, 'available_resources' => 4, 'low_stock_resources' => 1,
        'out_of_stock_resources' => 1, 'centers' => 5, 'available_centers' => 1,
        'available_spaces' => 70, 'active_distributions' => 1, 'upcoming_distributions' => 1,
    ] as $key => $expected) {
        check((int) $report['summary'][$key] === $expected, 'Public report summary is incorrect: ' . $key);
    }
    check(is_array($report['resources']) && is_array($report['centers']) && is_array($report['distributions']), 'Report must expose resource, center, and recent distribution summaries.');
    $reportJson = json_encode($report, JSON_THROW_ON_ERROR);
    check(!str_contains($reportJson, 'TEST PRIVATE'), 'Public reports must not expose recipient/staff identities or draft locations.');
    fwrite(STDOUT, "PASS: public report summary and privacy.\n");

    // Exercise the alternative existing-barangay schema in this SAME private DB.
    // No application database is read, altered, seeded, or dropped.
    $statement = $server->prepare('DELETE FROM resources WHERE id = ?');
    $statement->execute([$resourceIds['TEST Hidden']]);
    $server->exec('ALTER TABLE resources CHANGE name resource_name VARCHAR(120) NOT NULL,
        CHANGE stock quantity DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0,
        CHANGE low_stock_threshold minimum_stock DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 10,
        DROP COLUMN status');
    $server->exec('ALTER TABLE evacuation_centers CHANGE name center_name VARCHAR(150) NOT NULL,
        CHANGE address location VARCHAR(255) NOT NULL,
        CHANGE occupants current_occupants INT UNSIGNED NOT NULL DEFAULT 0,
        MODIFY status VARCHAR(30) NOT NULL');
    $server->exec("UPDATE evacuation_centers SET status = CASE WHEN status = 'Closed' THEN 'under_maintenance' ELSE LOWER(status) END");
    $server->exec('ALTER TABLE announcements CHANGE body content TEXT NOT NULL, MODIFY status VARCHAR(30) NOT NULL');
    $server->exec('UPDATE announcements SET status = LOWER(status)');
    $server->exec('ALTER TABLE distributions CHANGE distributed_at distribution_date DATE NOT NULL,
        ADD COLUMN distribution_location VARCHAR(255) NULL');
    $server->exec("UPDATE distributions SET distribution_location = 'TEST Recorded Location' WHERE event_id IS NULL");
    migratePublicServices($server);
    migratePublicServices($server);
    // Schema capabilities are cached per PDO instance, so use a new test-only connection.
    $alternative = new PDO('mysql:host=' . $host . ';port=' . $port . ';dbname=' . $testDatabase . ';charset=utf8mb4', $user === false ? 'root' : $user, $password === false ? '' : $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $alternative->exec("SET time_zone = '+08:00'");
    check((int) publicResources($alternative)['total'] === 6, 'Alternative resource schema must be supported.');
    check(hasOnlyNames(publicResources($alternative, ['status' => 'Low Stock']), ['TEST Water']), 'Alternative quantity/threshold columns must drive availability.');
    check(hasOnlyNames(publicCenters($alternative, ['status' => 'Closed']), ['TEST Closed Hall']), 'Under-maintenance centers must appear Closed.');
    check((int) rowNamed(publicCenters($alternative), 'TEST Binloc School')['available_spaces'] === 70, 'Alternative occupancy columns must compute spaces.');
    check((int) publicAnnouncements($alternative, ['q' => 'announcement details'])['total'] === 2, 'Alternative announcement content/lowercase statuses must work.');
    $alternativeDistributions = publicDistributions($alternative);
    $alternativeHistory = array_values(array_filter($alternativeDistributions['rows'], static fn(array $row): bool => str_starts_with($row['id'], 'history-')));
    check(count($alternativeHistory) === 1, 'Date-only legacy distributions must be grouped.');
    check($alternativeHistory[0]['time_recorded'] === false, 'Date-only records must not pretend to have a recorded time.');
    check($alternativeHistory[0]['location'] === 'TEST Recorded Location', 'Legacy distribution location must be preserved.');
    check((float) $alternativeHistory[0]['resources'][0]['distributed_quantity'] === 10.0, 'Alternative legacy quantities must remain exact.');
    check((int) publicDistributions($alternative, ['q' => 'TEST Recorded Location'])['total'] === 1, 'Alternative legacy location must be searchable.');
    check(publicReport($alternative)['summary'] === $report['summary'], 'Schema variants must produce the same public report totals.');
    $alternative = null;
    fwrite(STDOUT, "PASS: alternative schema column names, maintenance state, lowercase publication, and date-only history.\n");
} catch (Throwable $error) {
    $failed = true;
    $message = $error instanceof LogicException ? $error->getMessage() : get_class($error) . ' at ' . basename($error->getFile()) . ':' . $error->getLine();
    fwrite(STDERR, 'FAIL: ' . $message . "\n");
    if ($error instanceof PDOException) {
        fwrite(STDERR, "Check PDO MySQL, server access, and permission to create an isolated test database. No credentials are printed.\n");
    }
} finally {
    if ($createdTestDatabase && $server instanceof PDO && preg_match('/\Asagipbro_test_[a-f0-9]{20}\z/', $testDatabase)) {
        try {
            // The exact fresh database created above is the only deletion target.
            $server->exec('DROP DATABASE `' . $testDatabase . '`');
            fwrite(STDOUT, "Cleaned up the private test database; configured application database was untouched.\n");
        } catch (Throwable $cleanupError) {
            $failed = true;
            fwrite(STDERR, 'Could not clean up private test database ' . $testDatabase . ". Remove only this test database after checking the server.\n");
        }
    }
}
fwrite($failed ? STDERR : STDOUT, ($failed ? 'FAILED' : 'PASSED') . ': ' . $assertions . " assertions.\n");
exit($failed ? 1 : 0);

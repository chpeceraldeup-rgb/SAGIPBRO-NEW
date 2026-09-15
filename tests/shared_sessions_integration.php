<?php
declare(strict_types=1);

/** Isolated MySQL test. Requires CREATE/DROP DATABASE on a test server. */
if (PHP_SAPI !== 'cli') exit('Run from the command line.');

require_once __DIR__ . '/../config/database_session_handler.php';
$name = 'sagipbro_session_test_' . bin2hex(random_bytes(10));
$created = false;
$server = null;
$failed = false;

try {
    $host = getenv('SAGIPBRO_DB_HOST') ?: '127.0.0.1';
    $port = getenv('SAGIPBRO_DB_PORT') ?: '3306';
    if (strpbrk($host, ";\r\n") !== false || !ctype_digit($port)
        || !preg_match('/\Asagipbro_session_test_[a-f0-9]{20}\z/', $name)) {
        throw new RuntimeException('Unsafe test database settings.');
    }
    $user = getenv('SAGIPBRO_DB_USER');
    $password = getenv('SAGIPBRO_DB_PASSWORD');
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5];
    $server = new PDO($dsn, $user === false ? 'root' : $user, $password === false ? '' : $password, $options);
    $server->exec('CREATE DATABASE `' . $name . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $created = true;
    $testDsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $node1 = new PDO($testDsn, $user === false ? 'root' : $user, $password === false ? '' : $password, $options);
    $node2 = new PDO($testDsn, $user === false ? 'root' : $user, $password === false ? '' : $password, $options);
    $node1->exec("CREATE TABLE sagipbro_sessions (
        id VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
        data MEDIUMBLOB NOT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_sagipbro_sessions_updated (updated_at)
    ) ENGINE=InnoDB");
    $one = new SagipbroDatabaseSessionHandler($node1);
    $two = new SagipbroDatabaseSessionHandler($node2);
    $id = bin2hex(random_bytes(16));
    if ($two->validateId($id)) throw new RuntimeException('Missing ID was accepted.');
    if (!$one->write($id, 'user_id|i:7;')) throw new RuntimeException('Write failed.');
    if ($one->read($id) !== 'user_id|i:7;') throw new RuntimeException('Node 1 read failed.');
    $busy = false;
    try {
        $two->read($id);
    } catch (RuntimeException $error) {
        $busy = $error->getMessage() === 'Session storage is busy.';
    }
    if (!$busy) throw new RuntimeException('Concurrent session access was not serialized.');
    $one->close();
    if (!$two->validateId($id) || $two->read($id) !== 'user_id|i:7;') {
        throw new RuntimeException('Node 2 could not read node 1 session.');
    }
    $two->close();
    if (!$two->updateTimestamp($id, '') || !$two->destroy($id)) {
        throw new RuntimeException('Timestamp or destroy failed.');
    }
    $two->close();
    if ($one->validateId($id) || $one->read($id) !== '') {
        throw new RuntimeException('Destroyed session remained valid.');
    }
    $one->close();
} catch (Throwable $error) {
    fwrite(STDERR, 'FAIL: shared session test (' . get_class($error) . "). Check isolated MySQL access.\n");
    $failed = true;
} finally {
    if ($created && $server instanceof PDO) {
        try {
            $server->exec('DROP DATABASE `' . $name . '`');
        } catch (Throwable $error) {
            fwrite(STDERR, 'FAIL: isolated test database cleanup (' . get_class($error) . ").\n");
            $failed = true;
        }
    }
}
if ($failed) exit(1);
echo "PASS: two MySQL connections shared, refreshed, and destroyed one session.\n";

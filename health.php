<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/config/connection.php';

try {
    $production = getenv('SAGIPBRO_ENV') === 'production';
    $driver = getenv('SAGIPBRO_SESSION_DRIVER') ?: ($production ? 'database' : 'files');
    if (!in_array($driver, ['files', 'database'], true)
        || ($production && ($driver !== 'database' || getenv('SAGIPBRO_SECURE_COOKIES') !== '1'))) {
        throw new RuntimeException('Invalid session configuration.');
    }
    $database = sagipbroDatabase();
    $database->query('SELECT 1')->fetchColumn();
    $database->query('SELECT 1 FROM sagipbro_sessions LIMIT 1')->fetchColumn();
    echo "ready\n";
} catch (Throwable $error) {
    error_log('SAGIPBRO readiness failed (' . get_class($error) . ').');
    http_response_code(503);
    header('Retry-After: 5');
    echo "unavailable\n";
}

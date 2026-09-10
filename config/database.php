<?php

require_once __DIR__ . '/connection.php';

try {
    $conn = sagipbroDatabase();
} catch (Throwable $e) {
    error_log('SAGIPBRO database connection unavailable (' . get_class($e) . ').');
    http_response_code(503);
    exit('Database temporarily unavailable. Please contact the system administrator.');
}

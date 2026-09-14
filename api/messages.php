<?php
require_once __DIR__ . '/bootstrap.php';
requireApiLogin(['admin', 'official']);
header('Cache-Control: no-store, private');
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    methodNotAllowed(['GET']);
}
session_write_close();
try {
    $messages = $conn->query('SELECT id, name, email, phone, sitio, subject, message, status, created_at FROM contact_messages ORDER BY created_at DESC, id DESC')->fetchAll();
    jsonResponse(['data' => $messages]);
} catch (Throwable $e) {
    error_log('Messages API unavailable (' . get_class($e) . ').');
    jsonResponse(['error' => 'Messages temporarily unavailable.'], 503);
}

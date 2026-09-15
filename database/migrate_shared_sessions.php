<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run this migration from the command line.');
}

require_once __DIR__ . '/../config/connection.php';

try {
    sagipbroDatabase()->exec("CREATE TABLE IF NOT EXISTS sagipbro_sessions (
        id VARCHAR(128) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
        data MEDIUMBLOB NOT NULL,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_sagipbro_sessions_updated (updated_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Shared session table ready.\n";
} catch (Throwable $error) {
    error_log('Shared session migration failed (' . get_class($error) . ').');
    fwrite(STDERR, "Shared session migration failed. Check database access.\n");
    exit(1);
}

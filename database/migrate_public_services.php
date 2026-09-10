<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run database migrations from the command line.');
}
require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/public_services_migration.php';
try {
    migratePublicServices(sagipbroDatabase());
    echo "Public Services schema is ready. Existing records were preserved; no sample records were inserted.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed. Check the selected database, credentials, and imported base schema.\n" . $e->getMessage() . "\n");
    exit(1);
}

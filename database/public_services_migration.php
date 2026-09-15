<?php
declare(strict_types=1);

/** Additive migration: supports both the repository schema and the existing barangay schema. */
function migratePublicServices(PDO $db): void
{
    $columns = $db->prepare('SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
    $tableColumns = static function (string $table) use ($columns): array {
        $columns->execute([$table]);
        return $columns->fetchAll(PDO::FETCH_COLUMN);
    };
    foreach (['resources', 'evacuation_centers', 'distributions', 'announcements'] as $table) {
        if (!$tableColumns($table)) {
            throw new RuntimeException('Import the base SAGIPBRO schema before running this migration.');
        }
    }
    $db->exec("CREATE TABLE IF NOT EXISTS distribution_events (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(180) NOT NULL,
        location VARCHAR(255) NOT NULL,
        details TEXT NULL,
        starts_at DATETIME NOT NULL,
        ends_at DATETIME NULL,
        status ENUM('Upcoming', 'Active', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Upcoming',
        publication_status ENUM('Draft', 'Published') NOT NULL DEFAULT 'Draft',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_public_event_schedule (publication_status, status, starts_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $db->exec("CREATE TABLE IF NOT EXISTS distribution_event_resources (
        event_id INT UNSIGNED NOT NULL,
        resource_id INT UNSIGNED NOT NULL,
        planned_quantity DECIMAL(12,2) UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY (event_id, resource_id),
        CONSTRAINT fk_public_event_plan FOREIGN KEY (event_id) REFERENCES distribution_events(id) ON DELETE CASCADE,
        CONSTRAINT fk_public_event_resource FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE RESTRICT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    if (!in_array('event_id', $tableColumns('distributions'), true)) {
        $db->exec('ALTER TABLE distributions ADD COLUMN event_id INT UNSIGNED NULL, ADD CONSTRAINT fk_distribution_public_event FOREIGN KEY (event_id) REFERENCES distribution_events(id) ON DELETE SET NULL');
    }
    $announcementColumns = $tableColumns('announcements');
    if (!in_array('priority', $announcementColumns, true)) {
        $db->exec("ALTER TABLE announcements ADD COLUMN priority ENUM('Normal', 'Urgent') NOT NULL DEFAULT 'Normal'");
    }
    if (!in_array('expires_at', $announcementColumns, true)) {
        $db->exec('ALTER TABLE announcements ADD COLUMN expires_at DATETIME NULL');
    }
    $centerColumns = $tableColumns('evacuation_centers');
    if (!in_array('contact_person', $centerColumns, true)) {
        $db->exec('ALTER TABLE evacuation_centers ADD COLUMN contact_person VARCHAR(150) NULL');
    }
    if (!in_array('contact_number', $centerColumns, true)) {
        $db->exec('ALTER TABLE evacuation_centers ADD COLUMN contact_number VARCHAR(30) NULL');
    }
    if (!in_array('notes', $centerColumns, true)) {
        $db->exec('ALTER TABLE evacuation_centers ADD COLUMN notes TEXT NULL');
    }
}

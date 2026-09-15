<?php
declare(strict_types=1);

/** Shared session storage. MySQL named locks serialize requests using the same ID. */
final class SagipbroDatabaseSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    private ?string $lockedName = null;

    public function __construct(private PDO $database) {}

    public function open(string $path, string $name): bool { return true; }

    public function close(): bool
    {
        if ($this->lockedName !== null) {
            $statement = $this->database->prepare('SELECT RELEASE_LOCK(?)');
            $statement->execute([$this->lockedName]);
            $this->lockedName = null;
        }
        return true;
    }

    private function lock(string $id): void
    {
        $name = 'sb_s_' . substr(hash('sha256', $id), 0, 58);
        if ($this->lockedName === $name) return;
        if ($this->lockedName !== null) $this->close();
        $statement = $this->database->prepare('SELECT GET_LOCK(?, 5)');
        $statement->execute([$name]);
        if ((int) $statement->fetchColumn() !== 1) {
            throw new RuntimeException('Session storage is busy.');
        }
        $this->lockedName = $name;
    }

    public function read(string $id): string|false
    {
        $this->lock($id);
        $statement = $this->database->prepare('SELECT data FROM sagipbro_sessions WHERE id = ?');
        $statement->execute([$id]);
        $data = $statement->fetchColumn();
        return $data === false ? '' : (string) $data;
    }

    public function write(string $id, string $data): bool
    {
        $this->lock($id);
        $statement = $this->database->prepare('INSERT INTO sagipbro_sessions (id, data, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE data = VALUES(data), updated_at = NOW()');
        return $statement->execute([$id, $data]);
    }

    public function destroy(string $id): bool
    {
        $this->lock($id);
        $statement = $this->database->prepare('DELETE FROM sagipbro_sessions WHERE id = ?');
        return $statement->execute([$id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $statement = $this->database->prepare('DELETE FROM sagipbro_sessions WHERE updated_at < DATE_SUB(NOW(), INTERVAL ? SECOND)');
        $statement->execute([$max_lifetime]);
        return $statement->rowCount();
    }

    public function validateId(string $id): bool
    {
        $statement = $this->database->prepare('SELECT 1 FROM sagipbro_sessions WHERE id = ?');
        $statement->execute([$id]);
        return (bool) $statement->fetchColumn();
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        $this->lock($id);
        $statement = $this->database->prepare('UPDATE sagipbro_sessions SET updated_at = NOW() WHERE id = ?');
        return $statement->execute([$id]);
    }
}

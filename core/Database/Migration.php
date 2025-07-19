<?php

namespace Core\Database;

use App\Config\Database;

class Migration
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->connection->exec($sql);
    }

    public function getMigrations(): array
    {
        $this->createMigrationsTable();

        $stmt = $this->connection->query("SELECT migration FROM migrations ORDER BY id");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function addMigration(string $migration, int $batch): void
    {
        $stmt = $this->connection->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migration, $batch]);
    }

    public function getLastBatch(): int
    {
        $stmt = $this->connection->query("SELECT MAX(batch) FROM migrations");
        return (int) $stmt->fetchColumn();
    }

    public function runMigration(string $migrationFile): void
    {
        $sql = file_get_contents($migrationFile);
        $this->connection->exec($sql);
    }
}
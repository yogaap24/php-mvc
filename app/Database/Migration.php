<?php

namespace Yogaap\PHP\MVC\Database;

use Yogaap\PHP\MVC\Config\Database;

class Migration
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $driver = $this->getDriver();

        switch ($driver) {
            case 'pgsql':
            case 'postgres':
                $sql = "CREATE TABLE IF NOT EXISTS migrations (
                    id SERIAL PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    CONSTRAINT unique_migration UNIQUE (migration)
                )";
                break;

            case 'sqlite':
                $sql = "CREATE TABLE IF NOT EXISTS migrations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    migration VARCHAR(255) NOT NULL,
                    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(migration)
                )";
                break;

            case 'mysql':
            default:
                $sql = "CREATE TABLE IF NOT EXISTS migrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_migration (migration)
                )";
                break;
        }

        $this->connection->exec($sql);
    }

    private function getDriver(): string
    {
        return $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    public function run(): void
    {
        $migrationPath = __DIR__ . '/../../database/migrations';

        if (!is_dir($migrationPath)) {
            mkdir($migrationPath, 0755, true);
        }

        $files = glob($migrationPath . '/*.sql');
        sort($files);

        foreach ($files as $file) {
            $migrationName = basename($file);

            if (!$this->isMigrationExecuted($migrationName)) {
                echo "Running migration: {$migrationName}\n";
                $this->executeMigration($file, $migrationName);
                echo "Migration {$migrationName} completed.\n";
            } else {
                echo "Migration {$migrationName} already executed.\n";
            }
        }
    }

    private function isMigrationExecuted(string $migrationName): bool
    {
        $sql = "SELECT COUNT(*) FROM migrations WHERE migration = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$migrationName]);

        return $statement->fetchColumn() > 0;
    }

    private function executeMigration(string $filePath, string $migrationName): void
    {
        $sql = file_get_contents($filePath);

        // Parse SQL for current database driver
        $parser = new SQLParser($this->getDriver());
        $sql = $parser->parse($sql);

        try {
            // Ensure we have a fresh connection
            $this->connection = Database::getConnection();

            // For MySQL, we need to handle DDL statements differently
            // because they auto-commit
            if ($this->getDriver() === 'mysql') {
                $this->executeMySQLMigration($sql, $migrationName);
            } else {
                $this->executeTransactionalMigration($sql, $migrationName);
            }
        } catch (\Exception $e) {
            throw new \Exception("Migration {$migrationName} failed: " . $e->getMessage());
        }
    }

    private function executeMySQLMigration(string $sql, string $migrationName): void
    {
        // Split SQL by semicolon and execute each statement
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $this->executeStatement($statement);
            }
        }

        // Record the migration
        $recordSql = "INSERT INTO migrations (migration) VALUES (?)";
        $recordStatement = $this->connection->prepare($recordSql);
        $recordStatement->execute([$migrationName]);
    }

    private function executeTransactionalMigration(string $sql, string $migrationName): void
    {
        // Start transaction for non-MySQL databases
        $this->connection->beginTransaction();

        try {
            // Split SQL by semicolon and execute each statement
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $this->executeStatement($statement);
                }
            }

            // Record the migration
            $recordSql = "INSERT INTO migrations (migration) VALUES (?)";
            $recordStatement = $this->connection->prepare($recordSql);
            $recordStatement->execute([$migrationName]);

            $this->connection->commit();
        } catch (\Exception $e) {
            if ($this->connection->inTransaction()) {
                $this->connection->rollBack();
            }
            throw $e;
        }
    }

    private function executeStatement(string $statement): void
    {
        // For MySQL, handle CREATE INDEX statements specially
        if ($this->getDriver() === 'mysql' && preg_match('/CREATE INDEX\s+(\w+)\s+ON\s+(\w+)/i', $statement, $matches)) {
            $indexName = $matches[1];
            $tableName = $matches[2];

            // Check if index already exists
            $checkSql = "SELECT COUNT(*) FROM information_schema.statistics
                        WHERE table_schema = DATABASE()
                        AND table_name = ?
                        AND index_name = ?";
            $checkStatement = $this->connection->prepare($checkSql);
            $checkStatement->execute([$tableName, $indexName]);

            if ($checkStatement->fetchColumn() == 0) {
                $this->connection->exec($statement);
            }
        } else {
            $this->connection->exec($statement);
        }
    }

    public function rollback(string $migrationName): void
    {
        $rollbackPath = __DIR__ . '/../../database/rollbacks';
        $rollbackFile = $rollbackPath . '/' . $migrationName;

        if (!file_exists($rollbackFile)) {
            throw new \Exception("Rollback file not found: {$rollbackFile}");
        }

        $sql = file_get_contents($rollbackFile);

        try {
            $this->connection->beginTransaction();

            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    $this->connection->exec($statement);
                }
            }

            // Remove migration record
            $deleteSql = "DELETE FROM migrations WHERE migration = ?";
            $deleteStatement = $this->connection->prepare($deleteSql);
            $deleteStatement->execute([$migrationName]);

            $this->connection->commit();
            echo "Rollback {$migrationName} completed.\n";
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \Exception("Rollback {$migrationName} failed: " . $e->getMessage());
        }
    }

    public function status(): void
    {
        $sql = "SELECT migration, executed_at FROM migrations ORDER BY executed_at";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $executed = $statement->fetchAll();

        echo "Executed migrations:\n";
        foreach ($executed as $migration) {
            echo "- {$migration['migration']} (executed at: {$migration['executed_at']})\n";
        }

        $migrationPath = __DIR__ . '/../../database/migrations';
        $files = glob($migrationPath . '/*.sql');
        $executedNames = array_column($executed, 'migration');

        echo "\nPending migrations:\n";
        $hasPending = false;
        foreach ($files as $file) {
            $migrationName = basename($file);
            if (!in_array($migrationName, $executedNames)) {
                echo "- {$migrationName}\n";
                $hasPending = true;
            }
        }

        if (!$hasPending) {
            echo "No pending migrations.\n";
        }
    }

    public function fresh(): void
    {
        // Ensure we have a fresh connection
        $this->connection = Database::getConnection();

        $this->showInfo("Dropping all tables...");
        $this->dropAllTables();

        $this->showInfo("Clearing migrations table...");
        $this->clearMigrationsTable();

        $this->showInfo("Running fresh migrations...");
        $this->run();
    }

    private function dropAllTables(): void
    {
        $driver = $this->getDriver();

        switch ($driver) {
            case 'pgsql':
            case 'postgres':
                $this->dropPostgreSQLTables();
                break;
            case 'sqlite':
                $this->dropSQLiteTables();
                break;
            case 'mysql':
            default:
                $this->dropMySQLTables();
                break;
        }
    }

    private function dropMySQLTables(): void
    {
        $this->connection->exec('SET FOREIGN_KEY_CHECKS = 0');

        $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $tables = $statement->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $this->connection->exec("DROP TABLE IF EXISTS `{$table}`");
        }

        $this->connection->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function dropPostgreSQLTables(): void
    {
        $sql = "SELECT tablename FROM pg_tables WHERE schemaname = 'public'";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $tables = $statement->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $this->connection->exec("DROP TABLE IF EXISTS \"{$table}\" CASCADE");
        }
    }

    private function dropSQLiteTables(): void
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $tables = $statement->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $this->connection->exec("DROP TABLE IF EXISTS `{$table}`");
        }
    }

    private function clearMigrationsTable(): void
    {
        $this->createMigrationsTable();
        $this->connection->exec("DELETE FROM migrations");
    }

    private function showInfo(string $message): void
    {
        echo "ℹ️  {$message}\n";
    }
}

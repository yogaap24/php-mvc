<?php

namespace Core\Console;

use Core\Database\Migration;
use Core\Database\SQLParser;
use App\Config\Database;

class MigrateCommand
{
    private Migration $migration;
    private SQLParser $sqlParser;

    public function __construct()
    {
        $this->migration = new Migration();
        $this->sqlParser = new SQLParser(Database::getConnection());
    }

    public function run(): void
    {
        $this->migration->createMigrationsTable();

        $migrationFiles = $this->getMigrationFiles();
        $executedMigrations = $this->migration->getMigrations();

        $pendingMigrations = array_diff($migrationFiles, $executedMigrations);

        if (empty($pendingMigrations)) {
            echo "No pending migrations.\n";
            return;
        }

        $batch = $this->migration->getLastBatch() + 1;

        foreach ($pendingMigrations as $migrationFile) {
            $this->runMigration($migrationFile, $batch);
        }
    }

    public function status(): void
    {
        $this->migration->createMigrationsTable();

        $migrationFiles = $this->getMigrationFiles();
        $executedMigrations = $this->migration->getMigrations();

        echo "Migration Status:\n";
        echo "================\n\n";

        if (empty($migrationFiles)) {
            echo "No migration files found.\n";
            return;
        }

        foreach ($migrationFiles as $file) {
            $status = in_array($file, $executedMigrations) ? '✓ Executed' : '✗ Pending';
            echo sprintf("%-50s %s\n", $file, $status);
        }
    }

    private function getMigrationFiles(): array
    {
        $migrationDir = __DIR__ . '/../../database/migrations/';
        $files = glob($migrationDir . '*.sql');

        if (!$files) {
            return [];
        }

        return array_map(function($file) {
            return basename($file, '.sql');
        }, $files);
    }

    private function runMigration(string $migrationName, int $batch): void
    {
        $migrationFile = __DIR__ . '/../../database/migrations/' . $migrationName . '.sql';

        if (!file_exists($migrationFile)) {
            echo "Migration file not found: {$migrationFile}\n";
            return;
        }

        try {
            $sql = file_get_contents($migrationFile);
            $this->sqlParser->parseAndExecute($sql);
            $this->migration->addMigration($migrationName, $batch);

            echo "Migrated: {$migrationName}\n";
        } catch (\Exception $e) {
            echo "Error migrating {$migrationName}: " . $e->getMessage() . "\n";
        }
    }
}
<?php

namespace Yogaap\PHP\MVC\Console;

use Yogaap\PHP\MVC\Database\Migration;

class MigrateCommand
{
    private Migration $migration;

    public function __construct()
    {
        $this->migration = new Migration();
    }

    public function run(array $args): void
    {
        $command = $args[1] ?? 'help';

        try {
            switch ($command) {
                case 'migrate':
                    $this->runMigrations();
                    break;

                case 'rollback':
                    $migrationName = $args[2] ?? null;
                    if (!$migrationName) {
                        $this->showError("Usage: php console migrate:rollback <migration_name>");
                        return;
                    }
                    $this->rollbackMigration($migrationName);
                    break;

                case 'status':
                    $this->showStatus();
                    break;

                case 'fresh':
                    $this->freshMigration();
                    break;

                case 'help':
                default:
                    $this->showHelp();
                    break;
            }
        } catch (\Exception $e) {
            $this->showError($e->getMessage());
        }
    }

    private function runMigrations(): void
    {
        $this->showInfo("Running migrations...");
        $this->migration->run();
        $this->showSuccess("Migrations completed!");
    }

    private function rollbackMigration(string $migrationName): void
    {
        $this->showInfo("Rolling back migration: {$migrationName}");
        $this->migration->rollback($migrationName);
        $this->showSuccess("Rollback completed!");
    }

    private function showStatus(): void
    {
        $this->showInfo("Migration status:");
        $this->migration->status();
    }

    private function freshMigration(): void
    {
        $this->showInfo("Fresh migration (dropping all tables)...");
        $this->migration->fresh();
        $this->showSuccess("Fresh migration completed!");
    }

    private function showHelp(): void
    {
        echo "\n";
        echo "🚀 PHP MVC Migration Commands\n";
        echo "============================\n\n";
        echo "Available commands:\n";
        echo "  migrate        - Run all pending migrations\n";
        echo "  rollback       - Rollback a specific migration\n";
        echo "  status         - Show migration status\n";
        echo "  fresh          - Drop all tables and run migrations\n";
        echo "  help           - Show this help message\n\n";
        echo "Usage examples:\n";
        echo "  php console migrate\n";
        echo "  php console migrate:rollback 001_create_users_tables.sql\n";
        echo "  php console migrate:status\n";
        echo "  php console migrate:fresh\n\n";
    }

    private function showInfo(string $message): void
    {
        echo "ℹ️  {$message}\n";
    }

    private function showSuccess(string $message): void
    {
        echo "✅ {$message}\n";
    }

    private function showError(string $message): void
    {
        echo "❌ Error: {$message}\n";
    }
}

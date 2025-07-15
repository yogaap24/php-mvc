<?php

namespace Yogaap\PHP\MVC\Database;

class SQLParser
{
    private string $driver;

    public function __construct(string $driver)
    {
        $this->driver = $driver;
    }

    public function parse(string $sql): string
    {
        // Replace universal placeholders with database-specific syntax
        $sql = $this->replaceDataTypes($sql);
        $sql = $this->replaceAutoIncrement($sql);
        $sql = $this->replaceConstraints($sql);
        $sql = $this->replaceFunctions($sql);
        $sql = $this->replaceIndexes($sql);

        return $sql;
    }

    private function replaceDataTypes(string $sql): string
    {
        $replacements = $this->getDataTypeReplacements();

        foreach ($replacements as $universal => $specific) {
            // Use regex to replace placeholders with proper escaping
            $pattern = '/\{\{' . preg_quote($universal, '/') . '\}\}/i';
            $sql = preg_replace($pattern, $specific, $sql);
        }

        return $sql;
    }

    private function replaceAutoIncrement(string $sql): string
    {
        switch ($this->driver) {
            case 'pgsql':
            case 'postgres':
                return str_replace('{{AUTO_INCREMENT}}', 'SERIAL', $sql);
            case 'sqlite':
                return str_replace('{{AUTO_INCREMENT}}', 'INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
            case 'mysql':
            default:
                return str_replace('{{AUTO_INCREMENT}}', 'INT AUTO_INCREMENT PRIMARY KEY', $sql);
        }
    }

    private function replaceConstraints(string $sql): string
    {
        switch ($this->driver) {
            case 'pgsql':
            case 'postgres':
                $sql = str_replace('{{UNIQUE_KEY}}', 'CONSTRAINT', $sql);
                break;
            case 'sqlite':
                $sql = str_replace('{{UNIQUE_KEY}}', 'UNIQUE', $sql);
                break;
            case 'mysql':
            default:
                $sql = str_replace('{{UNIQUE_KEY}}', 'UNIQUE KEY', $sql);
                break;
        }

        return $sql;
    }

    private function replaceFunctions(string $sql): string
    {
        switch ($this->driver) {
            case 'pgsql':
            case 'postgres':
                $sql = str_replace('{{NOW}}', 'CURRENT_TIMESTAMP', $sql);
                $sql = str_replace('{{INTERVAL_12_HOURS}}', "CURRENT_TIMESTAMP + INTERVAL '12 hours'", $sql);
                // PostgreSQL doesn't support ON UPDATE for TIMESTAMP
                $sql = preg_replace('/\s+ON UPDATE CURRENT_TIMESTAMP/i', '', $sql);
                break;
            case 'sqlite':
                $sql = str_replace('{{NOW}}', "datetime('now')", $sql);
                $sql = str_replace('{{INTERVAL_12_HOURS}}', "datetime('now', '+12 hours')", $sql);
                // SQLite doesn't support ON UPDATE for TIMESTAMP
                $sql = preg_replace('/\s+ON UPDATE CURRENT_TIMESTAMP/i', '', $sql);
                break;
            case 'mysql':
            default:
                $sql = str_replace('{{NOW}}', 'CURRENT_TIMESTAMP', $sql);
                $sql = str_replace('{{INTERVAL_12_HOURS}}', '(CURRENT_TIMESTAMP + INTERVAL 12 HOUR)', $sql);
                // MySQL supports ON UPDATE
                break;
        }

        return $sql;
    }

    private function replaceIndexes(string $sql): string
    {
        switch ($this->driver) {
            case 'mysql':
                // MySQL doesn't support IF NOT EXISTS for indexes
                // Simply remove IF NOT EXISTS from CREATE INDEX statements
                $sql = preg_replace('/CREATE INDEX IF NOT EXISTS\s+/i', 'CREATE INDEX ', $sql);
                break;
            case 'pgsql':
            case 'postgres':
            case 'sqlite':
                // PostgreSQL and SQLite support IF NOT EXISTS
                break;
        }

        return $sql;
    }

    private function getDataTypeReplacements(): array
    {
        switch ($this->driver) {
            case 'pgsql':
            case 'postgres':
                return [
                    'STRING' => 'VARCHAR',
                    'TEXT' => 'TEXT',
                    'INT' => 'INTEGER',
                    'BIGINT' => 'BIGINT',
                    'TIMESTAMP' => 'TIMESTAMP',
                    'BOOLEAN' => 'BOOLEAN',
                ];
            case 'sqlite':
                return [
                    'STRING' => 'TEXT',
                    'TEXT' => 'TEXT',
                    'INT' => 'INTEGER',
                    'BIGINT' => 'INTEGER',
                    'TIMESTAMP' => 'TIMESTAMP',
                    'BOOLEAN' => 'INTEGER',
                ];
            case 'mysql':
            default:
                return [
                    'STRING' => 'VARCHAR',
                    'TEXT' => 'TEXT',
                    'INT' => 'INT',
                    'BIGINT' => 'BIGINT',
                    'TIMESTAMP' => 'TIMESTAMP',
                    'BOOLEAN' => 'BOOLEAN',
                ];
        }
    }
}

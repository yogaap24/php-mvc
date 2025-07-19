<?php

namespace Core\Database;

class SQLParser
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function parseAndExecute(string $sql): void
    {
        $statements = $this->splitStatements($sql);

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $this->connection->exec($statement);
            }
        }
    }

    private function splitStatements(string $sql): array
    {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // Split by semicolon but not inside strings
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = null;

        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];

            if (!$inString) {
                if ($char === "'" || $char === '"') {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === ';') {
                    $statements[] = $current;
                    $current = '';
                    continue;
                }
            } else {
                if ($char === $stringChar && $sql[$i - 1] !== '\\') {
                    $inString = false;
                    $stringChar = null;
                }
            }

            $current .= $char;
        }

        if (!empty(trim($current))) {
            $statements[] = $current;
        }

        return $statements;
    }
}
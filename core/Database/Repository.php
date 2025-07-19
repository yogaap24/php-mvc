<?php

namespace Core\Database;

use PDO;

abstract class Repository
{
    protected PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    protected function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    protected function commit(): void
    {
        $this->connection->commit();
    }

    protected function rollback(): void
    {
        $this->connection->rollback();
    }

    protected function execute(string $query, array $params = []): \PDOStatement
    {
        $statement = $this->connection->prepare($query);
        $statement->execute($params);
        return $statement;
    }

    protected function fetchOne(string $query, array $params = []): ?array
    {
        $statement = $this->execute($query, $params);
        $result = $statement->fetch();
        return $result ?: null;
    }

    protected function fetchAll(string $query, array $params = []): array
    {
        $statement = $this->execute($query, $params);
        return $statement->fetchAll();
    }
}
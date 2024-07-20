<?php

namespace Yogaap\PHP\MVC\Repository;

use Yogaap\PHP\MVC\Domain\Session;

class SessionRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Session $session): Session
    {
        $statement = $this->connection->prepare("INSERT INTO sessions (id, user_id) VALUES (?, ?)");
        $statement->execute([
            $session->id,
            $session->user_id
        ]);

        return $session;
    }

    public function findSession(string $id): ?Session
    {
        $sql = 'SELECT * FROM sessions WHERE id = :id';
        $statement = $this->connection->prepare($sql);
        $statement->execute(['id' => $id]);

        try {
            $row = $statement->fetch();
            if (!$row) {
                return null;
            }

            $session = new Session();
            $session->id = $row['id'];
            $session->user_id = $row['user_id'];

            return $session;
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteSession(string $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM sessions WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function deleteAll(): void
    {
        $this->connection->exec('DELETE FROM sessions');
    }
}
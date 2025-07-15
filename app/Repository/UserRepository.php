<?php

namespace Yogaap\PHP\MVC\Repository;

use Yogaap\PHP\MVC\Domain\User;

class UserRepository
{

    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $statement = $this->connection->prepare("INSERT INTO users (id, email, password) VALUES (?, ?, ?)");
        $statement->execute([
            $user->id,
            $user->email,
            $user->password
        ]);

        return $user;
    }

    public function findUser(string $identifier): ?User
    {
        // Determine if identifier is email or ID
        $column = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'id';

        $sql = "SELECT id, email, password FROM users WHERE {$column} = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$identifier]);

        try {
            $row = $statement->fetch();
            if (!$row) {
                return null;
            }

            $user = new User();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->password = $row['password'];

            return $user;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findUserByEmail(string $email): ?User
    {
        $sql = "SELECT id, email, password FROM users WHERE email = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$email]);

        try {
            $row = $statement->fetch();
            if (!$row) {
                return null;
            }

            $user = new User();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->password = $row['password'];

            return $user;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findUserById(string $id): ?User
    {
        $sql = "SELECT id, email, password FROM users WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);

        try {
            $row = $statement->fetch();
            if (!$row) {
                return null;
            }

            $user = new User();
            $user->id = $row['id'];
            $user->email = $row['email'];
            $user->password = $row['password'];

            return $user;
        } finally {
            $statement->closeCursor();
        }
    }

    public function update(User $user, $id): User
    {
        $statement = $this->connection->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $statement->execute([
            $user->email,
            $user->password,
            $id
        ]);

        return $user;
    }


    public function deleteAll(): void
    {
        $this->connection->exec('DELETE FROM users');
    }
}

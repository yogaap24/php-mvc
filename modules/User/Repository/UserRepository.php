<?php

namespace Modules\User\Repository;

use App\Config\Database;
use Modules\User\Entity\User;

class UserRepository implements UserRepositoryInterface
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public function save(User $user): void
    {
        if ($user->getId()) {
            $this->update($user);
        } else {
            $this->insert($user);
        }
    }

    public function findById(string $id): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function delete(string $id): void
    {
        $stmt = $this->connection->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
    }

    private function insert(User $user): void
    {
        // Generate UUID if not set
        $user->generateId();

        $stmt = $this->connection->prepare(
            'INSERT INTO users (id, email, password, created_at) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $user->getId(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    private function update(User $user): void
    {
        $user->touchUpdatedAt();

        $stmt = $this->connection->prepare(
            'UPDATE users SET email = ?, password = ?, updated_at = ? WHERE id = ?'
        );
        $stmt->execute([
            $user->getEmail(),
            $user->getPassword(),
            $user->getUpdatedAt()?->format('Y-m-d H:i:s'),
            $user->getId()
        ]);
    }

    private function mapToEntity(array $data): User
    {
        $user = new User();
        $user->setId($data['id']);
        $user->setEmail($data['email']);
        $user->setPasswordHash($data['password']); // Set hash directly
        $user->setCreatedAt(new \DateTime($data['created_at']));

        if ($data['updated_at']) {
            $user->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $user;
    }
}
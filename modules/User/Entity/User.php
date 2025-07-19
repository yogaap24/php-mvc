<?php

namespace Modules\User\Entity;

class User
{
    private ?string $id = null;
    private string $email = '';
    private string $password = '';
    private \DateTime $created_at;
    private ?\DateTime $updated_at = null;
    private bool $is_active = true;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function generateId(): void
    {
        if ($this->id === null) {
            $this->id = $this->generateUuid();
        }
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = trim(strtolower($email));
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->touchUpdatedAt();
    }

    public function setPasswordHash(string $hash): void
    {
        $this->password = $hash;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function touchUpdatedAt(): void
    {
        $this->updated_at = new \DateTime();
    }

    public function getIsActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
        $this->touchUpdatedAt();
    }

    public function activate(): void
    {
        $this->setIsActive(true);
    }

    public function deactivate(): void
    {
        $this->setIsActive(false);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s')
        ];
    }

    public function toArrayWithSensitiveData(): array
    {
        return array_merge($this->toArray(), [
            'password' => $this->password
        ]);
    }

    public static function fromArray(array $data): self
    {
        $user = new self();

        if (isset($data['id'])) {
            $user->setId($data['id']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['password'])) {
            $user->setPasswordHash($data['password']);
        }

        if (isset($data['is_active'])) {
            $user->setIsActive((bool) $data['is_active']);
        }

        if (isset($data['created_at'])) {
            $user->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at']) && $data['updated_at']) {
            $user->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $user;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
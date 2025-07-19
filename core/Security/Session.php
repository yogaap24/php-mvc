<?php

namespace Core\Security;

class Session
{
    private string $id;
    private string $user_id;
    private \DateTime $created_at;
    private \DateTime $expires_at;
    private array $data = [];

    public function __construct(string $id = '', string $user_id = '')
    {
        $this->id = $id ?: $this->generateSessionId();
        $this->user_id = $user_id;
        $this->created_at = new \DateTime();
        $this->expires_at = new \DateTime('+12 hours'); // Default 12 hours
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getExpiresAt(): \DateTime
    {
        return $this->expires_at;
    }

    public function setExpiresAt(\DateTime $expires_at): void
    {
        $this->expires_at = $expires_at;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function addData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function removeData(string $key): void
    {
        unset($this->data[$key]);
    }

    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function getDataValue(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function isExpired(): bool
    {
        return new \DateTime() > $this->expires_at;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !empty($this->id) && !empty($this->user_id);
    }

    public function extend(int $hours = 12): void
    {
        $this->expires_at = new \DateTime("+{$hours} hours");
    }

    public function refresh(): void
    {
        $this->id = $this->generateSessionId();
        $this->created_at = new \DateTime();
        $this->extend();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'expires_at' => $this->expires_at->format('Y-m-d H:i:s'),
            'data' => $this->data
        ];
    }

    public static function fromArray(array $data): self
    {
        $session = new self($data['id'] ?? '', $data['user_id'] ?? '');

        if (isset($data['created_at'])) {
            $session->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['expires_at'])) {
            $session->setExpiresAt(new \DateTime($data['expires_at']));
        }

        if (isset($data['data']) && is_array($data['data'])) {
            $session->setData($data['data']);
        }

        return $session;
    }

    private function generateSessionId(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function __toString(): string
    {
        return $this->id;
    }
}